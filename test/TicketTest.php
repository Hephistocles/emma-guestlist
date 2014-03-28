<?php
require_once dirname(__FILE__) . '/../src/Ticket.php';

class TicketTest extends PHPUnit_Framework_TestCase {
 	
    function testCanCreateATicketFromParams() {
        $ticket = new Ticket(50, 2, 5, "Finbarr Leacy");
        $this->assertEquals($ticket->booking_id, 50);
        $this->assertEquals($ticket->ticket_num, 5);
        $this->assertEquals($ticket->ticket_type, 2);
        $this->assertEquals($ticket->guest_name, "Finbarr Leacy");
    }
    function testCanCreateTicketFromDB() {
    	// TODO this test is not portable!
		$ticket = new Ticket(50, 2, 5);
        $this->assertEquals($ticket->booking_id, 50);
        $this->assertEquals($ticket->ticket_num, 5);
        $this->assertEquals($ticket->ticket_type, 2);
        $this->assertEquals($ticket->guest_name, "Finbarr Leacy");
    }
 
    function testCanCreateTicketFromCode() {
    	// TODO this test is not portable!
		$ticket = new Ticket("eje14-50-2-5");
        $this->assertEquals($ticket->booking_id, 50);
        $this->assertEquals($ticket->ticket_num, 5);
        $this->assertEquals($ticket->ticket_type, 2);
        $this->assertEquals($ticket->guest_name, "Finbarr Leacy");
    }
 
}
?>