<?php
/**
 * @package WP TOOL
 * @version 1.1
 */
/*
Plugin Name: WP TOOL
Description: This plugin provide the information about your domain, weather info, near by places and tool to convert your currency into any currency on the basis of IP and Location (Latitude and Longitude),.
Author: Shashank Singh
Email: shashank.webdeveloper@gmail.com
Whatsapp: 9990243580
Version: 1.1
Author URI: http://comingsoon.com
*/


/*
* Includes Extrnal Jquery
*/
function wptl_include_external_js() {
  //Google Maps
  wp_enqueue_script('google-maps-domain', 'http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyCssyRcp2rwnT_6YXSqhtjz0aY5SXIC60s', array(), '1', false);

  //GeoPlugin and Google JsonApi
  wp_enqueue_script('geoplugin-javascript', 'http://www.geoplugin.net/javascript.gp', array(), '1', false);
  //when using Google to load JSON API
  wp_enqueue_script('google-jsonapi', 'http://www.google.com/jsapi', array(), '1', false);
  wp_enqueue_script('geoplugin-cconvertor', 'http://www.geoplugin.net/ajax_currency_converter.gp', array(), '1', false);
}

add_action('wp_enqueue_scripts', 'wptl_include_external_js');


/*
* Service: Domain Info
*/

function wptl_get_domain_info(){?>
  <div style="height: 100%; margin-top:10%;">
      <div style="min-height: 195px; margin:0 auto; background-color:#EFF0DB; padding: 1px 25px 15px;">
          <div>
          <?php
          /*Get user ip address*/
          $ip = $_SERVER['REMOTE_ADDR'];
          echo "<br/> Your Ip Address: ".$ip;
          ?>
          <br/><br/>
          <a href='index.php?ip=true'>Know More about This IP</a>
          <br/><br/> Or Search Other <form method="post" action="#"><input type="text" name="domain"> - IP / Domain Name (Eg:example.com)<br/><br/><input type="submit" name="submit" value="Search"></form>
          </div>
      </div>
  <?php
  if (isset($_GET['ip']) || isset($_POST['submit'])) {
    $domain = preg_replace("(^https?://)", "", esc_url($_POST['domain']));
    if(!empty($domain))
    {
      //Validate Domain and IP
      if(!filter_var(gethostbyname($domain), FILTER_VALIDATE_IP)){
        echo "Not Valid Domain !!";
        exit;
      }else{
        $ip_address = gethostbyname($domain);
      }
    }
    else
    {
    	$ip_address = $ip;
    }

    /*Get user ip address details with geoplugin.net*/
    $geopluginURL='http://www.geoplugin.net/php.gp?ip='.$ip_address;
    $addrDetailsArr = unserialize(file_get_contents($geopluginURL)); 
    $city = htmlspecialchars($addrDetailsArr['geoplugin_city']);
    $region = htmlspecialchars($addrDetailsArr['geoplugin_region']);
    $country = htmlspecialchars($addrDetailsArr['geoplugin_countryName']);
    $country_code = htmlspecialchars($addrDetailsArr['geoplugin_countryCode']);
    $latitude = htmlspecialchars($addrDetailsArr['geoplugin_latitude']);
    $longitude = htmlspecialchars($addrDetailsArr['geoplugin_longitude']);
    $currency = htmlspecialchars($addrDetailsArr['geoplugin_currencyCode']);

    if(!$city){
       $city='Not Define';
    }if(!$country){
       $country='Not Define';
    }
    ?>
    <br/>
    <div style="margin:0 auto; background-color:#fff; padding: 15px 25px 15px; text-align:center;">
    <h3>Your search result</h3>
    <br/>
    <table align="center">
    	<tr>
    		<td><strong>Ip-Address : </strong></td>
    		<td><?php echo $ip_address;?></td>
    	</tr>
    	<tr>
    		<td><strong>City : </strong></td>
    		<td><?php echo $city;?></td>
    	</tr>
    	<tr>
    		<td><strong>Region : </strong></td>
    		<td><?php echo $region;?></td>
    	</tr>
    	<tr>
    		<td><strong>Country : </strong></td>
    		<td><?php echo $country;?></td>
    	</tr>
    	<tr>
    		<td><strong>Country-Code : </strong></td>
    		<td><?php echo $country_code;?></td>
    	</tr>
    	<tr>
    		<td><strong>Currency : </strong></td>
    		<td><?php echo $currency;?></td>
    	</tr>
    	<tr>
    		<td><strong>Lattitude : </strong></td>
    		<td><?php echo $latitude;?></td>
    	</tr>
    	<tr>
    		<td><strong>Longitude : </strong></td>
    		<td><?php echo $longitude;?></td>
    	</tr>
    </table>
    <br/>
    <div id="outermap" style="text-align:center">
    <div id="map" style="width: 700px; height: 400px; display: inline-block;"></div>
    </div>

      <script type="text/javascript">
      var city = '<?php echo $city;?>';
      var country = '<?php echo $country;?>';
      var Lat = '<?php echo $latitude;?>';
      var Long = '<?php echo $longitude;?>';
      var locations = [
    		{
    			lat:	Lat,
    			lon:	Long,
    			address:	city,
                title:	country
    		}
        ];

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          //center: new google.maps.LatLng(-33.92, 151.25),
    	  center: new google.maps.LatLng(Lat, Long),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;

        for (i = 0; i < locations.length; i++) {  
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i].lat, locations[i].lon),
            map: map
          });

    	  bindInfoWindow(marker, map, infowindow, "<p>" + locations[i].address + "<br/>" + locations[i].title + "</p>",locations[i].title);
        }

    	function bindInfoWindow(marker, map, infowindow, html, Ltitle) { 
        google.maps.event.addListener(marker, 'mouseover', function() {
                infowindow.setContent(html); 
                infowindow.open(map, marker); 

        });
        google.maps.event.addListener(marker, 'mouseout', function() {

        }); 
    }
      </script>  
  <?php
  }
}
add_shortcode('domain-info','wptl_get_domain_info');


