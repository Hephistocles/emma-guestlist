<?php
class WPEMImporter {

	private $fromConn;
	private $toConn;
	private $personMap = array();
	private $map = array();

	function __construct($from, $to) {
		$this->fromConn = $from;
		$this->toConn = $to;
	}

	/**
	 * Inserts multiple records into $table in toConn. Iterates over every row in $result, calling $value_callback for each to prepare the value.
	 * Also prepares a mapping array, from a given key (selected from the result array or the individual values by key_callback) to the auto incremented ID
	 */
	public function insertMany($table, $result, $value_callback, $key_callback) {
		$map = array();

		// first prepare an array of values to insert
		while ($array = $result->fetch_array()) {
			$new_values = call_user_func($value_callback, $array);

			for ($nv = 0; $nv<count($new_values); $nv++) {
				foreach ($new_values[$nv] as $i=>$value) {
					if (gettype($value) == 'string')
						$new_values[$nv][$i] = "'$value'";
				}
				$sql = "INSERT INTO $table VALUES (" . implode(", ", $new_values[$nv]) . ")";
				$this->toConn->multi_query($sql);
				$key = call_user_func($key_callback, $new_values[$nv], $array);
				if ($this->toConn->insert_id() > 0) 
					$map[$key] = $this->toConn->insert_id();
			}
		}
		return $map;
	}

	public function import_tickets($config) {
		$prefix = $config["prefix"];
		$event_id = $config["event_id"];

		if (!isset($this->map['bookers']))
			throw new Exception("Importer must import bookers before tickets", 1);
		if (!isset($this->map['ticket_types']))
			throw new Exception("Importer must import ticket types before tickets", 1);
		if (!isset($this->map['payment_methods']))
			throw new Exception("Importer must import ticket payment methods before tickets", 1);
		
		$exportSql = "SELECT person_id, booking_id, booking_date, booking_status, booking_meta FROM {$prefix}em_bookings WHERE event_id=$event_id";

		$result = $this->fromConn->query($exportSql);
		$this->insertMany("cl_guestlist (guest_name, status_id, booker_id, ticket_type_id, booking_date, payment_method_id, ticket_meta)", $result, function($array) {
			$meta = @unserialize($array['booking_meta']);
			if ($meta === null || $meta == false) {
				throw new Exception("Decoding error", 1);
			}
			$tickets = array();
			foreach ($meta['attendees'] as $ticket_type=>$attendees) {
				foreach ($attendees as $attendee) {
					$donation_array = array();

					// add donation to meta for now, and remove it in a second pass in a moment
					if (strcasecmp($attendee['attendee_donation'], 'Yes') == 0)  {
						$donation_array['addon'] = array();
						$donation_array['addon']['donation'] = 2;
					}
					$tickets[] = array($this->toConn->escape($attendee['attendee_name']),
						$this->map_status_code($array['booking_status']),
						$this->map['bookers'][$array['person_id']],
						$this->map['ticket_types'][$ticket_type],
						$this->toConn->escape($array['booking_date']),
						$this->map['payment_methods'][$attendee['payment_method']],
						serialize($donation_array) 
					);
				}
			}
			return $tickets;
		}, function ($new_values, $array) {
		});

		//	go through the new tickets and add add-ons for the donations
		$ticketSql = "SELECT ticket_id, ticket_meta FROM cl_guestlist WHERE ticket_meta<>'a:0:{}'";
		$result = $this->toConn->query($ticketSql);
		$this->insertMany("cl_ticket_addons (ticket_id, addon_name, addon_price)", $result, function ($array) {
			$addons = array();

			$meta = unserialize($array['ticket_meta']);
			if (isset($meta['addon'])) {
				foreach ($meta['addon'] as $addon_name => $value) {
					$addons[] = array($array['ticket_id'], $addon_name, $value);
				}
				unset($meta['addon']);
				$this->toConn->query("UPDATE cl_guestlist SET ticket_meta='" . 
					$this->toConn->escape(serialize($meta)) . "' WHERE ticket_id=" . $array['ticket_id']);
			}
			return $addons;
		}, function ($new_values, $array) {});

		// $this->toConn->query("INSERT INTO cl_guestlist (guest_name, status_id, booker_id, ticket_type_id, booking_date, ticket_meta)
		// 	SELECT guest_name, status_id, booker_id, ticket_type_id, booking_date, ticket_meta FROM 
		// 	unlinked_guestlist JOIN cl_bookers ON cl_bookers.booker_email=unlinked_guestlist.booker_email
		// 		JOIN cl_ticket_types ON orig_ticket_type_id=cl_ticket_types.meta");
		// $this->toConn->query("DROP TABLE unlinked_guestlist;");
	}

	public function import_bookers($config) {
		$prefix = $config["prefix"];
		$event_id = $config["event_id"];
		$exportSql = "SELECT display_name AS booker_name, user_email AS booker_email, ID AS booker_wp_user_id
		FROM (
			SELECT DISTINCT person_id
			FROM {$prefix}em_bookings 
			WHERE event_id=$event_id
		) AS d
		JOIN {$prefix}users ON ID = person_id";

		$result = $this->fromConn->query($exportSql);
		// TODO: parameterise table name?
		$this->map['bookers'] = $this->insertMany("cl_bookers (booker_name, booker_email, booker_wp_user_id)", $result, function($array) {
			return array(array($this->toConn->escape($array['booker_name']),
				$this->toConn->escape($array['booker_email']), 
				$this->toConn->escape($array['booker_wp_user_id'])));
		}, function ($new_values, $array) {
			return $array['booker_wp_user_id'];
		});
	}

	public function import_ticket_types($config) {
		$prefix = $config["prefix"];
		$event_id = $config["event_id"];

		$exportSql = "SELECT ticket_name, ticket_price, ticket_id FROM  " . $prefix . "em_tickets WHERE event_id =$event_id";
	
		$result = $this->fromConn->query($exportSql);

		// TODO: parameterise table name?
		$this->map['ticket_types'] = $this->insertMany("cl_ticket_types (ticket_name, ticket_price)", $result, function($array) {
			return array(array(
				$this->toConn->escape($array['ticket_name']),
				$this->toConn->escape($array['ticket_price'])
			));
		}, function ($new_values, $array) {
			return $array['ticket_id'];
		});
	}
	public function import_payment_methods($config) {
		$prefix = $config["prefix"];
		$event_id = $config["event_id"];
		// TODO: parameterise table names
		$exportsql = "SELECT DISTINCT transaction_gateway FROM  " . $prefix . "em_transactions JOIN " . $prefix . "em_bookings 
			ON " . $prefix . "em_bookings.booking_id=" . $prefix . "em_transactions.booking_id WHERE event_id=" . $event_id .
			" UNION SELECT 'Cheque'"; // Slightly hacky, but it's not technically in the payment methods list of WPEM
		$result = $this->fromConn->query($exportsql);

		$this->map['payment_methods'] = $this->insertMany("cl_payment_methods (method_name)", $result, function($array) {
			return array(array(
				$array['transaction_gateway']
			));
		}, function ($new_values, $array) {
			return $array['transaction_gateway'];
		});
	}

	public function map_status_code($old_code) {
		switch ($old_code) {
			case 0: // Pending
				return 1; // Pending
			case 1: // Approved
				return 2; // Confirmed
			case 2: // Rejected
				return 3; // Cancelled
			case 3: // Cancelled
				return 3; // Cancelled
			case 4: // Awaiting Online Payment
				return 1; // Pending
			case 5: // Awaiting Payment
				return 1; // Pending
			default:
				return -1;
		}
	}
}
?>