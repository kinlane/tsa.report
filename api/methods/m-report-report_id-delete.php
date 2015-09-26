<?php
$route = '/report/:report_id/';
$app->delete($route, function ($report_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$report_id = prepareIdIn($report_id,$host);

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$_POST = $request->params();

	$query = "DELETE FROM report WHERE ID = " . $report_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	});
?>
