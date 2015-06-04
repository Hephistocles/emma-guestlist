<?php
require_once "../credentials.php"; 
require_once dirname(__FILE__) . '/../src/WPEMImporter.php';

class WPEMImporterTest extends PHPUnit_Framework_TestCase {

	public static $importer;
	public static $conn;

	public static function setupBeforeClass() {
		global $dbhost,$dbuser,$dbpass,$dbname;
		self::$conn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
		self::$conn->exec("../src/setup.sql");
		self::$conn->exec("../src/defaults.sql");
		self::$importer = new WPEMImporter(self::$conn, self::$conn);
	}

	public static function tearDownAfterClass() {
		// self::$conn->exec("../src/teardown.sql");
	}

	function testBookerImport() {
		self::$importer->import_bookers(array("event_id"=>1, "prefix"=>"eje2014_"));

		$original_total = self::$conn->get_array( "SELECT COUNT(*) FROM (SELECT DISTINCT person_id FROM  eje2014_em_bookings WHERE event_id=1) AS people" )[0][0];
		// can't test per-event because we don't support multiple events yet.
		$new_total = self::$conn->get_array( "SELECT COUNT(*) FROM cl_bookers")[0][0];
			
		$this->assertEquals($original_total, $new_total);

	}
	function testTicketTypeImport() {
		self::$importer->import_ticket_types(array("event_id"=>1, "prefix"=>"eje2014_"));

		$original_total = self::$conn->get_array( "SELECT COUNT(*), SUM(ticket_price) FROM eje2014_em_tickets WHERE event_id=1" )[0];
		// can't test per-event because we don't support multiple events yet.
		$new_total = self::$conn->get_array( "SELECT COUNT(*), SUM(ticket_price) FROM cl_ticket_types")[0];
			
		$this->assertEquals(($original_total[0]===null)?0:$original_total[0], $new_total[0]);
		$this->assertEquals(($original_total[1]===null)?0:$original_total[1], $new_total[1]);
	}
	function testPaymentMethodImport() {
		self::$importer->import_payment_methods(array("event_id"=>1, "prefix"=>"eje2014_"));

		// hacky +1 because I know the cheques aren't included in the WPEM table
		$original_total = self::$conn->get_array( "SELECT COUNT(DISTINCT transaction_gateway)+1 FROM eje2014_em_transactions" )[0];
		// can't test per-event because we don't support multiple events yet.
		$new_total = self::$conn->get_array( "SELECT COUNT(*) FROM cl_payment_methods")[0];
			
		$this->assertEquals($original_total[0], $new_total[0]);
	}
	/**
	 * @depends testTicketTypeImport
	 * @depends testBookerImport
	 * @depends testPaymentMethodImport
	 */
	function testTicketImport() {
		self::$importer->import_tickets(array("event_id"=>1, "prefix"=>"eje2014_"));
		$orig_query = "SELECT SUM(booking_spaces) AS totalspaces, SUM(booking_price) AS totalprice FROM eje2014_em_bookings WHERE event_id=1";
		$new_query = "SELECT COUNT( * ) AS totalspaces, SUM( ticket_price + IFNULL( addon, 0 ) ) AS totalprice
			FROM cl_guestlist
			JOIN cl_ticket_types ON cl_guestlist.ticket_type_id = cl_ticket_types.ticket_type_id
			LEFT JOIN (
				SELECT ticket_id, SUM( addon_price ) AS addon
				FROM cl_ticket_addons
				GROUP BY cl_ticket_addons.ticket_id
			) AS addons ON cl_guestlist.ticket_id = addons.ticket_id
		";
		
		// testing by booking status

		// checking against bookings table
		$original_sums = self::$conn->get_array( $orig_query. " AND (booking_status=0 OR booking_status=4 OR booking_status=5)" );
		// checking against tickets_bookings table
		$new_sums = self::$conn->get_array( $new_query. " WHERE status_id=1" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND booking_status=1" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE status_id=2" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);
		
		$original_sums = self::$conn->get_array( $orig_query. " AND (booking_status=2 OR booking_status=3)" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE status_id=3" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		// $original_sums = self::$conn->get_array( $orig_query. " AND booking_status=4" );
		// $new_sums = self::$conn->get_array( $new_query. " WHERE status_id=4" );
		// $this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
		// 	$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		// $this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
		// 	$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query);
		$new_sums = self::$conn->get_array( $new_query );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		// testing by ticket type
		$orig_query = "SELECT SUM( ticket_booking_spaces ) AS totalspaces, SUM( ticket_booking_price ) AS totalprice
			FROM  eje2014_em_tickets_bookings
			JOIN eje2014_em_bookings ON eje2014_em_bookings.booking_id = eje2014_em_tickets_bookings.booking_id
			WHERE event_id =1";
		$new_query = "SELECT COUNT( * ) AS totalspaces, SUM(ticket_price) AS totalprice
			FROM cl_guestlist
			JOIN cl_ticket_types ON cl_guestlist.ticket_type_id = cl_ticket_types.ticket_type_id";

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=1" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=1" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=2" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=2" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=14" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=3" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=15" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=4" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=16" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=5" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=17" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=6" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=18" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=7" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=19" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=8" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=20" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=9" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=21" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=10" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=22" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=11" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=23" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=12" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query. " AND ticket_id=24" );
		$new_sums = self::$conn->get_array( $new_query. " WHERE cl_ticket_types.ticket_type_id=13" );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		$original_sums = self::$conn->get_array( $orig_query );
		$new_sums = self::$conn->get_array( $new_query );
		$this->assertEquals($original_sums[0]["totalspaces"]===null?0:$original_sums[0]["totalspaces"], 
			$new_sums[0]["totalspaces"]===null?0:$new_sums[0]["totalspaces"]);
		$this->assertEquals($original_sums[0]["totalprice"]===null?0:$original_sums[0]["totalprice"], 
			$new_sums[0]["totalprice"]===null?0:$new_sums[0]["totalprice"]);

		
	}
}
?>