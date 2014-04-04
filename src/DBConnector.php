<?php
class DBConnector {

	public $connection;
	private $latest_result;
	function __construct($dbhost,$dbuser,$dbpass,$dbname) {
		$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname, 3306);
	
		if ($this->connection->connect_errno > 0) {
			throw new Exception ('Unable to connect to database [' . $connection->connect_error . ']');
		}
	}
	/**
	 * Execute a given SQL script directly on the command line
	 * @param	string	$script_location The fully qualified path name to the sql script.
	 */
	function exec($script_location) {
		$commands = file_get_contents($script_location);
		$i = 0; 
		
		if ($this->connection->multi_query($commands)) { 
			do { 
				$i++; 
			} while ($this->connection->next_result()); 
		} 
		if ($this->connection->errno) { 
			throw new Exception("Batch execution prematurely ended on statement $i [{$this->connection->error}].\n") ;
		} 
	}
	function query($sql) {
		if (!$result = $this->connection->query($sql)) {
			throw new Exception ('There was an error running query[' . $this->connection->error . ']');
		}
		$this->latest_result = $result;
		return $result;
	}
	function get_row() {
		return $this->latest_result->fetch_array();
	}
	function get_remaining_array() {
		$array = array();
		while ($row = $this->get_row()) {
			$array[] = $row;
		}
		return $array;
	}
	function get_array() {

		$i = func_num_args();
		$a = func_get_args();

		switch ($i) {
			case 1:
				// if a single argument is passed, interpret it as SQL for a new query
				$this->query($a[0]);
			default:
				// either way - get an array for the latest result
				return $this->get_remaining_array();
		}
	}
}
	
?>