<?php
$route = '/report/tags/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$Query = "SELECT t.tag_id, t.tag, count(*) AS report_count from tags t";
	$Query .= " INNER JOIN report_tag_pivot utp ON t.tag_id = utp.tag_id";
	$Query .= " GROUP BY t.Tag ORDER BY count(*) DESC";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$tag_id = $Database['tag_id'];
		$tag = $Database['tag'];
		$report_count = $Database['report_count'];

		$host = $_SERVER['HTTP_HOST'];
		$tag_id = prepareIdOut($tag_id,$host);

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['report_count'] = $report_count;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
