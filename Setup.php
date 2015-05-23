<?PHP
mysql_connect ('localhost', 'root', 'toor');
mysql_select_db( 'EYTool' );

//CREATE TABLE


$sql1 = 	"CREATE TABLE ADDRESSES ( 
ID INT NOT NULL AUTO_INCREMENT, 
BATCH_NO INT NOT NULL, 
NUMBER INT,
STREET VARCHAR(80), 
SUBURB VARCHAR(80), 
STATE VARCHAR(80), 
COUNTRY VARCHAR(80), 
POSTCODE VARCHAR(10), 
LAT FLOAT NOT NULL,
LNG FLOAT NOT NULL,
PRE VARCHAR(160),
ADDRESS VARCHAR (160), 
PRIMARY KEY ( ID ));";

$sql2 = "CREATE TABLE INVALID_ADDRESSES ( 
ID INT NOT NULL AUTO_INCREMENT, 
BATCH_NO INT NOT NULL, 
ADDRESS VARCHAR(160), 
PRE VARCHAR(160), 
PRIMARY KEY ( ID ));";


//run query and output result
if (mysql_query($sql1) && mysql_query($sql2) === TRUE) {
	echo "<br><h3 align=center>Setup completed properly";
} else {
	echo die("Error creating table: " . mysql_error());
}

?>
