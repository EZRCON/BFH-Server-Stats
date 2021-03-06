<?php
// BF4 Stats Page by Ty_ger07
// http://open-web-community.com/

// include required files
require_once("../pchart/class/pData.class.php");
require_once("../pchart/class/pDraw.class.php");
require_once("../pchart/class/pImage.class.php");
require_once('../../../config.php');
require_once('../connect.php');
require_once('../case.php');
// check if necessary environment exists on this server
if(extension_loaded('gd') && function_exists('gd_info'))
{
	// SQL query limit
	$limit = 7;
	// check if a server was provided
	// if so, this is a server stats page
	if(!empty($sid))
	{
		$query  = "
			SELECT SUBSTRING(`TimeMapLoad`, 1, length(`TimeMapLoad`) - 9) AS Date, AVG(`MaxPlayers`) AS Average
			FROM `tbl_mapstats`
			WHERE `ServerID` = {$sid}
			AND `Gamemode` != ''
			AND `MapName` != ''
			GROUP BY `Date`
			ORDER BY `Date` DESC
			LIMIT {$limit}
		";
		$result = @mysqli_query($BF4stats, $query);
	}
	// this must be a global stats page
	else
	{
		// merge server IDs array into a variable
		$ids = join(',',$ServerIDs);
		$query  = "
			SELECT SUBSTRING(`TimeMapLoad`, 1, length(`TimeMapLoad`) - 9) AS Date, AVG(`MaxPlayers`) AS Average
			FROM `tbl_mapstats`
			WHERE `ServerID` in ({$ids})
			AND `Gamemode` != ''
			AND `MapName` != ''
			GROUP BY `Date`
			ORDER BY `Date` DESC
			LIMIT {$limit}
		";
		$result = @mysqli_query($BF4stats, $query);
	}
	if($result)
	{
		$i = 1;
		while($row = mysqli_fetch_assoc($result))
		{
			$rounds[$i] = $i;
			$date[] = date("M d", strtotime($row['Date']));
			$average[] = $row['Average'];
			$i++;
		}
	}
	$myData = new pData();
	$myData->addPoints($average,"Serie1");
	$myData->setSerieDescription("Serie1","Average");
	$myData->setSerieOnAxis("Serie1",0);
	$serieSettings = array("R"=>218,"G"=>165,"B"=>32);
	$myData->setPalette("Serie1",$serieSettings);
	$myData->addPoints($date,"Absissa");
	$myData->setAbscissa("Absissa");
	$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
	$myData->setAxisName(0,"Players");
	$myData->setAxisUnit(0,"");
	$myPicture = new pImage(600,300,$myData,TRUE);
	$myPicture->setFontProperties(array("FontName"=>"../pchart/fonts/Forgotte.ttf","FontSize"=>12));
	$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE
	, "R"=>150, "G"=>150, "B"=>150);
	// if so, this is a server stats page
	if(!empty($sid))
	{
		$myPicture->drawText(297,18,"Average number of players in server in last ". $limit ." days of server activity.",$TextSettings);
	}
	// this must be a global stats page
	else
	{
		$myPicture->drawText(297,18,"Average number of players in servers in last ". $limit ." days of server activity.",$TextSettings);
	}
	$myPicture->setShadow(FALSE);
	$myPicture->setGraphArea(50,50,576,270);
	$myPicture->setFontProperties(array("R"=>150,"G"=>150,"B"=>150,"FontName"=>"../pchart/fonts/pf_arma_five.ttf","FontSize"=>6));
	$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
	, "Mode"=>SCALE_MODE_FLOATING
	, "LabelingMethod"=>LABELING_ALL
	, "GridR"=>150, "GridG"=>150, "GridB"=>150, "GridAlpha"=>50, "TickR"=>150, "TickG"=>150, "TickB"=>150, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>0, "DrawSubTicks"=>1, "SubTickR"=>150, "SubTickG"=>150, "SubTickB"=>150, "SubTickAlpha"=>50, "DrawYLines"=>NONE, "AxisR"=>150, "AxisG"=>150,"AxisB"=>150);
	$myPicture->drawScale($Settings);
	$Config = "";
	$myPicture->drawSplineChart();
	$Config = array("FontR"=>150, "FontG"=>150, "FontB"=>150, "FontName"=>"../pchart/fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
	, "Mode"=>LEGEND_HORIZONTAL
	);
	$myPicture->drawLegend(529,12,$Config);
	$myPicture->stroke($BrowserExpire=TRUE);
}
// php GD extension doesn't exist. show error image
else
{
	// start outputting the image
	header("Content-type: image/png");
	echo file_get_contents('./images/error.png');
}
?>
