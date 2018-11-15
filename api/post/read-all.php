<?php
// pattern
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Content-Type, Accept');
	header('Content-Type:application/json');
	$method = $_SERVER['REQUEST_METHOD'];
	$thisScriptMethod = 'GET';
	if($method != $thisScriptMethod)
		die("'$method' method not supported, this is a script for '$thisScriptMethod' method");
	include_once '../../config/Connection.php';
	include_once '../../models/dmh.php';
	$db = new Connection();
	$conn = $db->getConn();
	$dmh = new dmh($conn);
	$form = json_decode(file_get_contents('php://input'), true);
	$form['table'] = basename(dirname(__FILE__));

echo json_encode($dmh->getData($method, $form));