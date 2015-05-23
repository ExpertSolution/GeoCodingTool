<?PHP
ini_set('max_execution_time', 14000);
class Geocoder {

 static private $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address="; // The URL sent to process Geocode
 static public function getLocation($address) {
  $request = self::$url.urlencode($address); // SELF: To refer to the class's variable. URLENCODE: Encoding a string to be used in a query part of a URL
  $response_json = file_get_contents($request); 
  $response = json_decode($response_json, true);
  if ($response['status'] == 'OK') {
   return $response['results']['0'];
  }
  else return false;
 }
}
?>
