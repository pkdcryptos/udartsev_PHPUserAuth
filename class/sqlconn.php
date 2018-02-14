<?php
### Vladimir S. Udartsev
### udartsev.ru
class sqlconn {
	private $sqlconn;
	public $error = null;

	/*Connection To Database Public Function*/
	public function __construct($mysqlSettings) {
		/*Setting Variables*/
		$serverName = $mysqlSettings['serverName'];
		$userName = $mysqlSettings['userName'];
		$userPassword = $mysqlSettings['userPassword'];
		$databaseName = $mysqlSettings['databaseName'];

		/*Connect To Database*/
		$sqlconn = new mysqli($serverName, $userName, $userPassword, $databaseName);

		/*If Connection Error -> Save Error To Error Variable*/
		if ($sqlconn->connect_error) {
			$this->error['MySQLiConnectionError'] = "MySQLi ERROR: Connection failed: " . $sqlconn->connect_error;
			$_SESSION['error'] = "Can not connect to database! Please, contact to administrator.";
			return false;
		}

		/*Setting Up Database Encofing to UTF-8*/
		$sqlconn->query("SET NAMES 'utf8'");
		$sqlconn->query("SET CHARACTER SET 'utf8'");

		/*Save Connection To Private Variable*/
		$this->sqlconn = $sqlconn;

		/*Return Connection Data*/
		return $this->sqlconn;
	}

	/*SQL Query Request*/
	function query($query) {
		/*If Query OK ->return Data, else -> save error*/
		if ($q = mysqli_query($this->sqlconn, $query)) {return $q;} else {
			$this->error['MySQLiQueryError'] = mysqli_error($this->sqlconn) . ' [' . $query . ']';
			$_SESSION['error'] = "Database query insertion error. Please, contact to administrator.";
			//echo "<pre>"; //TEST ONLY
			//var_dump($this->error); //TEST ONLY
			return false;
		}
	}

	/*Row Prepare for the SQL Request*/
	public function preapreString($data) {

		/*Removing Spaces*/
		$data = trim($data);

		/*Shields Special Characters In a String and Returns It*/
		return $this->sqlconn->escape_string($data);
	}

	/*Close Connection Request*/
	public function close() {
		$this->sqlconn->close();
	}
}