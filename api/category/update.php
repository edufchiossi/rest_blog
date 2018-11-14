<?php
$method = $_SERVER['REQUEST_METHOD'];
if($method != 'PUT')
	die("'$method' method not supported, this is a script for 'PUT' method");
// pattern
	header('Content-Type:application/json');
	header('Access-Control-Allow-Origin: *');
	include_once '../../config/Connection.php';
	include_once '../../models/dmh.php';
	$db = new Connection();
	$conn = $db->getConn();
	$dmh = new dmh($conn);
	$form = json_decode(file_get_contents('php://input'), true);
	$form['table'] = basename(dirname(__FILE__));
	
echo json_encode($dmh->getData($method, $form));