<?

	$use_Date =  '2019-08-01 00:00:00';
	$add_use_Date =  '30';
	if($add_use_Date == '30'){
		$timestamp = strtotime("+30 days", strtotime($use_Date));
	}else if($add_use_Date == '60'){
		$timestamp = strtotime("+60 days", strtotime($use_Date));
	}else if($add_use_Date == '90'){
		$timestamp = strtotime("+90 days", strtotime($use_Date));
	}
	//사용일! 입력하기
	$useDate = date("Y-m-d", $timestamp);
	
	$useDate2 = date("Y-m-d", strtotime($use_Date));	
	$useDate3 = date($use_Date, $timestamp);	
	$useDate4 = strtotime("+30 days", strtotime($use_Date));	
	$useDate5 = date("Y-m-d", $useDate4);	

	echo "use_Date : ".$use_Date."<br><br>";
	echo "add_use_Date : ".$add_use_Date."<br><br>";
	echo "timestamp : ".$timestamp."<br><br>";
	echo "useDate : ".$useDate."<br><br>";
	echo "useDate2 : ".$useDate2."<br><br>";
	echo "useDate3 : ".$useDate3."<br><br>";
	echo "useDate4 : ".$useDate4."<br><br>";
	echo "useDate5 : ".$useDate5."<br><br>";
?>