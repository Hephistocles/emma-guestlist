<?php
require_once "../credentials.php"; 
require_once dirname(__FILE__) . '/../src/WPEMImporter.php';

class WPEMImporterTest extends PHPUnit_Framework_TestCase {
	function testImport() {
		$conn;
		try {
			global $dbhost,$dbuser,$dbpass,$dbname;
			$fromConn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
		} catch (Exception $error) {
			$this->fail("DB Error: " . $error->getMessage());
			return;
		}
		$toConn = $fromConn;
		$importer = new WPEMImporter($fromConn, $toConn);
		$importer->import_tickets(array("event_id"=>1));
		$orig_query = "SELECT SUM(booking_spaces) AS totalspaces, SUM(booking_price) AS totalprice FROM eje2014_em_bookings WHERE event_id=1";
		$new_query = "SELECT COUNT(*) AS totalspaces, SUM(ticket_price) AS totalprice FROM cl_guestlist JOIN cl_ticket_types ON cl_guestlist.ticket_type_id=cl_ticket_types.ticket_type_id";
		foreach (array(-1, 0,1,3) as $status) {
			$original_sums = $fromConn->get_array( $orig_query. ($status>0?"AND booking_status=$status":"") );
			
			$new_status = $status>0?$importer->map_status_code($status):-1;
			$new_sums = $fromConn->get_array( $new_query. ($status>0?"WHERE status_id=$new_status":"") );

			$this->assertEquals($original_sums[0]["totalprice"], $new_sums[0]["totalprice"]);
			$this->assertEquals($original_sums[0]["totalspaces"], $new_sums[0]["totalspaces"]);
		}

		$orig_query = "SELECT SUM(ticket_booking_spaces) AS totalspaces, SUM(booking_price) AS totalprice FROM eje2014_em_tickets_bookings JOIN eje2014_em_bookings 
			ON eje2014_em_tickets_bookings.booking_id=eje2014_em_bookings.booking_id WHERE event_id=1";
		foreach (array(1,2) as $type) {
			$original_sums = $fromConn->get_array( $orig_query. ($status>0?"AND booking_status=$status":"") );
			
			$new_status = $status>0?$importer->map_status_code($status):-1;
			$new_sums = $fromConn->get_array( $new_query. ($status>0?"WHERE status_id=$new_status":"") );

			$this->assertEquals($original_sums[0]["totalprice"], $new_sums[0]["totalprice"]);
			$this->assertEquals($original_sums[0]["totalspaces"], $new_sums[0]["totalspaces"]);
		}
	}
}
?>