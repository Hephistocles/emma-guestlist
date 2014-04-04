<?php
class WPEMImporter {

	private $fromConn;
	private $toConn;

	function __construct($from, $to) {
		$this->fromConn = $from;
		$this->toConn = $to;
	}
	public function import_tickets($config) {
		
	}
	public function map_status_code($old_code) {
		
	}
}
?>