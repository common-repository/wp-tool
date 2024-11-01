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

$loc = urlencode($loc);

$geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$loc.'&sensor=false');
// We convert the JSON to an array
$geo = json_decode($geo, true);
// If everything is cool
if ($geo['status'] = 'OK') {
  // We set our values
  $latitude = htmlspecialchars($geo['results'][0]['geometry']['location']['lat']);
  $longitude = htmlspecialchars($geo['results'][0]['geometry']['location']['lng']);
}
$limit = 5;
$nearByPlaces = unserialize(file_get_contents('http://www.geoplugin.net/extras/nearby.gp?lat='.$latitude.'&long='.$longitude.'&limit='.$limit));

$limit = 5;
$count = 1;
echo '<h3 style="text-align:center;">Near By Your Search Location</h3>';
echo '<table align="center">';
foreach($nearByPlaces as $place)
{
?>
	<tr>
    	<td style="text-align:center;"><?php echo "<span style='margin-right: -50px; color:#2D1F9A;'><b>Place-".$count."</b></span>";?></td>
    </tr>
	<tr>
		<td><strong>Place Name : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_place']);?></td>
	</tr>
	<tr>
		<td><strong>Region : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_region']);?></td>
	</tr>
	<tr>
		<td><strong>Latitude : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_latitude']);?></td>
	</tr>
	<tr>
		<td><strong>Longitude : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_latitude']);?></td>
	</tr>
	<tr>
		<td><strong>Distance(miles) : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_distanceMiles']);?></td>
	</tr>
	<tr>
		<td><strong>Distance(km) : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_distanceKilometers']);?></td>
	</tr>
	<tr>
		<td><strong>Direction (degrees) : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_directionAngle']);?></td>
	</tr>
	<tr>
		<td><strong>Direction (heading) : </strong></td>
		<td><?php echo htmlspecialchars($place['geoplugin_directionHeading']);?></td>
	</tr>
    <tr>
    	<td></td>
        <td></td>
    <tr>
<?php
	$count++;
}
?>
</table>