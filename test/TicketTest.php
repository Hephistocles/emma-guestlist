<?php
require_once dirname(__FILE__) . '/../src/Ticket.php';
require_once "../credentials.php"; 
require_once dirname(__FILE__) . '/../vendor/dinesh/barcode/src/Dinesh/Barcode/QRcode.php';
require_once dirname(__FILE__) . '/../vendor/dinesh/barcode/src/Dinesh/Barcode/DNS2D.php';

class TicketTest extends PHPUnit_Framework_TestCase {
 	
    function testCanCreateATicketFromParams() {
        $ticket = new Ticket(50, 2, 5, "Finbarr Leacy");
        $this->assertEquals($ticket->booking_id, 50);
        $this->assertEquals($ticket->ticket_num, 5);
        $this->assertEquals($ticket->ticket_type, 2);
        $this->assertEquals($ticket->guest_name, "Finbarr Leacy");
    }
 
    function testCanCreateTicketFromCode() {
    	// TODO this test is not portable!
        global $dbhost,$dbuser,$dbpass,$dbname;
        $conn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
		$ticket = new Ticket("eje14-50-2-5", $conn);
        $this->assertEquals($ticket->booking_id, 50);
        $this->assertEquals($ticket->ticket_num, 5);
        $this->assertEquals($ticket->ticket_type, 2);
        $this->assertEquals($ticket->guest_name, "Finbarr Leacy");
    }

    function testTicketEncode() {
    	$code = "eje14-50-2-5";
        global $dbhost,$dbuser,$dbpass,$dbname;
        $conn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
    	$ticket = new Ticket($code, $conn);
    	$this->assertEquals($code, $ticket->encode());
    }
    function testTicketSerialise() {
        $code = "eje14-50-2-5";
        global $dbhost,$dbuser,$dbpass,$dbname;
        $conn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
        $ticket = new Ticket($code, $conn);
    	$this->assertEquals('O:6:"Ticket":4:{s:10:"booking_id";i:50;s:10:"ticket_num";i:5;s:11:"ticket_type";i:2;s:10:"guest_name";s:13:"Finbarr Leacy";}', serialize($ticket));
    }
    function testTicketBarcode() {
    	try {

            $code = "eje14-50-2-5";
            global $dbhost,$dbuser,$dbpass,$dbname;
            $conn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
            $ticket = new Ticket($code, $conn);
	    	$code = serialize($ticket);
	    	$drawer = new Dinesh\Barcode\DNS2D();
	    	$drawer->setStorPath("/home/christoph/dev/guestlist/test/output/");
            // NOTE: Smaller QR codes are faster to scan, so serializing a PHP object is probs not the best way forward
	    	$path = $drawer->getBarcodePNGPath($code, "QRCODE", 50, 50, $ticket->encode());
	    } catch (Exception $e) {
	    	$this->fail($e->getMessage());
	    }
    	// TODO: actually test the result. All I know here is that no errors have been thrown!
    }
 
}
?>