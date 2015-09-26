<?php
$route = '/report/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['query'])){ $query = trim(mysql_real_escape_string($params['query'])); } else { $query = '';}
	if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
	if(isset($params['count'])){ $count = trim(mysql_real_escape_string($params['count'])); } else { $count = 250;}
	if(isset($params['sort'])){ $sort = trim(mysql_real_escape_string($params['sort'])); } else { $sort = 'Title';}
	if(isset($params['order'])){ $order = trim(mysql_real_escape_string($params['order'])); } else { $order = 'DESC';}

	// Pull from MySQL
	if($query!='')
		{
		$Query = "SELECT * FROM report WHERE name LIKE '%" . $query . "%' OR title LIKE '%" . $query . "%' OR content LIKE '%" . $query . "%'";
		}
	else
		{
		$Query = "SELECT * FROM report";
		}
	$Query .= " ORDER BY " . $sort . " " . $order . " LIMIT " . $page . "," . $count;
	//echo $Query . "<br />";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$report_id = $Database['report_id'];
		$report_date = $Database['report_date'];
		$name = $Database['name'];
		$title = $Database['title'];
		$content = $Database['content'];

		$TagQuery = "SELECT t.tag_id, t.tag from tags t";
		$TagQuery .= " INNER JOIN report_tag_pivot utp ON t.tag_id = utp.tag_id";
		$TagQuery .= " WHERE utp.report_id = " . $report_id;
		$TagQuery .= " ORDER BY t.tag DESC";
		$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());

		// manipulation zone
		$host = $_SERVER['HTTP_HOST'];
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
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
