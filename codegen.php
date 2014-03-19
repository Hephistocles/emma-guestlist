<?php

$code_prefix = "eje14";
$code_separator = "-";

function encode($booking) {
	$booking_id = $booking['booking_id'];
	$ticket_id = 0;
	global $code_prefix;
	global $code_separator;
	// TODO: include ticket type?
	$meta = unserialize($booking['booking_meta']);
	// print_r($meta);
	$codes = array();
	if (isset($meta['attendees'])) {
		$attendees = $meta['attendees'];
		for ($i = 0; $i<count($attendees); $i++) {
			$ticket_id++;
			$code = implode($code_separator, array(
				$code_prefix,
				$booking_id,
				$ticket_id
			));
			echo $code + "<----<br />";
			$codes[] = $code;
		}
	} else {
		echo "No Attendees"; 
		print_r($meta);
	}
	return $codes;
}

function decode($booking) {
	$booking_id = $booking['booking_id'];
	$ticket_id = 0;
	global $code_prefix;
	global $code_separator;
	// TODO: include ticket type?
	$meta = unserialize($booking['booking_meta']);
	// print_r($meta);
	$codes = array();
	if (isset($meta['attendees'])) {
		$attendees = $meta['attendees'];
		for ($i = 0; $i<count($attendees); $i++) {
			$ticket_id++;
			$code = implode($code_separator, array(
				$code_prefix,
				$booking_id,
				$ticket_id
			));
			echo $code + "<----<br />";
			$codes[] = $code;
		}
	} else {
		echo "No Attendees"; 
		print_r($meta);
	}
	return $codes;
}

?>