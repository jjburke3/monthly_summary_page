<?php

$state_value = $_GET['state'];
$results = $_GET['results'];
$type = $_GET['type'];
$ranking = $_GET['ranking'];
$range = $_GET['range'];
$program = $_GET['program'];
$page = $_GET['page'];
$renew = $_GET['renew'];

$schema = ($state_value=="FL"?"jburke.":"Periscope_Data.dbo.");
$pip = $_GET['pip'];
$pd = $_GET['pd'];
$bi = $_GET['bi'];
$cmp = $_GET['cmp'];
$col = $_GET['col'];
$ranking2 = $_GET['ranking2'];
$clutch = $_GET['clutch'];
$minprem = $_GET['minprem'];
$cov = $_GET['cov'];
$onlevel = ($_GET['onlevel']=="ON"?"OL_":"");
$tableName = "MONTHLY_PRICING_MEETING".$range;
$rangeDate = (int)substr($range,1);

$servername = "";
$username = "";
$password = "";
$dbname = "";

if($_GET['state'] == "FL"){

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
}
elseif($_GET['state'] == "TX"){
	
	//create an instance of the  ADO connection object
	$conn = new COM ("ADODB.Connection")
	  or die("Cannot start ADO");

	//define connection string, specify database driver
	$connStr = "PROVIDER=SQLOLEDB;SERVER=".$servername.";UID=".$username.";PWD=".$password.";DATABASE=".$dbname; 
	  $conn->open($connStr); //Open the connection to the database

	
}

$renewWhere = (($rangeDate > 2017028 or $range == "_temp") and $renew != "%")?"and NB_RN like '%".$renew."%' ":"";

switch($cov){
	case "PIP":
		$covSev = $pip;
		break;
	case "PD":
		$covSev = $pd;
		break;
	case "BI":
		$covSev = $bi;
		break;
	case "CMP":
		$covSev = $cmp;
		break;
	case "COL":
		$covSev = $col;
		break;
}

switch($cov){
	case "ALL":
		$premium_calc = "coalesce(sum(".$onlevel."EARNED_PREMIUM_PIP),0)+coalesce(sum(".$onlevel."EARNED_PREMIUM_PD),0)
			+COALESCE(SUM(".$onlevel."EARNED_PREMIUM_BI),0)+COALESCE(SUM(".$onlevel."EARNED_PREMIUM_CMP),0)
			+COALESCE(SUM(".$onlevel."EARNED_PREMIUM_COL),0)";
		
		$loss_count = "coalesce(sum(Q1_PIP),0)+coalesce(sum(Q1_PD),0)
			+coalesce(sum(Q1_BI),0)+coalesce(sum(Q1_CMP),0)+coalesce(sum(Q1_COL),0)";
			
		$loss_amount = "coalesce(sum(Q1_PIP*PIP_FACTOR),0)*".$pip."+coalesce(sum(Q1_PD*PD_FACTOR),0)*".$pd."
			+coalesce(sum(Q1_BI*BI_FACTOR),0)*".$bi."+coalesce(sum(Q1_CMP*CMP_FACTOR),0)*".$cmp."+coalesce(sum(Q1_COL*COL_FACTOR),0)*".$col."";
		
		$q2_premium_calc = "coalesce(sum(".$onlevel."Q2_EARNED_PREMIUM_PIP),0)+coalesce(sum(".$onlevel."Q2_EARNED_PREMIUM_PD),0)
			+COALESCE(SUM(".$onlevel."Q2_EARNED_PREMIUM_BI),0)+COALESCE(SUM(".$onlevel."Q2_EARNED_PREMIUM_CMP),0)
			+COALESCE(SUM(".$onlevel."Q2_EARNED_PREMIUM_COL),0)";
		
		$q2_loss_count = "coalesce(sum(Q2_PIP),0)+coalesce(sum(Q2_PD),0)
			+coalesce(sum(Q2_BI),0)+coalesce(sum(Q2_CMP),0)+coalesce(sum(Q2_COL),0)";
			
		$q2_loss_amount = "coalesce(sum(Q2_PIP*PIP_FACTOR),0)*".$pip."+coalesce(sum(Q2_PD*PD_FACTOR),0)*".$pd."
			+coalesce(sum(Q2_BI*BI_FACTOR),0)*".$bi."+coalesce(sum(Q2_CMP*CMP_FACTOR),0)*".$cmp."+coalesce(sum(Q2_COL*COL_FACTOR),0)*".$col."";
			
		$q3_premium_calc = "coalesce(sum(".$onlevel."Q3_EARNED_PREMIUM_PIP),0)+coalesce(sum(".$onlevel."Q3_EARNED_PREMIUM_PD),0)
			+COALESCE(SUM(".$onlevel."Q3_EARNED_PREMIUM_BI),0)+COALESCE(SUM(".$onlevel."Q3_EARNED_PREMIUM_CMP),0)
			+COALESCE(SUM(".$onlevel."Q3_EARNED_PREMIUM_COL),0)";
			
		$year_premium_calc = "coalesce(sum(".$onlevel."YEAR_EARNED_PREMIUM_PIP),0)+coalesce(sum(".$onlevel."YEAR_EARNED_PREMIUM_PD),0)
			+COALESCE(SUM(".$onlevel."YEAR_EARNED_PREMIUM_BI),0)+COALESCE(SUM(".$onlevel."YEAR_EARNED_PREMIUM_CMP),0)
			+COALESCE(SUM(".$onlevel."YEAR_EARNED_PREMIUM_COL),0)";
		
		$year_loss_count = "coalesce(sum(YEAR_PIP),0)+coalesce(sum(YEAR_PD),0)
			+coalesce(sum(YEAR_BI),0)+coalesce(sum(YEAR_CMP),0)+coalesce(sum(YEAR_COL),0)";
			
		$year_loss_amount = "coalesce(sum(YEAR_PIP*PIP_FACTOR),0)*".$pip."+coalesce(sum(YEAR_PD*PD_FACTOR),0)*".$pd."
			+coalesce(sum(YEAR_BI*BI_FACTOR),0)*".$bi."+coalesce(sum(YEAR_CMP*CMP_FACTOR),0)*".$cmp."+coalesce(sum(YEAR_COL*COL_FACTOR),0)*".$col."";
			
		$incur_loss_amount = "coalesce(sum(YEAR_PIP_INCUR),0)+coalesce(sum(YEAR_PD_INCUR),0)
			+coalesce(sum(YEAR_BI_INCUR),0)+coalesce(sum(YEAR_CMP_INCUR),0)+coalesce(sum(YEAR_COL_INCUR),0)";
			
		break;
	default:
		$premium_calc = "coalesce(sum(".$onlevel."EARNED_PREMIUM_".$cov."),0)";
		
		$loss_count = "coalesce(sum(Q1_".$cov."),0)";
			
		$loss_amount = "coalesce(sum(Q1_".$cov."*".$cov."_FACTOR),0)*".$covSev."";
		
		$q2_premium_calc = "coalesce(sum(".$onlevel."Q2_EARNED_PREMIUM_".$cov."),0)";
		
		$q2_loss_count = "coalesce(sum(Q2_".$cov."*".$cov."_FACTOR),0)";
			
		$q2_loss_amount = "coalesce(sum(Q2_".$cov."),0)*".$covSev."";
			

			
		$q3_premium_calc = "coalesce(sum(".$onlevel."Q3_EARNED_PREMIUM_".$cov."),0)";
			
		$year_premium_calc = "coalesce(sum(".$onlevel."YEAR_EARNED_PREMIUM_".$cov."),0)";
		
		$year_loss_count = "coalesce(sum(YEAR_".$cov."),0)";
			
		$year_loss_amount = "coalesce(sum(YEAR_".$cov."*".$cov."_FACTOR),0)*".$covSev."";
			
		$incur_loss_amount = "coalesce(sum(YEAR_".$cov."_INCUR),0)";
			

}
	
