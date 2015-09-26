<?php
$route = '/report/:report_id/tags/';
$app->post($route, function ($report_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$report_id = prepareIdIn($report_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['tag']))
		{
		$tag = trim(mysql_real_escape_string($param['tag']));

		$CheckTagQuery = "SELECT tag_id FROM tags where tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);
			$tag_id = $Tag['tag_id'];
			}
		else
			{
			$query = "INSERT INTO tags(Tag) VALUES('" . $tag . "'); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());
			$tag_id = mysql_insert_id();
			}

		$CheckTagPivotQuery = "SELECT * FROM report_tag_pivot where tag_id = " . trim($tag_id) . " AND report_id = " . trim($report_id);
		$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

		if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
			{
			$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
			}
		else
			{
			$query = "INSERT INTO report_tag_pivot(tag_id,report_id) VALUES(" . $tag_id . "," . $report_id . "); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());
			}

		$tag_id = prepareIdOut($tag_id,$host);

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['report_count'] = 0;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
