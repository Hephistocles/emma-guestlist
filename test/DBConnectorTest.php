<?php

require_once "../credentials.php"; 
require_once dirname(__FILE__) . '/../src/DBConnector.php';

class DBConnectorTest extends PHPUnit_Framework_TestCase {
	function canConnect() {
		try {
			global $dbhost,$dbuser,$dbpass,$dbname;
			$conn = new DBConnector($dbhost,$dbuser,$dbpass,$dbname);
		} catch (Exception $error) {
			$this->fail($error->getMessage());
		}
		return $conn;
	}

	function testCanQuery() {
		$conn = $this->canConnect();
		$conn->query("SELECT 1 AS \"one\", 2 AS \"two\", 3 AS \"three\"");
		if ($row = $conn->get_row()) {
			$this->assertEquals(1, $row[0]);
			$this->assertEquals(1, $row["one"]);
			$this->assertEquals(2, $row[1]);
			$this->assertEquals(2, $row["two"]);
			$this->assertEquals(3, $row[2]);
			$this->assertEquals(3, $row["three"]);
		} else {
			$this->fail("No results returned");
		}
		if ($row = $conn->get_row()) {
			$this->fail("Too many results returned");
		}
		try {
			$conn->query("invalid query");
		} catch (Exception $e) {
			return;
		}
		fail("Allowed invalid query");
	}
	function testSQL () {
		try {
			$conn = $this->canConnect();
			$conn->query("CREATE TABLE IF NOT EXISTS sqltest (ID int(11) NOT NULL AUTO_INCREMENT, name varchar(10) NOT NULL, age int(11) NOT NULL, PRIMARY KEY (ID));");
			$conn->query("INSERT INTO sqltest (name, age) VALUES ('fiona', 20)");
			$conn->query("INSERT INTO sqltest (name, age) VALUES ('chris', 21)");
			$conn->query("INSERT INTO sqltest (name, age) VALUES ('daisy', 19)");
			$conn->query("UPDATE sqltest SET age=20 WHERE name='daisy'");
			$conn->query("DELETE FROM sqltest WHERE name='fiona'");
			$conn->query("INSERT INTO sqltest (name, age) VALUES ('quentin', 11)");
			$array = $conn->get_array("SELECT name, age FROM sqltest WHERE age>15 ORDER BY age");
			$conn->query("DROP TABLE sqltest;");
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
		$this->assertEquals(2, count($array));
		$this->assertEquals('daisy', $array[0][0]);
		$this->assertEquals('chris', $array[1]['name']);
		$this->assertEquals(20, $array[0]['age']);
		$this->assertEquals(21, $array[1][1]);
	}

	function testSetup() {
		$conn = $this->canConnect();
		$conn->exec("../src/setup.sql");
		$conn->exec("../src/defaults.sql");
		$conn->exec("../src/teardown.sql");
	}
}

?>