/*
* Service: Currency Converter
*/

function wptl_get_currency_info(){
?>
  <div style="margin-top:10%;">
  <div style="margin:0 auto; background-color:#EFF0DB; padding: 10px 25px 25px;">
  <h3>Currency Converter</h3>
  <input type='text' id='gp_amount' size='4' /> 
  <select id="gp_from"></select> to <select id="gp_to"></select>
  <p><input type='button' onClick='gp_convertIt()' value = 'Convert It' /></p>
  <div id="gp_converted"></div>
  </div>
  <script>gp_currencySymbols()</script>
<?php  
}
add_shortcode('currency-info','wptl_get_currency_info');


/*
* Service: Weather Forecasting
*/

function wptl_get_weather_info(){
?>
  <script>
  jQuery(document).ready(function($){
    $("#weather_by_loc").on('submit',function(e) {
      e.preventDefault();
      var loc = $("#location").val();
      $.ajax({
        url: "<?php echo plugin_dir_url(__FILE__). 'ajax/WeatherByLocData.php';?>",
        dataType: 'text',           // Url to which the request is send
        type: "POST",               // Type of request to be send, called as method
        data: {
          location: loc
        }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        success: function(data)     // A function to be called if request succeeds
        {
          $("#weather_result").html(data);
        }
      });

    });
  });
  </script>
  <div style="margin-top:10%;">
  <div style="margin:0 auto; background-color:#EFF0DB; padding: 10px 25px 25px;">
  <div class="weather_by_loc">
  <form style="text-align:center;" id="weather_by_loc" name="weather_by_loc" method="post" action="javascript:void(0);">
  Search By Location - <select id="location" name="location">
  <option value="">Select Location</option>
  <option value="Delhi">Delhi</option>
  <option value="Chennai">Chennai</option>
  <option value="Mumbai">Mumbai</option>
  <option value="Kolkata">Kolkata</option>
  <option value="Ahmedabad">Ahmedabad</option>
  <option value="Varanasi">Varanasi</option>
  <option value="Lucknow">Lucknow</option>
  <option value="Kanpur">Kanpur</option>
  <option value="Banglore">Banglore</option>
  <option value="Allahabad">Allahabad</option>
  <option value="Pune">Pune</option>
  <option value="Surat">Surat</option>
  <option value="Indore">Indore</option>
  <option value="Patna">Patna</option>
  <option value="Hyderabad">Hyderabad</option>
  </select>
  <input type="submit" name="submit" value="Search">
  </form>
  <br><br>
  <div id="weather_result" style="text-align:center;">
  </div>
  </div>
  </div>
  </div>
<?php
}
add_shortcode('weather-info','wptl_get_weather_info');


/*
* Service: Find Distance
*/

