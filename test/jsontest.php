<?
include "../lib/common.php";
// Web JSON 파일 읽어오기
//$url = 'http://ip주소/getFileList.php';
//$json_string = file_get_contents($url);

$DB_con = db1();
$reg_Date = "2019-10-24 23:21:12";
	$f_name1 = 'Connect1.json';
	$fname1 = iconv("UTF-8", "EUC-KR", $f_name1);
	// $f_name2 = '디코이.json';
	// $fname2 = iconv("UTF-8", "EUC-KR", $f_name2);
	// $f_name3 = '무브클럽.json';
	// $fname3 = iconv("UTF-8", "EUC-KR", $f_name3);
	// $f_name4 = '소프.json';
	// $fname4 = iconv("UTF-8", "EUC-KR", $f_name4);
	// $f_name5 = 'Connect1.json';
	// $fname5 = iconv("UTF-8", "EUC-KR", $f_name5);

// Local JSON 파일 읽어오기
$json_string = file_get_contents($fname1);
	$place_Idx = '16';
//$json_string = file_get_contents($fname2);
	//$place_Idx = '17';
//$json_string = file_get_contents($fname3);
	//$place_Idx = '18';
//$json_string = file_get_contents($fname4);
	//$place_Idx = '19';
//$json_string = file_get_contents($fname5);
	//$place_Idx = '20';
	/*
	$f_name1 = 'AURA.json';
	$fname1 = iconv("UTF-8", "EUC-KR", $f_name1);
	$json_string = file_get_contents($fname5);
	$place_Idx = '5';
*/
// 다차원 배열 반복처리
$json = json_decode($json_string, true);
// 사용할 배열을 위해 배열크기 확인
$json_cnt = count($json['features']);
// json파일 확인
//echo '<pre>' . print_r($json, true) . '</pre>';
// 배열크기 만큼 반복하여 DB에 넣기
for($i = 0; $i < $json_cnt; $i++){
	$lng = $json['features'][$i]['geometry']['coordinates'][0]; // 경도
	$lat = $json['features'][$i]['geometry']['coordinates'][1]; // 위도
	$male_0009 = $json['features'][$i]['properties']['male_0009']; // 남성0~9세
	$male_1019 = $json['features'][$i]['properties']['male_1019']; // 남성10~19세
	$male_20 = $json['features'][$i]['properties']['male_2029']; // 남성20~29세
	$male_30 = $json['features'][$i]['properties']['male_3039']; // 남성30~39세
	$male_40 = $json['features'][$i]['properties']['male_4049']; // 남성40~49세
	$male_5059 = $json['features'][$i]['properties']['male_5059']; // 남성50~59세
	$male_6069 = $json['features'][$i]['properties']['male_6069']; // 남성60~69세
	$male_7079 = $json['features'][$i]['properties']['male_7079']; // 남성70~79세
	$male_8089 = $json['features'][$i]['properties']['male_8089']; // 남성80~89세
	$male_9099 = $json['features'][$i]['properties']['male_9099']; // 남성90~99세
	$male_100 = $json['features'][$i]['properties']['male_100']; // 남성100세
	$male_Etc = $json['features'][$i]['properties']['male_etc']; // 기타남성
	$female_0009 = $json['features'][$i]['properties']['female_0009']; // 여성0~9세
	$female_1019 = $json['features'][$i]['properties']['female_1019']; // 여성10~19세
	$female_20 = $json['features'][$i]['properties']['female_2029']; // 여성20~29세
	$female_30 = $json['features'][$i]['properties']['female_3039']; // 여성30~39세
	$female_40 = $json['features'][$i]['properties']['female_4049']; // 여성40~49세
	$female_5059 = $json['features'][$i]['properties']['female_5059']; // 여성50~59세
	$female_6069 = $json['features'][$i]['properties']['female_6069']; // 여성60~69세
	$female_7079 = $json['features'][$i]['properties']['female_7079']; // 여성70~79세
	$female_8089 = $json['features'][$i]['properties']['female_8089']; // 여성80~89세
	$female_9099 = $json['features'][$i]['properties']['female_9099']; // 여성90~99세
	$female_100 = $json['features'][$i]['properties']['female_100']; // 여성100세
	$female_Etc = $json['features'][$i]['properties']['female_etc']; // 기타여성
	$etc_Cnt = $json['features'][$i]['properties']['etc']; // 기타			//회원 기본테이블 저장
	
	// 계산하기
	// 여성10대 = 여성0~9세 + 여성10~19세
	$female10 = (double)$female_0009 + (double)$female_1019;	
	if($female10 == ''){
		$female10 = 0;
	}
	// 여성 50대 이상~
	$female50 = (double)$female_5059 + (double)$female_6069 + (double)$female_7079 + (double)$female_8089 + (double)$female_9099 + (double)$female_100;
	if($female50 == ''){
		$female50 = 0;
	}
	// 여성총합
	$female_Cnt = (double)$female_0009 + (double)$female_1019 + (double)$female_20 + (double)$female_30 + (double)$female_40 + (double)$female_5059 + (double)$female_6069 + (double)$female_7079 + (double)$female_8089 + (double)$female_9099 + (double)$female_100 + (double)$female_etc;

	// 남성10대 = 남성0~9세 + 남성10~19세
	$male10 = (double)$male_0009 + (double)$male_1019;	
	if($male10 == ''){
		$male10 = 0;
	}
	// 남성 50대 이상~
	$male50 = (double)$male_5059 + (double)$male_6069 + (double)$male_7079 + (double)$male_8089 + (double)$male_9099 + (double)$male_100;
	if($male50 == ''){
		$male50 = 0;
	}
	// 남성총합
	$male_Cnt = (double)$male_0009 + (double)$male_1019 + (double)$male_20 + (double)$male_30 + (double)$male_40 + (double)$male_5059 + (double)$male_6069 + (double)$male_7079 + (double)$male_8089 + (double)$male_9099 + (double)$male_100 + (double)$male_etc;

	//총합
	$tot_Cnt = (double)$female_Cnt + (double)$male_Cnt;
	$area_Code = "이태원";
	$reg_Id = "admin";
	/*
	echo $female10."<BR>";
	echo $female50."<BR>";
	echo $female_Cnt."<BR>";
	echo $male10."<BR>";
	echo $male50."<BR>";
	echo $male_Cnt."<BR>";
	echo $tot_Cnt."<BR>";
	
	echo $area_Code."<BR>";
	echo $lng."<BR>";
	echo $lat."<BR>";
	echo $female10."<BR>";
	echo $female_2029."<BR>";
	echo $female_3039."<BR>";
	echo $female_4049."<BR>";
	echo $female50."<BR>";
	echo $female_Etc."<BR>";
	echo $female_Cnt."<BR>";
	echo $male10."<BR>";
	echo $male_2029."<BR>";
	echo $male_3039."<BR>";
	echo $male_4049."<BR>";
	echo $male50."<BR>";
	echo $male_Etc."<BR>";
	echo $male_Cnt."<BR>";
	echo $etc_Cnt."<BR>";
	echo $tot_Cnt."<BR>";
	echo $reg_Id."<BR>";
	echo $reg_Date."<BR><Br>";
	*/
	//DB입력하기
	$chk_query = "
		SELECT *
		FROM TB_CONGESTION
		WHERE lng = ".$lng."
			AND lat = ".$lat.";
	";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->execute();
	$chk_Num = $chk_stmt->rowCount();
	if($chk_Num < 1){
		$query = "
			INSERT INTO TB_CONGESTION (area_Code, place_Idx, lng, lat, female10, female20, female30, female40, female50, female_Etc, female_Cnt, male10, male20, male30, male40, male50, male_Etc, male_Cnt, etc_Cnt, tot_Cnt, reg_Id, reg_Date) VALUES ('".$area_Code."', ".$place_Idx.", ".$lng.", ".$lat.", ".$female10.", ".$female_20.", ".$female_30.", ".$female_40.", ".$female50.", ".$female_Etc.", ".$female_Cnt.", ".$male10.", ".$male_20.", ".$male_30.", ".$male_40.", ".$male50.", ".$male_Etc.", ".$male_Cnt.", ".$etc_Cnt.", ".$tot_Cnt.", '".$reg_Id."', '".$reg_Date."');
		";
		//echo $query;
		$stmt = $DB_con->prepare($query);
		$stmt->execute();
	}else{
		$query = "
			UPDATE TB_CONGESTION 
			SET area_Code = '".$area_Code."', place_Idx = ".$place_Idx.", female10 = ".$female10.", female20 = ".$female_20.", female30 = ".$female_30.", female40 = ".$female_40.", female50 = ".$female50.", female_Etc = ".$female_Etc.", female_Cnt = ".$female_Cnt.", male10 = ".$male10.", male20 = ".$male_20.", male30 = ".$male_30.", male40 = ".$male_40.", male50 = ".$male50.", male_Etc = ".$male_Etc.", male_Cnt = ".$male_Cnt.", etc_Cnt = ".$etc_Cnt.", tot_Cnt = ".$tot_Cnt.", reg_Id = '".$reg_Id."', reg_Date = '".$reg_Date."'
			WHERE 
				lng = ".$lng."
				AND lat = ".$lat."
			LIMIT 1
			;
		";
		//echo $query;
		$stmt = $DB_con->prepare($query);
		$stmt->execute();
	}
	/*
	//DB입력하기
	$query = "
		INSERT INTO TB_CONGESTION (area_Code, lng, lat, female10, female20, female30, female40, female50, female_Etc, female_Cnt, male10, male20, male30, male40, male50, male_Etc, male_Cnt, etc_Cnt, tot_Cnt, reg_Id, reg_date) VALUES (:area_Code, :lng, :lat, :female10, :female20, :female30, :female40, :female50, :female_Etc, :female_Cnt, :male10, :male20, :male30, :male40, :male50, :male_Etc, :male_Cnt, :etc_Cnt, :tot_Cnt, :reg_Id, :reg_date);
	";
	
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":area_Code", $area_Code);
	$stmt->bindParam(":lng", $lng);
	$stmt->bindParam(":lat", $lat);
	$stmt->bindParam(":female10", $female10);
	$stmt->bindParam(":female20", $female_2029);
	$stmt->bindParam(":female30", $female_3039);
	$stmt->bindParam(":female40", $female_4049);
	$stmt->bindParam(":female50", $female50);
	$stmt->bindParam(":female_Etc", $female_Etc);
	$stmt->bindParam(":female_Cnt", $female_Cnt);
	$stmt->bindParam(":male10", $male10);
	$stmt->bindParam(":male20", $male_2029);
	$stmt->bindParam(":male30", $male_3039);
	$stmt->bindParam(":male40", $male_4049);
	$stmt->bindParam(":male50", $male50);
	$stmt->bindParam(":male_Etc", $male_Etc);
	$stmt->bindParam(":male_Cnt", $male_Cnt);
	$stmt->bindParam(":etc_Cnt", $etc_Cnt);
	$stmt->bindParam(":tot_Cnt", $tot_Cnt);
	$stmt->bindParam(":reg_Id", $reg_Id);
	$stmt->bindParam(":reg_Date", $reg_Date);
	$stmt->execute();
*/
}
$result = array("result" => "success");
echo json_encode($result);

