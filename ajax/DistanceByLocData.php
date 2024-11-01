<?php
error_reporting(0);
//validate and sanitize fields value
$loc1 = trim($_POST['location1']);
$loc2 = trim($_POST['location2']);

if(empty($loc1) || empty($loc2)){
  echo 'Please select loactions !!';
  exit;
}

if(!preg_match("/^[a-zA-Z]+$/", $loc1) || !preg_match("/^[a-zA-Z]+$/", $loc2)){
  echo 'Not Valid Locations !!';
  exit;
}

$loc1_encode = urlencode($loc1);
$loc2_encode = urlencode($loc2);

$geo1 = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$loc1_encode.'&sensor=false');
// We convert the JSON to an array
$geo1 = json_decode($geo1, true);
// If everything is cool
if ($geo1['status'] = 'OK') {
  // We set our values
  $latitude1 = htmlspecialchars($geo1['results'][0]['geometry']['location']['lat']);
  $longitude1 = htmlspecialchars($geo1['results'][0]['geometry']['location']['lng']);
}
$geo2 = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$loc2_encode.'&sensor=false');
$geo2 = json_decode($geo2, true);
// If everything is cool
if ($geo2['status'] = 'OK') {
  // We set our values
  $latitude2 = htmlspecialchars($geo2['results'][0]['geometry']['location']['lat']);
  $longitude2 = htmlspecialchars($geo2['results'][0]['geometry']['location']['lng']);
}

$from = urlencode($loc1);
$to = urlencode($loc2);
$data = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&language=en-EN&sensor=false");
$data = json_decode($data);
?>
<script>
  var lat1 = '<?php echo $latitude1;?>';
  var lon1 = '<?php echo $longitude1;?>';
  var lat2 = '<?php echo $latitude2;?>';
  var lon2 = '<?php echo $longitude2;?>';
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2-lat1);  // deg2rad below
  var dLon = deg2rad(lon2-lon1); 
  var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  var d = R * c; // Distance in km
  var result = "Straight Distance(Between Geographical Points): "+Math.round(d * 100) / 100+" Km.";
  $("#straight_distance").html(result);

function deg2rad(deg) {
  return deg * (Math.PI/180)
}
</script>
<?php
$time = 0;
$distance = 0;
foreach($data->rows[0]->elements as $road) {
    $time = htmlspecialchars($road->duration->text);
    $distance = htmlspecialchars($road->distance->text);
}
echo "From: ".$data->origin_addresses[0];
echo "<br/>";
echo "To: ".$data->destination_addresses[0];
echo "<br/>";
echo "Time: ".$time;
echo "<br/>";
echo "Distance: ".$distance;
echo '<br/><div id="straight_distance" style="text-align:center;"></div>';
?>