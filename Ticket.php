<?php
class Ticket {
	const CODE_PREFIX = "eje14";
	const CODE_SEPARATOR = "-";

	public $booking_id;
	public $ticket_id;
	// public $guest_name;
	// public $ticket_type;

	function __construct($code) {
		$i = func_num_args();
		$a = func_get_args();

		switch ($i) {
			case 1:
				$components = explode(Ticket::CODE_SEPARATOR, $a[0]);
				// discard prefix - components[0]
				$this->booking_id = $components[1];
				$this->ticket_id = $components[2];
				break;
			case 2:
				$this->booking_id = $a[0];
				$this->ticket_id = $a[1];
				break;
			// TODO: provide a constructor for additional data to save a DB query
			default:
				// TODO: break nicely
				break;
		}

		$this->lookup_data();
	}

	private function lookup_data() {
		// TODO: lookup remaining ticket data
	}

	public function encode() {
		return implode(Ticket::CODE_SEPARATOR, array(
				Ticket::CODE_PREFIX,
				$this->booking_id,
				$this->ticket_id,
			));
	}
}
?>