$cred = "case when ".$loss_count." < 5 then 0 when sqrt(cast(".$loss_count." as decimal)/1084) > 1 then 1 
	else sqrt(cast(".$loss_count." as decimal)/1084) end";	

$q2_cred = "case when ".$q2_loss_count." < 5 then 0 when sqrt(cast(".$q2_loss_count." as decimal)/1084) > 1 then 1 
	else sqrt(cast(".$q2_loss_count." as decimal)/1084) end";	
	
$year_cred = "case when ".$year_loss_count." < 5 then 0 when sqrt(cast(".$year_loss_count." as decimal)/1084) > 1 then 1 
	else sqrt(cast(".$year_loss_count." as decimal)/1084) end";

$loss_ratio = "case when coalesce(".$premium_calc.",0) = 0 then null 
	else cast(".$loss_amount." as decimal)/(".$premium_calc.") end";

$cred_loss_ratio = "(".$loss_ratio.")*(".$cred.") + 
(select ".$loss_ratio." from ".$schema.$tableName."
where TYPE = '".$type."'
		and PROGRAM = '".$program."'
		and STATE = '".$state_value."'
		".$renewWhere.")
*(1-(".$cred."))";

$q2_loss_ratio = "case when coalesce(".$q2_premium_calc.",0) = 0 then null 
	else cast(".$q2_loss_amount." as decimal)/(".$q2_premium_calc.") end";

$q2_cred_loss_ratio = "(".$q2_loss_ratio.")*(".$q2_cred.") + 
(select ".$q2_loss_ratio." from ".$schema.$tableName."
where TYPE = '".$type."'
		and PROGRAM = '".$program."'
		and STATE = '".$state_value."'
		".$renewWhere.")
*(1-(".$q2_cred."))";

$year_loss_ratio = "case when coalesce(".$year_premium_calc.",0) = 0 then null 
	else cast(".$year_loss_amount." as decimal)/(".$year_premium_calc.") end";

$year_cred_loss_ratio = "(".$year_loss_ratio.")*(".$year_cred.") + 
(select ".$year_loss_ratio." from ".$schema.$tableName."
where TYPE = '".$type."'
		and PROGRAM = '".$program."'
		and STATE = '".$state_value."'
		".$renewWhere.")
*(1-(".$year_cred."))";

$incur_loss_ratio = "cast(".$incur_loss_amount." as decimal)/(".$year_premium_calc.")";

