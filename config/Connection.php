<?php
class Connection {
	private $host;
	private $db;
	private $user;
	private $pass;
	private $conn;

	public function __construct($host='localhost', $db='rest_blog', $user='root', $pass=''){
		try {
			$this->conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(Exception $e) {
			die('Connection error: '.$e->getMessage());
		}
	}

	public function getConn(){
		return $this->conn;
	}
}