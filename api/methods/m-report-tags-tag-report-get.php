<?php
$route = '/report/tags/:tag/report/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	if(isset($_REQUEST['week'])){ $week = $params['week']; } else { $week = date('W'); }
	if(isset($_REQUEST['year'])){ $year = $params['year']; } else { $year = date('Y'); }

	$Query = "SELECT b.* from tags t";
	$Query .= " JOIN report_tag_pivot utp ON t.Tag_ID = utp.Tag_ID";
	$Query .= " JOIN report b ON utp.report_id = b.report_id";
	$Query .= " WHERE Tag = '" . $tag . "'";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$report_id = $Database['report_id'];
		$report_date = $Database['report_date'];
		$name = $Database['name'];
		$title = $Database['title'];
		$content = $Database['content'];

		// manipulation zone

		$TagQuery = "SELECT t.tag_id, t.tag from tags t";
		$TagQuery .= " INNER JOIN report_tag_pivot utp ON t.tag_id = utp.tag_id";
		$TagQuery .= " WHERE utp.Blog_ID = " . $report_id;
		$TagQuery .= " ORDER BY t.tag DESC";
		$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());

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
			//echo $thistag . "<br />";
			if($thistag=='Archive')
				{
				$archive = 1;
				}
			}

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
