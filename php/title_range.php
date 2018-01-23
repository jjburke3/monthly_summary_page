<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}



$sql = "
select CONCAT(DATE(DATE_FORMAT(DATE_SUB(max(DATA_AS_OF),INTERVAL 2 MONTH),'%Y%m01')),' to ',MAX(DATA_AS_OF)) AS TITLE_LABLE 
from jburke.MONTHLY_PRICING_MEETING".$_GET['range']."
";
$result = mysqli_query($conn,$sql);
$data = array();



for ($x = 0; $x < mysqli_num_rows($result); $x++) {
	$data[] = mysqli_fetch_assoc($result);
}

echo json_encode($data);
/*
if(mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			echo "PROGRAM: " . $row["PROGRAM"]. " - QUOTES: " . $row["QUOTES"]. "<br>";
		}
	} else {
	echo "0 results";
}


*/

mysqli_close($conn);
?>