<html>
<head>
<title>Bookings</title>
</head>
<body>
<?php 

echo phpinfo();

 ?>
<h1>Bookings: </h1>

New Content?
<?php
	$result_cache = array();
	if (isset($_GET['d'])) {
		include "credentials.php"; 
		$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname, 3306);
		// $con=mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
			
		if ($connection->connect_errno > 0) {
		    die ('Unable to connect to database [' . $connection->connect_error . ']');
		}



		$d = strval(intval($_GET['d']));
		$sql = "SELECT * FROM eje2014_em_bookings LIMIT 0 , " . $d;
		if (!$result = $connection->query($sql)) {
		    die ('There was an error running query[' . $connection->error . ']');
		}
		while ($row = $result->fetch_array()) {
			$result_cache[] = $row;
		}
		echo "<h1> NEW DATA: </h1><pre>";
		echo serialize($result_cache);
		echo "</pre><h1> END NEW DATA</h1>";
	} else {
		$result_cache = unserialize('a:5:{i:0;a:22:{i:0;s:1:"2";s:10:"booking_id";s:1:"2";i:1;s:1:"0";s:8:"event_id";s:1:"0";i:2;s:1:"9";s:9:"person_id";s:1:"9";i:3;s:1:"7";s:14:"booking_spaces";s:1:"7";i:4;s:0:"";s:15:"booking_comment";s:0:"";i:5;s:19:"2014-02-08 15:00:19";s:12:"booking_date";s:19:"2014-02-08 15:00:19";i:6;s:1:"0";s:14:"booking_status";s:1:"0";i:7;s:6:"576.00";s:13:"booking_price";s:6:"576.00";i:8;s:4:"0.00";s:16:"booking_tax_rate";s:4:"0.00";i:9;s:4:"0.00";s:13:"booking_taxes";s:4:"0.00";i:10;s:100:"a:2:{s:7:"booking";a:0:{}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";s:12:"booking_meta";s:100:"a:2:{s:7:"booking";a:0:{}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";}i:1;a:22:{i:0;s:1:"4";s:10:"booking_id";s:1:"4";i:1;s:1:"0";s:8:"event_id";s:1:"0";i:2;s:1:"9";s:9:"person_id";s:1:"9";i:3;s:1:"1";s:14:"booking_spaces";s:1:"1";i:4;s:0:"";s:15:"booking_comment";s:0:"";i:5;s:19:"2014-02-08 15:07:01";s:12:"booking_date";s:19:"2014-02-08 15:07:01";i:6;s:1:"0";s:14:"booking_status";s:1:"0";i:7;s:5:"90.00";s:13:"booking_price";s:5:"90.00";i:8;s:4:"0.00";s:16:"booking_tax_rate";s:4:"0.00";i:9;s:4:"0.00";s:13:"booking_taxes";s:4:"0.00";i:10;s:100:"a:2:{s:7:"booking";a:0:{}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";s:12:"booking_meta";s:100:"a:2:{s:7:"booking";a:0:{}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";}i:2;a:22:{i:0;s:2:"15";s:10:"booking_id";s:2:"15";i:1;s:1:"1";s:8:"event_id";s:1:"1";i:2;s:2:"72";s:9:"person_id";s:2:"72";i:3;s:1:"1";s:14:"booking_spaces";s:1:"1";i:4;s:0:"";s:15:"booking_comment";s:0:"";i:5;s:19:"2014-02-13 23:01:29";s:12:"booking_date";s:19:"2014-02-13 23:01:29";i:6;s:1:"0";s:14:"booking_status";s:1:"0";i:7;s:5:"88.00";s:13:"booking_price";s:5:"88.00";i:8;s:4:"0.00";s:16:"booking_tax_rate";s:4:"0.00";i:9;s:4:"0.00";s:13:"booking_taxes";s:4:"0.00";i:10;s:241:"a:2:{s:9:"attendees";a:1:{i:2;a:1:{i:0;a:3:{s:13:"attendee_name";s:14:"Venetia D\'Arcy";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:12:"College Bill";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";s:12:"booking_meta";s:241:"a:2:{s:9:"attendees";a:1:{i:2;a:1:{i:0;a:3:{s:13:"attendee_name";s:14:"Venetia D\'Arcy";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:12:"College Bill";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";}i:3;a:22:{i:0;s:2:"16";s:10:"booking_id";s:2:"16";i:1;s:1:"1";s:8:"event_id";s:1:"1";i:2;s:2:"42";s:9:"person_id";s:2:"42";i:3;s:1:"1";s:14:"booking_spaces";s:1:"1";i:4;s:0:"";s:15:"booking_comment";s:0:"";i:5;s:19:"2014-02-13 23:01:35";s:12:"booking_date";s:19:"2014-02-13 23:01:35";i:6;s:1:"0";s:14:"booking_status";s:1:"0";i:7;s:5:"80.00";s:13:"booking_price";s:5:"80.00";i:8;s:4:"0.00";s:16:"booking_tax_rate";s:4:"0.00";i:9;s:4:"0.00";s:13:"booking_taxes";s:4:"0.00";i:10;s:246:"a:2:{s:9:"attendees";a:1:{i:1;a:1:{i:0;a:3:{s:13:"attendee_name";s:18:"Christopher Murkin";s:17:"attendee_donation";s:3:"Yes";s:14:"payment_method";s:12:"College Bill";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";s:12:"booking_meta";s:246:"a:2:{s:9:"attendees";a:1:{i:1;a:1:{i:0;a:3:{s:13:"attendee_name";s:18:"Christopher Murkin";s:17:"attendee_donation";s:3:"Yes";s:14:"payment_method";s:12:"College Bill";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";}i:4;a:22:{i:0;s:2:"17";s:10:"booking_id";s:2:"17";i:1;s:1:"1";s:8:"event_id";s:1:"1";i:2;s:2:"44";s:9:"person_id";s:2:"44";i:3;s:1:"3";s:14:"booking_spaces";s:1:"3";i:4;s:0:"";s:15:"booking_comment";s:0:"";i:5;s:19:"2014-02-13 23:01:41";s:12:"booking_date";s:19:"2014-02-13 23:01:41";i:6;s:1:"0";s:14:"booking_status";s:1:"0";i:7;s:6:"244.00";s:13:"booking_price";s:6:"244.00";i:8;s:4:"0.00";s:16:"booking_tax_rate";s:4:"0.00";i:9;s:4:"0.00";s:13:"booking_taxes";s:4:"0.00";i:10;s:498:"a:2:{s:9:"attendees";a:2:{i:2;a:1:{i:0;a:3:{s:13:"attendee_name";s:11:"Eve Edwards";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:12:"College Bill";}}i:1;a:2:{i:0;a:3:{s:13:"attendee_name";s:18:"Katya Adam Pandian";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:6:"Cheque";}i:1;a:3:{s:13:"attendee_name";s:16:"William Truscott";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:6:"Cheque";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";s:12:"booking_meta";s:498:"a:2:{s:9:"attendees";a:2:{i:2;a:1:{i:0;a:3:{s:13:"attendee_name";s:11:"Eve Edwards";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:12:"College Bill";}}i:1;a:2:{i:0;a:3:{s:13:"attendee_name";s:18:"Katya Adam Pandian";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:6:"Cheque";}i:1;a:3:{s:13:"attendee_name";s:16:"William Truscott";s:17:"attendee_donation";s:2:"No";s:14:"payment_method";s:6:"Cheque";}}}s:12:"registration";a:2:{s:9:"user_name";s:0:"";s:10:"user_email";s:0:"";}}";}}');
	}
?>
<pre>
<?php
	$tickets = [];
	include_once "Ticket.php"; 
	for ($i = 0; $i<count($result_cache); $i++) {
		$booking = $result_cache[$i];
		$meta = unserialize($booking['booking_meta']);
		if (isset($meta['attendees'])) {
			$attendees = $meta['attendees'];
			for ($j = 0; $j<count($attendees); $j++) {
				$ticket = new Ticket($booking["booking_id"], $j);
				echo $ticket->encode() . "\n";
				$tickets[] = $ticket;
			}
		} else {
			// echo "No Attendees\n"; 
			// print_r($meta);
		}
	}
?>
</pre>
</body>

</html>