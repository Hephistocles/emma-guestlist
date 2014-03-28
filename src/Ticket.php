<?php
class Ticket {
	const CODE_PREFIX = "eje14";
	const CODE_SEPARATOR = "-";

	public $booking_id;
	public $ticket_id;
	public $guest_name;
	public $ticket_type;

	/**
	 * Accepts one, two or four arguments to create a new Ticket instance. If one or two args are passed, the rest are automatically inferred from the database
	 * @param [int/string] $code_or_id If only one argument is passed, it should be the ticket code (e.g. "eje14-17-1"). If multiple arguments are passed, the first should be the booking ID
	 * @param int $ticket_id The ticket id within the booking
	 * @param int $ticket_type The ticket type (corresponds to WP Booking Manager's ticket type codes)
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
				$this->ticket_id = $components[2];
				$this->lookup_data();
				break;
			case 2:
				$this->booking_id = $a[0];
				$this->ticket_id = $a[1];
				$this->lookup_data();
				break;
			case 4:
				$this->booking_id = $a[0];
				$this->ticket_id = $a[1];
				$this->ticket_type = $a[2];
				$this->guest_name = $a[3];
				break;
			default:
				throw new InvalidArgumentException("Requires 1, 2 or 4 arguments (see doc)");
				break;
		}
	}

	private function lookup_data() {
		// TODO: lookup remaining ticket data
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