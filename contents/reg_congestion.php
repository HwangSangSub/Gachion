<?
/*
* 프로그램				: 혼잡도 정보를 저장
* 페이지 설명			: 혼잡도 정보를 저장
* 파일명					: congestion.php
* 관련DB					: TB_CONGESTION
*/
include "../lib/common.php";
include "../lib/functionDB.php";		//공통 db함수.
$con_Idx = trim($conIdx);				// 지도 고유번호
$place_Idx = trim($placeIdx);			// 지점 고유번호
$DB_con = db1();

$con_query = "
	SELECT kml_File
	FROM TB_CONTENTS
	WHERE idx = :con_Idx
";
$con_stmt = $DB_con->prepare($con_query);
$con_stmt->bindParam(":con_Idx", $con_Idx);
$con_stmt->execute();
$con_row=$con_stmt->fetch(PDO::FETCH_ASSOC);
$kml_File = $con_row['kml_File'];											// 지도kml 파일
if($kml_File != ""){
	$xml = file_get_contents("./kmlfile/".$kml_File);
	$result_xml = simplexml_load_string($xml);
	$siname = $result_xml->Document->name; 
	$siname_exp = explode('.', $siname);
	$siname = $siname_exp[0];
	echo $siname;
	
}
$chk_place_query = "
	SELECT count(*) as cnt
	FROM TB_PLACE
	WHERE con_Idx = :con_Idx;
";
$chk_place_stmt = $DB_con->prepare($chk_place_query);
$chk_place_stmt->bindParam(":con_Idx", $con_Idx);
$chk_place_stmt->execute();
$chk_place_row=$chk_place_stmt->fetch(PDO::FETCH_ASSOC);
$chk_place_cnt = $chk_place_row['cnt'];											// 등록된 지점
if($chk_place_cnt > 0){
	$place_query = "
		SELECT *
		FROM TB_PLACE
		WHERE con_Idx = :con_Idx;
	";
	$place_stmt = $DB_con->prepare($place_query);
	$place_stmt->bindParam(":con_Idx", $con_Idx);
	$place_stmt->execute();
	while($place_row=$place_stmt->fetch(PDO::FETCH_ASSOC)){
		$place_Idx = $place_row['idx'];			// 지점 고유번호
		$placelng = $place_row['lng'];			// 경도
		$placelat = $place_row['lat'];			// 위도
		$place_lng = floor($placelng*100000)/100000; // 제일 간단
		$place_lat = floor($placelat*100000)/100000; // 제일 간단
		$chk_lnglat_query = "
			SELECT BLOCK_CD, BOUNDS, WKT
			FROM RP_WKT
			WHERE SIDO_NM LIKE '".$siname."%';
		";
		$chk_lnglat_stmt = $DB_con->prepare($chk_lnglat_query);
		$chk_lnglat_stmt->execute();
		while($chk_lnglat_row=$chk_lnglat_stmt->fetch(PDO::FETCH_ASSOC)){
			$block_cd = $chk_lnglat_row['BLOCK_CD'];			// 블럭값
			if($block_cd == ""){
				$block_cd = "";
				continue;
			}			
			$bounds = $chk_lnglat_row['BOUNDS'];				// 직사각형 폴리곤 값
			$bou_exp = explode(',', str_replace('))', '', str_replace('POLYGON((', '', $bounds)));
			$bou_cnt = count($bou_exp);
			$chk_lng = [];
			$chk_lat = [];
			for($i = 0; $i < $bou_cnt; $i++){
				$bou = $bou_exp[$i];
				$bouexp = explode(' ', $bou);
				array_push($chk_lng, $bouexp[0]);
				array_push($chk_lat, $bouexp[1]);
			}
			$min_lng = min($chk_lng);
			$max_lng = max($chk_lng);
			$min_lat = min($chk_lat);
			$max_lat = max($chk_lat);
			if(((double)$min_lng <= (double)$place_lng && (double)$max_lng >= (double)$place_lng) && ((double)$min_lat <= (double)$place_lat && (double)$max_lat >= (double)$place_lat)){
				echo $block_cd."만족한다.\n";
				break;
			}else{
			}
		}
	}
	$result = array("result" => "success");
}else{
	$result = array("result" => "error", "errorMsg" => "등록된 지점이 없습니다.");
}
dbClose($DB_con);
$sum_stmt = null;
$chk_stmt = null;
$chk_place_stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE); 
?>



