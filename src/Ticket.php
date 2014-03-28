<?php
require_once dirname(__FILE__) . '/../src/DBConnector.php';
class Ticket {
	const CODE_PREFIX = "eje14";
	const CODE_SEPARATOR = "-";

	public $booking_id;
	public $ticket_num;
	public $ticket_type;
	public $guest_name;

	/**
	 * Accepts one, two or four arguments to create a new Ticket instance. If one or three args are passed, the rest are automatically inferred from the database
	 * @param [int/string] $code_or_id If only one argument is passed, it should be the ticket code (e.g. "eje14-17-1"). If multiple arguments are passed, the first should be the booking ID
	 * @param int $ticket_type The ticket type (corresponds to WP Booking Manager's ticket type codes)
	 * @param int $ticket_num The ticket id within the booking
	 * @param string guest_name The name of the guest
	 */
	function __construct() {
		$i = func_num_args();
		$a = func_get_args();

		switch ($i) {
			case 1:
				$components = explode(Ticket::CODE_SEPARATOR, $a[0]);
				// discard prefix - components[0]
				$this->booking_id = $components[1];
				$this->ticket_type = $components[2];
				$this->ticket_num = $components[3];
				$this->lookup_data();
				break;
			case 3:
				$this->booking_id = $a[0];
				$this->ticket_type = $a[1];
				$this->ticket_num = $a[2];
				$this->lookup_data();
				break;
			case 4:
				$this->booking_id = $a[0];
				$this->ticket_type = $a[1];
				$this->ticket_num = $a[2];
				$this->guest_name = $a[3];
				break;
			default:
				throw new InvalidArgumentException("Requires 1, 2 or 4 arguments (see doc)");
				break;
		}
	}

	private function lookup_data() {
		$conn = new DBConnector();
		if ($this->booking_id == null || $this->ticket_num == null) {
			throw new Exception("Ticket not sufficiently initialised to infer remaining data");
		}

		// get ticket's guest name
		$array = $conn->get_array("SELECT booking_meta FROM eje2014_em_bookings WHERE booking_id=" . $this->booking_id);
		if (count($array) != 1) {
			throw new Exception("Expected 1 row with booking_id=" . $this->booking_id . " but found " . count($array) );
		} else {
			// a:2:{s:9:"attendees";a:1:{i:1;a:1:{i:0;a:3:{s:13:"attendee_name";s:13:"Jakub Telicka";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:12:"College Bill";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}
			$meta = unserialize($array[0]["booking_meta"]);
			$tickets = $meta['attendees'];
			$this->guest_name = $tickets[$this->ticket_type][$this->ticket_num]["attendee_name"];
		}
	}

	// public function encode() {
	// 	return implode(Ticket::CODE_SEPARATOR, array(
	// 			Ticket::CODE_PREFIX,
	// 			$this->booking_id,
	// 			$this->ticket_id,
	// 		));
	// }
}
?>