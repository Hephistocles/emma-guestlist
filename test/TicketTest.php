<?php
require_once dirname(__FILE__) . '/../src/Ticket.php';

class TicketTest extends PHPUnit_Framework_TestCase {
 	
    function testCanCreateATicketFromParams() {
        $ticket = new Ticket(17, 1, 1, "Eve Edwards");
        $this->assertEquals($ticket->booking_id, 17);
        $this->assertEquals($ticket->ticket_id, 1);
        $this->assertEquals($ticket->ticket_type, 1);
        $this->assertEquals($ticket->guest_name, "Eve Edwards");
    }
    function testCanCreateTicketFromDB() {
		$ticket = new Ticket(17, 1);
        $this->assertEquals($ticket->booking_id, 17);
        $this->assertEquals($ticket->ticket_id, 1);
        $this->assertEquals($ticket->ticket_type, 1);
        $this->assertEquals($ticket->guest_name, "Eve Edwards");
    }
 
    function testCanCreateTicketFromCode() {
		$ticket = new Ticket("eje14-17-1");
        $this->assertEquals($ticket->booking_id, 17);
        $this->assertEquals($ticket->ticket_id, 1);
        $this->assertEquals($ticket->ticket_type, 1);
        $this->assertEquals($ticket->guest_name, "Eve Edwards");
    }
 
}
?>