<?PHP

/*
****	@DESCRIPTION:		Adds address to a database table for extraction
****	@AUTHOR:			Nathaniel Vala & Philip Bui
*/

// Attempt MySQL server connection.
$link = mysql_connect ('localhost', 'root', 'toor');

//Check connection, throw error if you cannot connect
if(!$link){
	echo "connection failed";
    die("ERROR: Could not connect. " . mysql_error());
} else{
	//echo "successful connection";

	//select database
	mysql_select_db( 'EYTool' );
	
		//assign table count
		$row = mysql_fetch_row(mysql_query("SELECT MAX(BATCH_NO) FROM ADDRESSES"));
		echo "<br><h3 align=center>Your BATCH_NO = " . $BatchNo = $row[0] + 10;
		echo "<br> Please store this to collect your results later.";
	
}




require 'geocoder.php';
$geocode = new Geocoder();
if (isset($_FILES['file']['tmp_name'])) {
	switch ($_FILES['file']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			die('No file sent.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			die('Exceeded filesize limit.');
		default:
			die('Unknown errors.');
	}
	$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
	if (!in_array($_FILES['file']['type'], $mimes)){
		die("Invalid file type");
	}
	$addressFound = True;
	$data = $_FILES['file']['tmp_name'];
	$contents = file_get_contents($_FILES['file']['tmp_name']);
	$addresses = explode(";", $contents);
	$addressPattern = "%(\d{1,6})\s+(\s*([a-zA-Z]{3,30}|[A-Z]{1,5})\b){1,3}\s+([a-zA-Z]{3,20}|[A-Z]{1,5}.?)(\s*\-?,?\s*[a-zA-Z]{2,30}){0,6}(\s*,?\s*\d{1,5})?(\s*\-?,?\s*[a-zA-Z]{2,30}){0,3}%"; 
	//$addressPattern = "%([a-zA-Z]{1,6}.?\s*\d{1,5})?\d{1,5}(\s+\b[a-zA-Z]{3,30}\b){1,3}(\s+\b[a-zA-Z]{2,10}.?\b)(,?\s+\b[a-zA-Z]{3,30}\b){0,3}(,?\s+\d{1,5})?(,?\s+\b[a-zA-Z]{2,30}\b){0,3}%";
	//$streetPattern = "%\d{1,5}(\s+\b[a-zA-Z]{3,30}\b){1,3}%"; // Just looking for a street address
	//$postcodePattern = "%\d{1-5}%"; // Just looking for a postcode
	//$suburbPattern = "%\s+[a-zA-Z]{3,30}%"; // Just looking for a suburb or country of any kind
	$lineNo = 0;
	
	//extract for each line in the CSV
	foreach ($addresses as $address) {
		$split = NULL;
		if (preg_match($addressPattern, $address)) {
			$split = preg_split($addressPattern, $address);
			// echo $address . " is a full address <BR>";
		// }
		// else if (preg_match($streetPattern, $address)) {
			// $split = preg_split($streetPattern, $address);
			// echo $address . " is a street address <BR>";
		// }
		// else if (preg_match($postcodePattern, $address)) {
			// $split = preg_split($postcodePattern, $address);
			// echo " is a postcode <BR>";
		// }
		// else if (preg_match($suburbPattern, $address)) {
			// $split = preg_split($suburbPattern, $address);
			// echo " is a suburb <BR>";
		// 
	}else {
			//echo "<br>No addresses could be found on line " . $lineNo;
			$addressFound = False;
		}
		$singleAddress = true;
		$pre;
		$post;
		if (is_array($split)) {
			if (count($split) > 2) {
				//echo "More than one address found in line " . $lineNo;
				$singleAddress = False;
			}	
			if (count($split) >= 1) {
				$pre = $split[0];
				$address = str_replace($pre, "", $address);
			}
		}
	
	if($singleAddress && $addressFound){
		
			//set variable for the granularity the user wants
			$granularity = $_POST['grain']; //@options: exact, street, suburb
			if(is_null($granularity)) $granularity = "exact";
			$inDatabase = false;
			
			
			$number = null;
			$street = null;
			$suburb = null;
			$state = null;
			$country = null;
			$postcode = null;
			$lat = null;
			$lng = null;
			
			if($granularity == "exact"){
				//search for the same search
				$row = mysql_fetch_row(mysql_query('SELECT ADDRESS FROM ADDRESSES WHERE ADDRESS = "' . $address . '"'));
				//echo $addressComparrison = $row[0];

				if($address == $addressComparrison){
					$row = mysql_fetch_row(mysql_query('SELECT NUMBER, STREET, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG FROM ADDRESSES WHERE ADDRESS = "' . $address . '"'));
					if(isset($row[0]))
						$number = print_r($row["0"], TRUE);
					if(isset($row[1]))
						$street = print_r($row["1"], TRUE);
					if(isset($row[2]))
						$suburb = print_r($row["2"], TRUE);
					$state = print_r($row["3"], TRUE);
					$country = print_r($row["4"], TRUE);
					if(isset($row[5]))
						$postcode = print_r($row["5"], TRUE);
					$lat = print_r($row["6"], TRUE);
					$lng = print_r($row["7"], TRUE);
					
					$inDatabase = true;
					
					//echo print_r($row);
				}
			}
			if ($granularity === "street"){
				// work out the average of all the current geo points
				$numbered = true;
				$counter = 0;

				while($numbered && $counter <= 6){
					if(!(is_numeric($address[$counter])))
						$numbered = false;
					$counter += 1;
				}
				$lessNo = substr($address, $counter);
				$lessNo = strtolower($lessNo);
				$lessNo = str_replace(", ", ",", $lessNo);
				$lessNo = str_replace(" rd,", " road,", $lessNo);
				$lessNo = str_replace(" st,", " street,", $lessNo);
				$lessNo = str_replace(" pl,", " place,", $lessNo);
				$lessNo = str_replace(" ave,", " avenue,", $lessNo);
				$lessNo = str_replace(" blvd,", " boulevard,", $lessNo);
				$lessNo = str_replace(" esp,", " esplanade,", $lessNo);
				$lessNo = str_replace(" dr,", " drive,", $lessNo);
				$lessNo = str_replace(" pky,", " parkway,", $lessNo);
				$lessNo = str_replace(" pl,", " place,", $lessNo);
				$lessNo = str_replace(" ave,", " avenue,", $lessNo);
				$components = explode(",", $lessNo);
			
				
				$row = mysql_fetch_row(mysql_query('SELECT AVG(LAT) as LAT FROM ADDRESSES WHERE LOWER(STREET) = "' . $components[0] . '" AND LOWER(SUBURB) = "' . $components[1] . '"'));
				$lat = $row[0];
				$row = mysql_fetch_row(mysql_query('SELECT AVG(LNG) as LNG FROM ADDRESSES WHERE LOWER(STREET) = "' . $components[0] . '" AND LOWER(SUBURB) = "' . $components[1] . '"'));
				$lng = $row[0];
				
				if (isset($lat)){
					$inDatabase = true;
					$row = mysql_fetch_row(mysql_query('SELECT STREET, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG FROM ADDRESSES WHERE LOWER(STREET) = "' . $components[0] . '" AND LOWER(SUBURB) = "' . $components[1] . '"'));
					$street = print_r($row["0"], TRUE);
					$suburb = print_r($row["1"], TRUE);
					$state = print_r($row["2"], TRUE);
					$country = print_r($row["3"], TRUE);
					if(isset($row[4]))
						$postcode = print_r($row["4"], TRUE);
				}
				
				
			}else if ($granularity === "suburb"){
				// work out the average of all the current geo points
				$numbered = true;
				$counter = 0;
				while($numbered && $counter <= 6){
					if(!(is_numeric($address[$counter])))
						$numbered = false;
					$counter += 1;
				}
				$lessNo = substr($address, $counter);
				$lessNo = str_replace(", ", ",", $lessNo);
				$lessNo = strtolower($lessNo);
				$components = explode(",", $lessNo);
				//echo "<br> suburb = " . $components[1];
				$row = mysql_fetch_row(mysql_query('SELECT AVG(LAT) as LAT FROM ADDRESSES WHERE LOWER(SUBURB) = "' . $components[1] . '"'));
				$lat = $row[0];
				$row = mysql_fetch_row(mysql_query('SELECT AVG(LNG) as LNG FROM ADDRESSES WHERE LOWER(SUBURB) = "' . $components[1] . '"'));
				$lng = $row[0];
				
				if (isset($lat)){
					$inDatabase = true;
					$row = mysql_fetch_row(mysql_query('SELECT SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG FROM ADDRESSES WHERE LOWER(SUBURB) = "' . $components[1] . '"'));
					$suburb = print_r($row["0"], TRUE);
					$state = print_r($row["1"], TRUE);
					$country = print_r($row["2"], TRUE);
					if(isset($row[3]))
						$postcode = print_r($row["3"], TRUE);
				}
				
			}else{

			}
			
			
			
			


			//if not in the database already
			if(!$inDatabase)	{
				
				//run API
				$result = $geocode->getLocation($address);
				$ArraySize = count($result["address_components"]);

				
				//assign variables			
				for ($counter = 0; $counter < $ArraySize; $counter++)	{
					if($result["address_components"][$counter]["types"]["0"] === "street_number")
						$number = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "route")
						$street = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "locality")
						$suburb = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "administrative_area_level_2")
						$state = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "administrative_area_level_1")
						$state = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "country")
						$country = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "postal_code")
						$postcode = print_r($result["address_components"][$counter]["long_name"], TRUE);
					if($result["address_components"][$counter]["types"]["0"] === "postal_code_prefix")
						$postcode = print_r($result["address_components"][$counter]["long_name"], TRUE);

					
				}
				$lat = print_r($result["geometry"]["location"]["lat"], TRUE);
				$lng = print_r($result["geometry"]["location"]["lng"], TRUE);

				//make query
				if(is_null($number) && isset($street) && isset($postcode)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, STREET, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', "' . $street . '", "' . $suburb . '", "' . $state . '", "
							' . $country . '", "' . $postcode . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}else if (is_null($street) && isset ($suburb) && isset($postcode)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', "' . $suburb . '", "' . $state . '", "
							' . $country . '", "' . $postcode . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}else if (is_null($suburb)){
					$query = 'insert into invalid_addresses (BATCH_NO, address) values (' . $BatchNo . ', "' . $address . '")';
					//echo "<br><br> <h3> address too vague, will not be added to database</h3><br><br>";
				}else if (is_null($postcode) && isset($number) && isset($street)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, NUMBER, STREET, SUBURB, STATE, COUNTRY, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', ' . $number . ', "' . $street . '", "' . $suburb . '", "' . $state . '", "
							' . $country . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}else if (is_null($postcode) && is_null($number) && isset($street)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, STREET, SUBURB, STATE, COUNTRY, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', "' . $street . '", "' . $suburb . '", "' . $state . '", "
							' . $country . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}else if (is_null($postcode) && is_null($number) && is_null($street)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, SUBURB, STATE, COUNTRY, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', "' . $suburb . '", "' . $state . '", "
							' . $country . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				} else	{
					$query = 'INSERT INTO Addresses ( BATCH_NO, NUMBER, STREET, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', ' . $number . ', "' . $street . '", "' . $suburb . '", "' . $state . '", "
							' . $country . '", "' . $postcode . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}
			
				if (mysql_query($query) === TRUE) {
					//echo 'results added to database';
				} else {
					echo die('error adding record to addresses ' . mysql_error());
					if(mysql_query('insert into invalid_addresses (BATCH_NO, address) values (' . $BatchNo . ', "' . $address . '")') === false){
						echo die('error adding record to invalid_addresses ' . mysql_error());
					}
				}
			}else {
				
				//condition if in the database already
				if(is_null($number) && isset($street)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, STREET, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', "' . $street . '", "' . $suburb . '", "' . $state . '", "
							' . $country . '", "' . $postcode . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}else if (is_null($street) && isset ($suburb)){
					$query = 'INSERT INTO Addresses ( BATCH_NO, SUBURB, STATE, COUNTRY, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', "' . $suburb . '", "' . $state . '", "
							' . $country . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}else	{
					$query = 'INSERT INTO Addresses ( BATCH_NO, NUMBER, STREET, SUBURB, STATE, COUNTRY, POSTCODE, LAT, LNG, PRE, ADDRESS)
							VALUES (' . $BatchNo . ', ' . $number . ', "' . $street . '", "' . $suburb . '", "' . $state . '", "
							' . $country . '", "' . $postcode . '", ' . $lat . ', ' . $lng . ',"' . $pre . '","' . $address . '")';
				}
				if (mysql_query($query) === TRUE) {
					//echo 'results added to database';
				} else {
					echo die('error adding record to addresses ' . mysql_error());
					if(mysql_query('insert into invalid_addresses (BATCH_NO, address) values (' . $BatchNo . ', "' . $address . '")') === false){
						echo die('error adding record to invalid_addresses ' . mysql_error());
					}
				}

			}
			$lineNo += 1;
			$address = null;
		}else if(!$singleAddress){
			//echo "<br> Multiple Addresses found in line " . $lineNo;
			$lineNo += 1;
			$singleAddress = true;
			$addressFound = true;
		}else{
			//echo "<br> No addresses found in line " . $lineNo;
			$lineNo += 1;
			$singleAddress = true;
			$addressFound = true;
		}
	}

		
}else {
	echo "<H3> Ooops! You shouldn't be on this page since you didn't submit a file <H3> ";
}



?>
<html>
	<body bgcolor=#f0f0f0>
	
	
	
	
	</body>
</html>
