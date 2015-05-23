<?PHP

// Attempt MySQL server connection.
$link = mysql_connect('localhost', 'root', 'toor');

//Check connection, throw error if you cannot connect
if(!$link){
	echo "connection failed";
    die("ERROR: Could not connect. " . mysql_error());
} else{
	//assign table count
	$BatchNo = $_POST['number'];
	
	//select database
	mysql_select_db( 'EYTool' );
	
}
// return the users address with the geocode
$query = 'SELECT CONCAT(PRE, ADDRESS), LAT, LNG, CONCAT(CONCAT(NUMBER, " "), STREET), SUBURB, COUNTRY, POSTCODE INTO OUTFILE "C:/Apache24/htdocs/downloads/' . $BatchNo . '.csv"
	FIELDS TERMINATED BY "," OPTIONALLY ENCLOSED BY "" escaped by ""
	LINES TERMINATED BY ";\r\n" 
	FROM ADDRESSES WHERE BATCH_NO = ' . $BatchNo;

	if(mysql_query($query) === true){
	//	echo "true";
		echo "<br><h3 align=center>You're downloading  the results from batch " . $BatchNo;
		echo "<br> Please click <a href=/downloads/" . $BatchNo . ".csv download>here</a> to download your results as a CSV";
	}else{
		$query = 'SELECT CONCAT(PRE, ADDRESS), LAT, LNG INTO OUTFILE "C:/Apache24/htdocs/downloads/' . $BatchNo . '(1).csv"
			FIELDS TERMINATED BY "," OPTIONALLY ENCLOSED BY "" escaped by ""
			LINES TERMINATED BY ";\r\n" 
			FROM ADDRESSES WHERE BATCH_NO = ' . $BatchNo;
		mysql_query($query);
		echo "<br><h3 align=center>You're downloading  the results from batch " . $BatchNo;
		echo "<br> Please click <a href=/downloads/" . $BatchNo . "(1).csv download>here</a> to download your results as a CSV";
	}


	
?>

<html>
	<body bgcolor=#f0f0f0>
	
	
	
	
	</body>
</html>