$hit_ratio = "case when coalesce(sum(QUOTE_COUNT),0) = 0 then null 
	else cast(sum(SALES_COUNT) as decimal)/cast(sum(QUOTE_COUNT) as decimal) end";

$growth = "(".$premium_calc.")-(".$q2_premium_calc.")";

$q2_hit_ratio = "case when coalesce(sum(Q2_QUOTE_COUNT),0) = 0 then null 
	else cast(sum(Q2_SALES_COUNT) as decimal)/cast(sum(Q2_QUOTE_COUNT) as decimal) end";

$q2_growth = "(".$q2_premium_calc.")-(".$q3_premium_calc.")";

$tick = ($state_value=="TX"?"'":"`");

switch($ranking) {
	case "premium":
		$order_by = $premium_calc." desc";
		$order_by2 = " ".$tick."Earned Premium".$tick." desc";
		$order_by3 = " EARNED_PREMIUM desc ";
		$mapColor = "GREEN";
		break;
	case "worst_perform":
		$order_by = $cred_loss_ratio." desc";
		$order_by2 = " ".$tick."Credible Loss Ratio".$tick." desc";
		$order_by3 = " CRED_LOSS_RATIO desc ";
		$mapColor = "RED";
		break;
	case "best_perform":
		$order_by = $cred_loss_ratio." asc";
		$order_by2 = " ".$tick."Credible Loss Ratio".$tick." asc";
		$order_by3 = " CRED_LOSS_RATIO asc ";
		$mapColor = "GREEN";
		break;
	case "grow":
		$order_by = $growth." desc";
		$order_by2 = " ".$tick."Premium Growth".$tick." desc";
		$order_by3 = " GROW desc ";
		$mapColor = "GREEN";
		break;
	case "shrink":
		$order_by = $growth." asc";
		$order_by2 = " ".$tick."Premium Growth".$tick." asc";
		$order_by3 = " GROW asc ";
		$mapColor = "RED";
		break;
	case "high_hit_ratio":
		$order_by = $hit_ratio." desc";
		$order_by2 = " ".$tick."Hit Ratio".$tick." desc";
		$order_by3 = " HIT_RATIO desc ";
		$mapColor = "GREEN";
		break;
	case "low_hit_ratio":
		$order_by = $hit_ratio." asc";
		$order_by2 = " ".$tick."Hit Ratio".$tick." asc";
		$order_by3 = " HIT_RATIO asc ";
		$mapColor = "RED";
		break;
}

switch($clutch) {
	case "ALL":
		$clutch_sql = "";
		break;
	case "NON":
		$clutch_sql = " and CLUTCH like '%No Clutch%' ";
		break;
	case "CLUTCH":
		$clutch_sql = " and (CLUTCH like '%Online%' or CLUTCH like '%Offline%') ";
		break;
	case "ONLINE":
		$clutch_sql = " and CLUTCH like '%Online%' ";
		break;
	case "OFFLINE":
		$clutch_sql = " and CLUTCH like '%Offline%' ";
		break;
	case "LEADS":
		$clutch_sql = " and CLUTCH like '%Lead%' ";
		break;
	case "NATURAL":
		$clutch_sql = " and CLUTCH like '%Natural%' ";
		break;
}

$sql = "select "
		.($state_value=="TX"?"top(".$results.")":"")
		."GROUPING
	from "
		.$schema
		.$tableName."
	where TYPE = '".$type."'
		and PROGRAM = '".$program."'
		and STATE = '".$state_value."'
		".$renewWhere."
		".$clutch_sql."
	group by GROUPING
	having ".$premium_calc." >= ".$minprem."
	".(($ranking == "high_hit_ratio" or $ranking == "low_hit_ratio")?" and sum(QUOTE_COUNT) > 100 ":"")."
	order by ".$order_by."
		".($state_value=="FL"?"Limit ".$results:"")."" ;


if($_GET['state'] == "FL"){
	$result = mysqli_query($conn,$sql);
	$data = array();



	for ($x = 0; $x < mysqli_num_rows($result); $x++) {
		$data[] = mysqli_fetch_assoc($result);
	}
}
elseif($_GET['state']=="TX"){
	$rs = $conn->execute($sql);
	$data = array();



	$num_columns = $rs->Fields->Count();


	for ($i=0; $i < $num_columns; $i++) {
		$arrColumns[] = $rs->Fields($i);
		$newArr[] = $rs->Fields($i)->name;
	}

	while (!$rs->EOF)  //carry on looping through while there are records
	{
		$arrRow = array();
		for($i=0; $i < $num_columns; $i++) {
			$arrRow[$newArr[$i]] = $arrColumns[$i]->value;
		}
		$data[] = $arrRow;
		$rs->MoveNext(); //move on to the next record
	}
}
$data = json_encode($data);
$in_statement = str_replace(chr(34),chr(39),
	str_replace("[","",str_replace("]","",str_replace("}","",str_replace("{".chr(34)."GROUPING".chr(34).":","",$data)))));


	