dbClose($DB_con);
$stmt = null;	
$chk_stmt = null;

// $R : array data
// json_decode : JSON 문자열을 PHP 배열로 바꾼다
// json_decode 함수의 두번째 인자를 true 로 설정하면 무조건 array로 변환된다.
/*
foreach ($json as $key // $val) {
	//echo $json['features'][0]['geometry']['coordinates'][0];
	//$lng = $json['features'][0]['geometry']['coordinates'][0];
	//echo $lng;
	$lng = $json['features'][0]['geometry']['coordinates'][0]; // 경도
	$lat = $json['features'][0]['geometry']['coordinates'][1]; // 위도
	$male_0009 = $json['features'][0]['properties']['male_0009']; // 남성0~9세
	$male_1019 = $json['features'][0]['properties']['male_1019']; // 남성10~19세
	$male_2029 = $json['features'][0]['properties']['male_2029']; // 남성20~29세
	$male_3039 = $json['features'][0]['properties']['male_3039']; // 남성30~39세
	$male_4049 = $json['features'][0]['properties']['male_4049']; // 남성40~49세
	$male_5059 = $json['features'][0]['properties']['male_5059']; // 남성50~59세
	$male_6069 = $json['features'][0]['properties']['male_6069']; // 남성60~69세
	$male_7079 = $json['features'][0]['properties']['male_7079']; // 남성70~79세
	$male_8089 = $json['features'][0]['properties']['male_8089']; // 남성80~89세
	$male_9099 = $json['features'][0]['properties']['male_9099']; // 남성90~99세
	$male_100 = $json['features'][0]['properties']['male_100']; // 남성100세
	$male_etc = $json['features'][0]['properties']['male_etc']; // 기타남성
	$female_0009 = $json['features'][0]['properties']['female_0009']; // 여성0~9세
	$female_1019 = $json['features'][0]['properties']['female_1019']; // 여성10~19세
	$female_2029 = $json['features'][0]['properties']['female_2029']; // 여성20~29세
	$female_3039 = $json['features'][0]['properties']['female_3039']; // 여성30~39세
	$female_4049 = $json['features'][0]['properties']['female_4049']; // 여성40~49세
	$female_5059 = $json['features'][0]['properties']['female_5059']; // 여성50~59세
	$female_6069 = $json['features'][0]['properties']['female_6069']; // 여성60~69세
	$female_7079 = $json['features'][0]['properties']['female_7079']; // 여성70~79세
	$female_8089 = $json['features'][0]['properties']['female_8089']; // 여성80~89세
	$female_9099 = $json['features'][0]['properties']['female_9099']; // 여성90~99세
	$female_100 = $json['features'][0]['properties']['female_100']; // 여성100세
	$female_etc = $json['features'][0]['properties']['female_etc']; // 기타여성
	$etc = $json['features'][0]['properties']['etc']; // 기타
	//echo $key." : ".$val."<BR>";
	//echo $R['features'];
    if(is_array($val)) { // val 이 배열이면
        echo "$key:<br/>";
        //echo $key.' (key), value : (array)<br />';
    } else { // 배열이 아니면
        echo "$key // $val <br />";
    }
}*/
?>