function wptl_get_distance_info(){
?>
  <script>
  jQuery(document).ready(function($){
    $("#search_by_loc").on('submit',function(e) {
      e.preventDefault();
      var loc1 = $("#first_location").val();
      var loc2 = $("#second_location").val();
      $.ajax({
        url: "<?php echo plugin_dir_url(__FILE__).'ajax/DistanceByLocData.php';?>",
        dataType: 'text',         // Url to which the request is send
        type: "POST",             // Type of request to be send, called as method
        data: {
          location1: loc1,
          location2: loc2,
        }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        success: function(data)   // A function to be called if request succeeds
        {
          $("#search_result").html(data);
        }
      });

    });
  });
  </script>
  </head>
  <body>
  <div style="margin-top:10%;">
  <div style="margin:0 auto; background-color:#EFF0DB; padding: 10px 25px 25px;">
  <div class="near_by_loc">
  <form style="text-align:center;" id="search_by_loc" name="search_by_loc" method="post" action="javascript:void(0);">
  Location First - <select id="first_location" name="first_location">
  <option value="Delhi">Delhi</option>
  <option value="Chennai">Chennai</option>
  <option value="Mumbai">Mumbai</option>
  <option value="Kolkata">Kolkata</option>
  <option value="Ahmedabad">Ahmedabad</option>
  <option value="Varanasi">Varanasi</option>
  <option value="Lucknow">Lucknow</option>
  <option value="Kanpur">Kanpur</option>
  <option value="Banglore">Banglore</option>
  <option value="Allahabad">Allahabad</option>
  <option value="Pune">Pune</option>
  <option value="Surat">Surat</option>
  <option value="Indore">Indore</option>
  <option value="Patna">Patna</option>
  <option value="Hyderabad">Hyderabad</option>
  <option value="Jaipur">Jaipur</option>
  <option value="Bhopal">Bhopal</option>
  </select>

  Location Second - <select id="second_location" name="second_location">
  <option value="Delhi">Delhi</option>
  <option value="Chennai">Chennai</option>
  <option value="Mumbai">Mumbai</option>
  <option value="Kolkata">Kolkata</option>
  <option value="Ahmedabad">Ahmedabad</option>
  <option value="Varanasi">Varanasi</option>
  <option value="Lucknow">Lucknow</option>
  <option value="Kanpur">Kanpur</option>
  <option value="Banglore">Banglore</option>
  <option value="Allahabad">Allahabad</option>
  <option value="Pune">Pune</option>
  <option value="Surat">Surat</option>
  <option value="Indore">Indore</option>
  <option value="Patna">Patna</option>
  <option value="Hyderabad">Hyderabad</option>
  <option value="Jaipur">Jaipur</option>
  <option value="Bhopal">Bhopal</option>
  </select>
  <input type="submit" name="submit" value="Search">
  </form>
  <h3 style="text-align:center;">Your Search Result</h3>
  <div id="search_result" style="text-align:center;">
  </div>
  </div>
  </div>
  </div>
<?php
}
add_shortcode('distance-info','wptl_get_distance_info');


/*
* Service: Near By Places
*/

function wptl_get_nearplaces_info(){
?>
  <script>
  jQuery(document).ready(function($){
    $("#search_by_loc").on('submit',function(e) {
      e.preventDefault();
      var loc = $("#location").val();
      $.ajax({
        url: "<?php echo plugin_dir_url(__FILE__).'ajax/SearchByLocData.php';?>",
        dataType: 'text',           // Url to which the request is send
        type: "POST",               // Type of request to be send, called as method
        data: {
          location: loc
        },    // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        success: function(data)     // A function to be called if request succeeds
        {
          $("#search_result").html(data);
        }
      });

    });
  });
  </script>
  </head>
  <body>
  <div style="margin-top:10%;">
  <div style="margin:0 auto; background-color:#EFF0DB; padding: 10px 25px 25px;">
  <div class="near_by_loc">
  <form style="text-align:center;" id="search_by_loc" name="search_by_loc" method="post" action="javascript:void(0);">
  Search By Location - <select id="location" name="location">
  <option value="">Select Location</option>
  <option value="Delhi">Delhi</option>
  <option value="Chennai">Chennai</option>
  <option value="Mumbai">Mumbai</option>
  <option value="Kolkata">Kolkata</option>
  <option value="Ahmedabad">Ahmedabad</option>
  <option value="Varanasi">Varanasi</option>
  <option value="Lucknow">Lucknow</option>
  <option value="Kanpur">Kanpur</option>
  <option value="Banglore">Banglore</option>
  <option value="Allahabad">Allahabad</option>
  <option value="Pune">Pune</option>
  <option value="Surat">Surat</option>
  <option value="Indore">Indore</option>
  <option value="Patna">Patna</option>
  <option value="Hyderabad">Hyderabad</option>
  <option value="Jaipur">Jaipur</option>
  <option value="Bhopal">Bhopal</option>
  </select>
  <input type="submit" name="submit" value="Search">
  </form>
  <div id="search_result" style="text-align:center;">
  </div>
  </div>
  </div>
  </div>
<?php
}
add_shortcode('nearplaces-info','wptl_get_nearplaces_info');


/*
* Add admin menu for plugin setting page
*/

add_action('admin_menu', 'wptl_plugin_setting_menu');
function wptl_plugin_setting_menu(){
    add_menu_page('WP Tool Setting', 'WP Tool Setting', 'manage_options', 'ip-tool-setting', 'wptl_plugin_setting_page' );
}

function wptl_plugin_setting_page(){
  wp_enqueue_style( 'tool-admin-css', plugin_dir_url(__FILE__).'css/tool-style.css' );
?>
  <div class="wrap">
    <h1>Use Shortcode for using different IP Tool Services</h1>
    <br/>
    <table id="table-info">
      <tr>
      <th>Service</th>
      <th>Shortcode</th>
      </tr>
    <tr>
        <td>Currency Converter</td>
        <td><strong>[currency-info]</strong></td>
    </tr>
    <tr>
        <td>Weather Forcasting</td>
        <td><strong>[weather-info]</strong></td>
    </tr>
    <tr>
        <td>Domain Info</td>
        <td><strong>[domain-info]</strong></td>
    </tr>
    <tr>
        <td>Find Distance</td>
        <td><strong>[distance-info]</strong></td>
    </tr>
    <tr>
        <td>Near By Places</td>
        <td><strong>[nearplaces-info]</strong></td>
    </tr>
    </table>
  </div>
<?php
}
?>