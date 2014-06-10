<?php
session_start();
if (empty($_REQUEST['action'])) {
	echo json_encode(array(
		'success'=>false,
		'errormsg'=>'Not a valid action'
	));
	exit();
}
$params = $_REQUEST;
$r = new RiotApi($params);
$action = $_REQUEST['action'];
try {
	//check if the action exists in the controller. if not, throw an exception.
	if (method_exists($r, $action) === false) {
	   throw new Exception('Action is invalid.');
	}
	//execute the action
	$result['action'] = $action;
	$result['request'] = $params;
	$result['data'] = $r->$action();
	$result['success'] = true;
} catch (Exception $e) {
	//catch any exceptions and report the problem
	$result['success'] = false;
	$result['errormsg'] = $e->getMessage();
	error_log($result['errormsg']);
}
//echo the result of the API call
echo json_encode($result);
exit();