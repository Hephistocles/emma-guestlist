<?php
class WPEMImporter {

	private $fromConn;
	private $toConn;
	function __construct($from, $to) {
		$this->fromConn = $from;
		$this->toConn = $to;
	}
	public function import_tickets($config) {
		$prefix = $config["prefix"];
	}

	public function import_bookers($config) {
		$prefix = $config["prefix"];
		$event_id = $config["event_id"];
		// TODO: parameterise table names
		// TODO: This relies on from and to being the same...
		$sql = "INSERT INTO cl_bookers (booker_name, booker_email, booker_wp_user_id) 
		SELECT display_name AS booker_name, user_email AS booker_email, ID AS booker_wp_user_id
		FROM (
			SELECT DISTINCT person_id
			FROM  " . $prefix ."em_bookings 
			WHERE event_id=$event_id
		) AS d
		JOIN " . $prefix ."users ON ID = person_id";

		$this->fromConn->query($sql);
	}
	public function import_ticket_types($config) {
		$prefix = $config["prefix"];
		$event_id = $config["event_id"];
		// TODO: parameterise table names
		// TODO: This relies on from and to being the same...
		$sql = "INSERT INTO cl_ticket_types( ticket_name, ticket_price ) (
			SELECT ticket_name, ticket_price
			FROM  " . $prefix . "em_tickets`
			WHERE event_id =$event_id
		)";

		$this->fromConn->query($sql);
	}

	public function map_status_code($old_code) {
		switch ($old_code) {
			case 1:
				return 1; // Pending
			case 2:
				return 2; // Confirmed
			case 3:
				return 3; // Cancelled
			case 4:
				return 4; // In attendance
			default:
				return -1;
		}
	}
}
?>