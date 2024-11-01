<?php
error_reporting(0);
//validate and sanitize field value
$loc = trim($_POST['location']);
if(empty($loc)){
	echo 'Please select loaction !!';
	exit;
}

if(!preg_match("/^[a-zA-Z]+$/", $loc)){
	echo 'Not Valid Location !!';
	exit;
}

$loc1 = urlencode($loc);

$geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$loc1.'&sensor=false');
// We convert the JSON to an array
$geo = json_decode($geo, true);
// If everything is cool
if ($geo['status'] = 'OK') {
  // We set our values
  $latitude = htmlspecialchars($geo['results'][0]['geometry']['location']['lat']);
  $longitude = htmlspecialchars($geo['results'][0]['geometry']['location']['lng']);
}
 
if ( is_numeric($latitude) && is_numeric($longitude) ) {
 
	$lat = $latitude;
	$long = $longitude;
	//set farenheight for US
	if ($geoplugin['geoplugin_countryCode'] == 'US') {
		$tempScale = 'fahrenheit';
		$tempUnit = '&deg;F';
	} else {
		$tempScale = 'celsius';
		$tempUnit = '&deg;C';
	}
	require_once('../ParseXml.class.php');
 
	$xml = new ParseXml(); 
	$xml->LoadRemote("http://api.wunderground.com/auto/wui/geo/ForecastXML/index.xml?query={$lat},{$long}", 3);
	$dataArray = $xml->ToArray();
 
	$html = "<center><h4>Weather forecast for " . $loc;
	$html .= "</h4><table cellpadding=5 cellspacing=10><tr>";
 
	foreach ($dataArray['simpleforecast']['forecastday'] as $arr) {
 
		$html .= "<td align='center'>" . $arr['date']['weekday'] . "<br />";
		$html .= "<img src='http://icons-pe.wxug.com/i/c/a/" . $arr['icon'] . ".gif' border=0 /><br />";
		$html .= "<font color='red'>" . $arr['high'][$tempScale] . $tempUnit . " </font>";
		$html .= "<font color='blue'>" . $arr['low'][$tempScale] . $tempUnit . "</font>";
		$html .= "</td>";
 
 
	}
	$html .= "</tr></table>";
 
	echo $html;
}
?>