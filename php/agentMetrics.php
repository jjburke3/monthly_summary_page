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
$sql = "select concat(round(avg(CASE WHEN METRIC = 'AGENT AGE' THEN METRIC_NUMBER END),0),' days') AS 'Agent Age',
concat(round(100*IFNULL(SUM(CASE WHEN METRIC = '7 MONTHS INFORCE' THEN METRIC_NUMBER END),0)
	/SUM(CASE WHEN METRIC = '6 MONTHS INFORCE' THEN METRIC_NUMBER END),1),'%') AS '7/6 Ratio',
concat(round(100*IFNULL(sum(case when METRIC = 'EFT POLICIES' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'EFT %',
concat(round(100*IFNULL(sum(case when METRIC = 'LATE APPS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Late App %',
concat(round(100*IFNULL(sum(case when METRIC = 'Cell Numbers' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Cell %',
concat(round(100*IFNULL(sum(case when METRIC = 'EMAILS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Email %',
concat(round(100*IFNULL(sum(case when METRIC = 'NOTIFICATIONS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Notification %',
concat(round(100*IFNULL(sum(case when METRIC = 'MMs' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'MM %',
concat(round(100*IFNULL(sum(case when METRIC = '60 DAY NOTICES' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'UW Cancellation %',
concat(round(IFNULL(sum(case when METRIC = 'AGENT CALLS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'PIF' then METRIC_NUMBER end),3)) as 'Calls per PIF'
from jburke.AGENT_METRICS
where AGENT_NUMBER = ".$_GET['agentNumber']."
and REPORT_DATE = date_sub(date_format(curdate(),'%Y%m01'), interval 1 day)


union all
select concat(round(avg(CASE WHEN METRIC = 'AGENT AGE' THEN METRIC_NUMBER END),0),' days') AS 'Agent Age',
concat(round(100*IFNULL(SUM(CASE WHEN METRIC = '7 MONTHS INFORCE' THEN METRIC_NUMBER END),0)
	/SUM(CASE WHEN METRIC = '6 MONTHS INFORCE' THEN METRIC_NUMBER END),1),'%') AS '7/6 Ratio',
concat(round(100*IFNULL(sum(case when METRIC = 'EFT POLICIES' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'EFT %',
concat(round(100*IFNULL(sum(case when METRIC = 'LATE APPS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Late App %',
concat(round(100*IFNULL(sum(case when METRIC = 'Cell Numbers' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Cell %',
concat(round(100*IFNULL(sum(case when METRIC = 'EMAILS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Email %',
concat(round(100*IFNULL(sum(case when METRIC = 'NOTIFICATIONS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'Notification %',
concat(round(100*IFNULL(sum(case when METRIC = 'MMs' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'MM %',
concat(round(100*IFNULL(sum(case when METRIC = '60 DAY NOTICES' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'NEW BUSINESS' then METRIC_NUMBER end),1),'%') as 'UW Cancellation %',
concat(round(IFNULL(sum(case when METRIC = 'AGENT CALLS' then METRIC_NUMBER end),0)
	/sum(case when METRIC = 'PIF' then METRIC_NUMBER end),3)) as 'Calls per PIF'
from jburke.AGENT_METRICS
where AGENT_ACTIVE = 'Y'
and REPORT_DATE = date_sub(date_format(curdate(),'%Y%m01'), interval 1 day)";
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