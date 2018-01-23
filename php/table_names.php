<?php
if($_GET['state'] == "FL"){
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
	select * from jburke.MONTHLY_PRICING_MEETING_TABLES
	";
	$result = mysqli_query($conn,$sql);
	$data = array();

	for ($x = 0; $x < mysqli_num_rows($result); $x++) {
		$data[] = mysqli_fetch_assoc($result);
	}

	echo json_encode($data);

	mysqli_close($conn);
}
elseif($_GET['state']=="TX"){
	$servername = "";
	$username = "";
	$password = "";
	$dbname = "";

	// Create connection
	$conn = odbc_connect("DRIVER={SQL Server};Server=$servername;Database=$dbname;", $username, $password);

	// Check connection
	if (!$conn) {
		die("Connection failed: " . odbc_error());
	}

	$sql = "
	select * from Periscope_Data.dbo.MONTHLY_PRICING_MEETING_TABLES
	";
	$result = odbc_exec($conn,$sql);
	$data = array();

	for ($x = 0; $x < odbc_num_rows($result); $x++) {
		$data[] = odbc_fetch_array($result);
	}

	echo json_encode($data);

	odbc_close($conn);
}
?>