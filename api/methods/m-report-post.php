<?php
$route = '/report/';
$app->post($route, function () use ($app){

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['report_date'])){ $report_date = mysql_real_escape_string($params['report_date']); } else { $report_date = date('Y-m-d H:i:s'); }
	if(isset($params['name'])){ $name = mysql_real_escape_string($params['name']); } else { $name = 'No Name'; }
	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = 'No Title'; }
	if(isset($params['content'])){ $content = mysql_real_escape_string($params['content']); } else { $content = ''; }

	$Query = "INSERT INTO report(report_date,name,title,content)";
	$Query .= " VALUES(";
	$Query .= "'" . mysql_real_escape_string($report_date) . "',";
	$Query .= "'" . mysql_real_escape_string($name) . "',";
	$Query .= "'" . mysql_real_escape_string($title) . "',";
	$Query .= "'" . mysql_real_escape_string($content) . "'";
	$Query .= ")";
	//echo $Query . "<br />";
	mysql_query($Query) or die('Query failed: ' . mysql_error());
	$report_id = mysql_insert_id();

 	$host = $_SERVER['HTTP_HOST'];
 	$report_id = prepareIdOut($report_id,$host);

	$ReturnObject['report_id'] = $report_id;

	$app->response()->header("Content-Type", "application/json");
	echo format_json(json_encode($ReturnObject));

	});
?>