switch($page){
	case "main":
		$joinAdd = "";
		$selectAdd = "";
		$selectAdd2 = "";
		if($state_value == "FL" and $type == "ZIP"){
			$joinAdd = "left join (SELECT ZIP_CODE,
				SUBSTRING_INDEX(CAST(GROUP_CONCAT(CASE WHEN PROGRAM = 'WIN' THEN TERRITORY END SEPARATOR '|') AS CHAR),'|',1) AS WIN_TERR,
				SUBSTRING_INDEX(CAST(GROUP_CONCAT(CASE WHEN PROGRAM = 'OPT' THEN TERRITORY END SEPARATOR '|') AS CHAR),'|',1) AS OPT_TERR,
				SUBSTRING_INDEX(CAST(GROUP_CONCAT(CASE WHEN PROGRAM = 'SEL' THEN TERRITORY END SEPARATOR '|') AS CHAR),'|',1) AS SEL_TERR,
				SUBSTRING_INDEX(CAST(GROUP_CONCAT(CASE WHEN PROGRAM = 'ICN' THEN TERRITORY END SEPARATOR '|') AS CHAR),'|',1) AS ICN_TERR
				from pricing_data.rating_zip_codes
				WHERE EFFECTIVE_DATE = 
					CASE PROGRAM
						WHEN 'WIN' THEN (SELECT MAX(EFFECTIVE_DATE) FROM pricing_data.rating_zip_codes WHERE PROGRAM = 'WIN')
						WHEN 'OPT' THEN (SELECT MAX(EFFECTIVE_DATE) FROM pricing_data.rating_zip_codes WHERE PROGRAM = 'OPT')
						WHEN 'SEL' THEN (SELECT MAX(EFFECTIVE_DATE) FROM pricing_data.rating_zip_codes WHERE PROGRAM = 'SEL')
						WHEN 'ICN' THEN (SELECT MAX(EFFECTIVE_DATE) FROM pricing_data.rating_zip_codes WHERE PROGRAM = 'ICN')
					END
				GROUP BY 1) a on ZIP_CODE = GROUPING
				left join (SELECT ZIPMST_ZIP_CODE1, ZIPMST_COUNTY, ZIPMST_CITY
				FROM OSIS.ZIPMST
				GROUP BY 1) zips ON ZIPMST_ZIP_CODE1 = GROUPING";
			$selectAdd = ", ZIPMST_COUNTY as 'County', ZIPMST_CITY as 'City', 
				WIN_TERR as 'WIN Terr', SEL_TERR as 'SEL Terr', OPT_TERR as 'OPT Terr', ICN_TERR as 'ICN Terr'";
		}
		elseif($state_value == "FL" and $type == "AGENT"){
			$joinAdd = "left join OSIS.AGENT ON AGENT_NUMBER = GROUPING ";
			$selectAdd = ", trim(AGENT_NAME1) as 'Agent Name', trim(AGENT_PHY_COUNTY) as 'Agent County',
							AGENT_PHY_ZIP1 as 'Agent Zip', trim(AGENT_MKT_REP) as 'Agent Rep'";
			$selectAdd2 = $incur_loss_ratio." as '1 Year Incurred Loss Ratio',";
		}
		elseif($state_value == "TX" and $type == "AGENT"){
			$joinAdd = "left join Windhaven_Report.dbo.producer on concat(code,'-',subcode) = GROUPING ";
			$selectAdd = ", ltrim(rtrim(producerName)) as 'Agent Name', ltrim(rtrim(county)) as 'Agent County', 
					zip as 'Agent Zip', null as 'Agent Rep' ";
			$selectAdd2 = $incur_loss_ratio." as '1 Year Incurred Loss Ratio',";
		}
		$selectMain = $premium_calc." as 'Earned Premium',
				".$loss_count." as 'Loss Count',
				".$hit_ratio." as 'Hit Ratio',
				coalesce(sum(QUOTE_COUNT),0) as 'Quote Count',
				".$loss_ratio." as 'Loss Ratio',
				".$cred." as 'Credibility',
				".$cred_loss_ratio." as 'Credible Loss Ratio',
				".(($rangeDate > 20161031 or $rangeDate == "")?($year_cred_loss_ratio." as '1 Year Loss Ratio',"):"")."
				".(($rangeDate > 20161231 or $rangeDate == "")?$selectAdd2:"")."
				".$growth." as 'Premium Growth'";
		$sql = "select b.*".$selectAdd." from (select * from (select  GROUPING, 
				".$selectMain."
				from ".$schema.$tableName."
				where GROUPING in (".$in_statement.") 
					and TYPE = '".$type."'
					and PROGRAM = '".$program."'
					and STATE = '".$state_value."'
					".$renewWhere."
					".$clutch_sql."
				group by GROUPING) b
				union all
				select concat('Other ',count(distinct(GROUPING)),' ',
				case '".$type."' when 'AGENT' then 'Agents' when 'ZIP' then 'Zips' 
				when 'COUNTY' then 'Counties' when 'TERRITORY' then 'Territories' end) as GROUPING, 
				".$selectMain."
				from ".$schema.$tableName."
				where GROUPING not in (".$in_statement.") 
					and TYPE = '".$type."'
					and PROGRAM = '".$program."'
					and STATE = '".$state_value."'
					".$renewWhere."
					".$clutch_sql."
				union all
				select 'Total' as GROUPING, 
				".$selectMain."
				from ".$schema.$tableName."
				where TYPE = '".$type."'
					and PROGRAM = '".$program."'
					and STATE = '".$state_value."'
					".$renewWhere."
					".$clutch_sql.") b
				".$joinAdd."
				order by case when GROUPING = 'Total' then 2 when GROUPING like 'Other%' then 1 else 0 end asc,
				".$order_by2.";";
		break;
	case "ranks":
		if($state_value == "FL") {
			$result = mysqli_query($conn,"SET SESSION group_concat_max_len = 1000000;");
			$sql = "select GROUPING,
			case when EARNED_PREMIUM >= ".$minprem." then 
			concat(FIND_IN_SET(EARNED_PREMIUM,EP_RANKING),' of ',LENGTH(EP_RANKING)-LENGTH(REPLACE(EP_RANKING,',',''))+1)
			else '' end as 'Earned Premium',
			case when EARNED_PREMIUM >= ".$minprem." then 
			concat(FIND_IN_SET(CRED_LOSS_RATIO,LR_RANKING),' of ',LENGTH(LR_RANKING)-LENGTH(REPLACE(LR_RANKING,',',''))+1)
			else '' end as 'Loss Ratio',
			case when EARNED_PREMIUM >= ".$minprem." then 
			concat(FIND_IN_SET(GROW,GROW_RANKING),' of ',LENGTH(GROW_RANKING)-LENGTH(REPLACE(GROW_RANKING,',',''))+1)
			else '' end as 'Growth',
			case when EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 then 
			concat(FIND_IN_SET(HIT_RATIO,HR_RANKING),' of ',LENGTH(HR_RANKING)-LENGTH(REPLACE(HR_RANKING,',',''))+1)
			else '' end as 'Hit Ratio',
			case when LAST_EARNED_PREMIUM >= ".$minprem." then 
			concat(FIND_IN_SET(LAST_EARNED_PREMIUM,LEP_RANKING),' of ',LENGTH(LEP_RANKING)-LENGTH(REPLACE(LEP_RANKING,',',''))+1)
			else '' end as 'Last Period Earned Premium',
			case when LAST_EARNED_PREMIUM >= ".$minprem." then 
			concat(FIND_IN_SET(LAST_CRED_LOSS_RATIO,LLR_RANKING),' of ',LENGTH(LLR_RANKING)-LENGTH(REPLACE(LLR_RANKING,',',''))+1)
			else '' end as 'Last Period Loss Ratio',
			case when LAST_EARNED_PREMIUM >= ".$minprem." then 
			concat(FIND_IN_SET(LAST_GROWTH,LGROW_RANKING),' of ',LENGTH(LGROW_RANKING)-LENGTH(REPLACE(LGROW_RANKING,',',''))+1)
			else '' end as 'Last Period Growth',
			case when LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 then 
			concat(FIND_IN_SET(LAST_HIT_RATIO,LHR_RANKING),' of ',LENGTH(LHR_RANKING)-LENGTH(REPLACE(LHR_RANKING,',',''))+1)
			else '' end as 'Last Period Hit Ratio'
			from (select GROUPING, ".$premium_calc." as EARNED_PREMIUM,
					".$cred_loss_ratio." as CRED_LOSS_RATIO,
					".$hit_ratio." as HIT_RATIO,
					".$growth." as GROW,
					SUM(QUOTE_COUNT) AS QUOTES,
					".$q2_premium_calc." as LAST_EARNED_PREMIUM,
					".$q2_cred_loss_ratio." as LAST_CRED_LOSS_RATIO,
					".$q2_hit_ratio." as LAST_HIT_RATIO,
					".$q2_growth." as LAST_GROWTH,
					SUM(Q2_QUOTE_COUNT) AS LAST_QUOTES
			from ".$schema.$tableName." b
			where GROUPING in (".$in_statement.") 
						and TYPE = '".$type."'
						and PROGRAM = '".$program."'
						and STATE = '".$state_value."'
						".$renewWhere."
						".$clutch_sql."
						group by GROUPING) a
			join (select group_concat(case when EARNED_PREMIUM >= ".$minprem." then EARNED_PREMIUM end order by EARNED_PREMIUM desc) as EP_RANKING,
			group_concat(case when EARNED_PREMIUM >= ".$minprem." then CRED_LOSS_RATIO end order by CRED_LOSS_RATIO desc) as LR_RANKING,
			group_concat(case when EARNED_PREMIUM >= ".$minprem." then GROWTH end order by GROWTH desc) as GROW_RANKING,
			group_concat(case when EARNED_PREMIUM >= ".$minprem." and QUOTES >= 100 then HIT_RATIO end order by HIT_RATIO desc) as HR_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." then LAST_EARNED_PREMIUM end order by LAST_EARNED_PREMIUM desc) as LEP_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." then LAST_CRED_LOSS_RATIO end order by LAST_CRED_LOSS_RATIO desc) as LLR_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." then LAST_GROWTH end order by LAST_GROWTH desc) as LGROW_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." and LAST_QUOTES >= 100 then LAST_HIT_RATIO end order by LAST_HIT_RATIO desc) as LHR_RANKING
				from (select GROUPING, ".$premium_calc." as EARNED_PREMIUM,
					".$cred_loss_ratio." as CRED_LOSS_RATIO,
					".$hit_ratio." as HIT_RATIO,
					".$growth." as GROWTH,
					sum(QUOTE_COUNT) as QUOTES,
					".$q2_premium_calc." as LAST_EARNED_PREMIUM,
					".$q2_cred_loss_ratio." as LAST_CRED_LOSS_RATIO,
					".$q2_hit_ratio." as LAST_HIT_RATIO,
					".$q2_growth." as LAST_GROWTH,
					sum(Q2_QUOTE_COUNT) as LAST_QUOTES
				from ".$schema.$tableName."
				where TYPE = '".$type."'
						and PROGRAM = '".$program."'
						and STATE = '".$state_value."'
						".$renewWhere."
						".$clutch_sql."
				group by GROUPING) a
		) ranks on 1 = 1
		order by ".$order_by3."";}
		else {
			$sql = "SELECT GROUPING, 
				EP_RANKING AS 'Earned Premium',
				LR_RANKING AS 'Loss Ratio',
				GROW_RANKING as 'Growth',
				HR_RANKING as 'Hit Ratio',
				LEP_RANKING as 'Last Period Earned Premium',
				LLR_RANKING as 'Last Period Loss Ratio',
				LGROW_RANKING as 'Last Period Growth',
				LHR_RANKING as 'Last Period Hit Ratio'
				FROM (SELECT GROUPING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN
					CONCAT(RANK() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,EARNED_PREMIUM DESC),' of ',
						COUNT(CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN 1 END) OVER()) ELSE '' END AS EP_RANKING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN
					CONCAT(RANK() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,CRED_LOSS_RATIO DESC),' of ',
						COUNT(CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN 1 END) OVER()) ELSE '' END AS LR_RANKING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN
					CONCAT(RANK() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,GROW DESC),' of ',
						COUNT(CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN 1 END) OVER()) ELSE '' END AS GROW_RANKING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 THEN
					CONCAT(RANK() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 then 0 else 1 end asc,HIT_RATIO DESC),' of ',
						COUNT(CASE WHEN EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 THEN 1 END) OVER()) ELSE '' END AS HR_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN
					CONCAT(RANK() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,LAST_EARNED_PREMIUM DESC),' of ',
						COUNT(CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN 1 END) OVER()) ELSE '' END AS LEP_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN
					CONCAT(RANK() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,LAST_CRED_LOSS_RATIO DESC),' of ',
						COUNT(CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN 1 END) OVER()) ELSE '' END AS LLR_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN
					CONCAT(RANK() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,LAST_GROWTH DESC),' of ',
						COUNT(CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN 1 END) OVER()) ELSE '' END AS LGROW_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 THEN
					CONCAT(RANK() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 then 0 else 1 end asc,LAST_HIT_RATIO DESC),' of ',
						COUNT(CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 THEN 1 END) OVER()) ELSE '' END AS LHR_RANKING,
					EARNED_PREMIUM,
					CRED_LOSS_RATIO,
					HIT_RATIO,
					GROW,
					QUOTES,
					LAST_EARNED_PREMIUM,
					LAST_CRED_LOSS_RATIO,
					LAST_HIT_RATIO,
					LAST_GROWTH,
					LAST_QUOTES
					FROM (select GROUPING, ".$premium_calc." as EARNED_PREMIUM,
					".$cred_loss_ratio." as CRED_LOSS_RATIO,
					".$hit_ratio." as HIT_RATIO,
					".$growth." as GROW,
					SUM(QUOTE_COUNT) AS QUOTES,
					".$q2_premium_calc." as LAST_EARNED_PREMIUM,
					".$q2_cred_loss_ratio." as LAST_CRED_LOSS_RATIO,
					".$q2_hit_ratio." as LAST_HIT_RATIO,
					".$q2_growth." as LAST_GROWTH,
					SUM(Q2_QUOTE_COUNT) AS LAST_QUOTES
			from ".$schema.$tableName." b
			where TYPE = '".$type."'
						and PROGRAM = '".$program."'
						and STATE = '".$state_value."'
						".$renewWhere."
						".$clutch_sql."
						group by GROUPING) a ) b WHERE GROUPING IN (".$in_statement.")
						order by ".$order_by3."";
		}
		break;
	case "map":
		switch($state_value."-".$type){
			case "FL-AGENT":
				$selectAdd = ", AGENT_PHY_LATITUDE AS LAT, AGENT_PHY_LONGITUDE AS LONGITUDE";
				$joinAdd = " join OSIS.AGENT ON AGENT_NUMBER = GROUPING";
				$groupAdd = "";
				break;
			case "TX-AGENT":
				$selectAdd = ", max(latitude) as LAT, max(longitude) as LONGITUDE";
				$joinAdd = "join Periscope_Data.dbo.producerLocations on producerCode = GROUPING";
				$groupAdd = "";
				break;
			case "FL-TERRITORY":
				$selectAdd = ", ZIP_CODE as ZIP2 ";
				$joinAdd = "JOIN pricing_data.rating_zip_codes a on concat(a.PROGRAM,'-',TERRITORY) = GROUPING
						join (select PROGRAM, MAX(EFFECTIVE_DATE) AS MAX_EFFECTIVE
						FROM pricing_data.rating_zip_codes group by 1) c on a.PROGRAM = c.PROGRAM and a.EFFECTIVE_DATE = c.MAX_EFFECTIVE";
				$groupAdd = ", ZIP_CODE ";
				break;
			default:
				$selectAdd = "";
				$joinAdd = "";
				$groupAdd = "";
		}
		$sql = "select GROUPING, 
		'".$mapColor."' as COLOR
		".$selectAdd."
		from ".$schema.$tableName." b
				".$joinAdd."
				where GROUPING in (".$in_statement.") 
					and TYPE = '".$type."'
					and b.PROGRAM = '".$program."'
					and STATE = '".$state_value."'
					".$renewWhere."
					".$clutch_sql."
				group by GROUPING".$groupAdd;
		break;
	case "graph":
		if($state_value == "FL") {
			$result = mysqli_query($conn,"SET SESSION group_concat_max_len = 1000000;");
			$sql = "select GROUPING,
			case when EARNED_PREMIUM >= ".$minprem." then FIND_IN_SET(GROUPING,EP_RANKING) end as EP_RANKING,
			EARNED_PREMIUM,
			case when EARNED_PREMIUM >= ".$minprem." then FIND_IN_SET(GROUPING,LR_RANKING) end as LR_RANKING,
			CRED_LOSS_RATIO as TOTAL,
			case when EARNED_PREMIUM >= ".$minprem." then FIND_IN_SET(GROUPING,GROW_RANKING) end as GROWTH_RANKING,
			GROWTH as INCREASE,
			case when EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 then FIND_IN_SET(GROUPING,HR_RANKING) end as CONV_RANKING,
			HIT_RATIO as CONVERSION,
			case when LAST_EARNED_PREMIUM >= ".$minprem." then FIND_IN_SET(GROUPING,LEP_RANKING) end as P3_EP_RANKING,
			LAST_EARNED_PREMIUM as Q2_EARNED_PREMIUM,
			case when LAST_EARNED_PREMIUM >= ".$minprem." then FIND_IN_SET(GROUPING,LLR_RANKING) end as P3_LR_RANKING,
			LAST_CRED_LOSS_RATIO as Q2_TOTAL,
			case when LAST_EARNED_PREMIUM >= ".$minprem." then FIND_IN_SET(GROUPING,LGROW_RANKING) end as P3_GROWTH_RANKING,
			LAST_GROWTH as Q2_INCREASE,
			case when LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 then FIND_IN_SET(GROUPING,LHR_RANKING) end as P3_CONV_RANKING,
			LAST_HIT_RATIO as Q2_CONVERSION,
			case when GROUPING in (".$in_statement.") then 'Y' else 'N' end as MAIN_RANKING
			from (select GROUPING, ".$premium_calc." as EARNED_PREMIUM,
					".$cred_loss_ratio." as CRED_LOSS_RATIO,
					".$hit_ratio." as HIT_RATIO,
					".$growth." as GROWTH,
					SUM(QUOTE_COUNT) AS QUOTES,
					".$q2_premium_calc." as LAST_EARNED_PREMIUM,
					".$q2_cred_loss_ratio." as LAST_CRED_LOSS_RATIO,
					".$q2_hit_ratio." as LAST_HIT_RATIO,
					".$q2_growth." as LAST_GROWTH,
					SUM(Q2_QUOTE_COUNT) AS LAST_QUOTES
			from ".$schema.$tableName." b
			where TYPE = '".$type."'
						and PROGRAM = '".$program."'
						and STATE = '".$state_value."'
						".$renewWhere."
						".$clutch_sql."
						group by GROUPING) a
			join (select group_concat(case when EARNED_PREMIUM >= ".$minprem." then GROUPING end order by EARNED_PREMIUM desc) as EP_RANKING,
			group_concat(case when EARNED_PREMIUM >= ".$minprem." then GROUPING end order by CRED_LOSS_RATIO desc) as LR_RANKING,
			group_concat(case when EARNED_PREMIUM >= ".$minprem." then GROUPING end order by GROWTH desc) as GROW_RANKING,
			group_concat(case when EARNED_PREMIUM >= ".$minprem." and QUOTES >= 100 then GROUPING end order by HIT_RATIO desc) as HR_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." then GROUPING end order by LAST_EARNED_PREMIUM desc) as LEP_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." then GROUPING end order by LAST_CRED_LOSS_RATIO desc) as LLR_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." then GROUPING end order by LAST_GROWTH desc) as LGROW_RANKING,
			group_concat(case when LAST_EARNED_PREMIUM >= ".$minprem." and LAST_QUOTES >= 100 then GROUPING end order by LAST_HIT_RATIO desc) as LHR_RANKING
				from (select GROUPING, ".$premium_calc." as EARNED_PREMIUM,
					".$cred_loss_ratio." as CRED_LOSS_RATIO,
					".$hit_ratio." as HIT_RATIO,
					".$growth." as GROWTH,
					sum(QUOTE_COUNT) as QUOTES,
					".$q2_premium_calc." as LAST_EARNED_PREMIUM,
					".$q2_cred_loss_ratio." as LAST_CRED_LOSS_RATIO,
					".$q2_hit_ratio." as LAST_HIT_RATIO,
					".$q2_growth." as LAST_GROWTH,
					sum(Q2_QUOTE_COUNT) as LAST_QUOTES
				from ".$schema.$tableName."
				where TYPE = '".$type."'
						and PROGRAM = '".$program."'
						and STATE = '".$state_value."'
						".$renewWhere."
						".$clutch_sql."
				group by GROUPING) a
		) ranks on 1 = 1";}
		else {
			$sql = "select * from (SELECT GROUPING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN 
						ROW_NUMBER() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,EARNED_PREMIUM DESC) END AS EP_RANKING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN
						ROW_NUMBER() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,CRED_LOSS_RATIO DESC) END AS LR_RANKING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." THEN
						ROW_NUMBER() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,GROWTH DESC) END AS GROWTH_RANKING,
					CASE WHEN EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 THEN
						ROW_NUMBER() OVER(ORDER BY case when EARNED_PREMIUM >= ".$minprem." AND QUOTES >= 100 then 0 else 1 end asc,HIT_RATIO DESC) END AS CONV_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN
						ROW_NUMBER() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,LAST_EARNED_PREMIUM DESC) END AS P3_EP_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN
						ROW_NUMBER() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,LAST_CRED_LOSS_RATIO DESC) END AS P3_LR_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." THEN
						ROW_NUMBER() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." then 0 else 1 end asc,LAST_GROWTH DESC) END AS P3_GROWTH_RANKING,
					CASE WHEN LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 THEN
						ROW_NUMBER() OVER(ORDER BY case when LAST_EARNED_PREMIUM >= ".$minprem." AND LAST_QUOTES >= 100 then 0 else 1 end asc,LAST_HIT_RATIO DESC) END AS P3_CONV_RANKING,
					EARNED_PREMIUM,
					CRED_LOSS_RATIO AS TOTAL,
					HIT_RATIO AS CONVERSION,
					GROWTH AS INCREASE,
					LAST_EARNED_PREMIUM AS Q2_EARNED_PREMIUM,
					LAST_CRED_LOSS_RATIO AS Q2_TOTAL,
					LAST_HIT_RATIO AS Q2_CONVERSION,
					LAST_GROWTH AS Q2_INCREASE,
					case when GROUPING in (".$in_statement.") then 'Y' else 'N' end as MAIN_RANKING
					
					FROM (select GROUPING, ".$premium_calc." as EARNED_PREMIUM,
					".$cred_loss_ratio." as CRED_LOSS_RATIO,
					".$hit_ratio." as HIT_RATIO,
					".$growth." as GROWTH,
					SUM(QUOTE_COUNT) AS QUOTES,
					".$q2_premium_calc." as LAST_EARNED_PREMIUM,
					".$q2_cred_loss_ratio." as LAST_CRED_LOSS_RATIO,
					".$q2_hit_ratio." as LAST_HIT_RATIO,
					".$q2_growth." as LAST_GROWTH,
					SUM(Q2_QUOTE_COUNT) AS LAST_QUOTES
			from ".$schema.$tableName." b
			where TYPE = '".$type."'
						and PROGRAM = '".$program."'
						and STATE = '".$state_value."'
						".$renewWhere."
						".$clutch_sql."
						group by GROUPING) a ) b
						where EP_RANKING is not null or LR_RANKING is not null or GROWTH_RANKING is not null or CONV_RANKING is not null
						or P3_EP_RANKING is not null or P3_LR_RANKING is not null or P3_GROWTH_RANKING is not null or P3_CONV_RANKING is not null";
		}
		break;
}
if($_GET['state'] == "FL"){
	$result = mysqli_query($conn,$sql);
	$data = array();



	for ($x = 0; $x < mysqli_num_rows($result); $x++) {
		$data[] = mysqli_fetch_assoc($result);
	}
}
elseif($_GET['state']=="TX"){
	$rs->Close();
	$conn->Close();

	$rs = null;
	$conn = null;
	
	unset($data);
	unset($num_columns);
	unset($arrColumns);
	unset($newArr);
	unset($arrRow);
		
	//create an instance of the  ADO connection object
	$conn = new COM ("ADODB.Connection")
	  or die("Cannot start ADO");

	//define connection string, specify database driver
	$connStr = "PROVIDER=SQLOLEDB;SERVER=".$servername.";UID=".$username.";PWD=".$password.";DATABASE=".$dbname; 
	  $conn->open($connStr); //Open the connection to the database

	$rs = $conn->execute($sql);
	$data = array();

	$num_columns = $rs->Fields->Count();


	for ($i=0; $i < $num_columns; $i++) {
		$arrColumns[] = $rs->Fields($i);
		$newArr[] = $rs->Fields($i)->name;
	}

	while (!$rs->EOF)  //carry on looping through while there are records
	{
		$arrRow = array();
		for($i=0; $i < $num_columns; $i++) {
			$arrRow[$newArr[$i]] = (string)$arrColumns[$i]->value;
		}
		$data[] = $arrRow;
		$rs->MoveNext(); //move on to the next record
	}
}
echo json_encode($data);

if($_GET['state']=="FL"){
	mysqli_close($conn);
}
elseif($_GET['state']=="TX"){
	$rs->Close();
	$conn->Close();

	$rs = null;
	$conn = null;
}
?>