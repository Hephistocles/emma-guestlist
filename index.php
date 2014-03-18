<html>
<head>
<title>Bookings</title>
</head>
<body>
<h1>Bookings:</h1>
<?php
	include "credentials.php";
	$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname, 3306);
	// $con=mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
		
	if ($connection->connect_errno > 0) {
	    die ('Unable to connect to database [' . $connection->connect_error . ']');
	}


	$sql = "SELECT * FROM eje2014_em_bookings LIMIT 0 , 5";
	if (!$result = $connection->query($sql)) {
	    die ('There was an error running query[' . $connection->error . ']');
	}
	
	while ($row = $result->fetch_array()) {
		print_r($row);
	}

?>
</body>

</html>