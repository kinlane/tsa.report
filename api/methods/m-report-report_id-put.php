<?php
$route = '/report/:report_id/';
$app->put($route, function ($report_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$report_id = prepareIdIn($report_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['report_date'])){ $report_date = mysql_real_escape_string($params['report_date']); } else { $report_date = date('Y-m-d H:i:s'); }
	if(isset($params['name'])){ $name = mysql_real_escape_string($params['name']); } else { $name = 'No Title'; }
	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = 'No Title'; }
	if(isset($params['content'])){ $content = mysql_real_escape_string($params['content']); } else { $content = ''; }

  	$Query = "SELECT * FROM report WHERE report_id = " . $report_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{

		$query = "UPDATE report SET";
 		$query .= " name = '" . $name . "'";
		$query .= ", title = '" . $title . "'";
		$query .= ", content = '" . $content . "'";

		$query .= " WHERE report_id = " . $report_id;

		echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		}

	$TagQuery = "SELECT t.tag_id, t.tag from tags t";
	$TagQuery .= " INNER JOIN report_tag_pivot rtp ON t.tag_id = rtp.tag_id";
	$TagQuery .= " WHERE rtp.report_id = " . $report_id;
	$TagQuery .= " ORDER BY t.tag DESC";
	$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());

	$report_id = prepareIdOut($report_id,$host);

	$F = array();
	$F['report_id'] = $report_id;
	$F['report_date'] = $report_date;
	$F['name'] = $name;
	$F['title'] = $title;
	$F['content'] = $content;

	$F['tags'] = array();

	while ($Tag = mysql_fetch_assoc($TagResult))
		{
		$thistag = $Tag['tag'];

		$T = array();
		$T = $thistag;
		array_push($F['tags'], $T);
		}

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
