<?
/*
* 프로그램				: 핀 통계보기 (지도에 표현하기 위해 geojson 방식으로 처리)
* 페이지 설명			: 각 좌표의 총인구, 혼잡정도, 성별비율, 나이비율 조회
* 파일명					: view_congestion.php
* 관련DB					: TB_CONGESTION, TB_PLACE
*/
include "../lib/common.php";
header('Content-type:application/json; charset=utf-8');

$con_Idx = trim($con_Idx);						//콘텐츠고유번호
$area_Code = trim($area_Code);				//지역코드
$time_Chk = trim($time_Chk);					//시간(1: 1시간 전 ~ 24 : 24시간전) // 0 또는 빈값일 경우 현재 시간 기준으로 조회

/*
if($time_Chk == "0" || $time_Chk == "" ){
	$sql_search ="  AND A.reg_Date = (SELECT MAX(A.reg_Date) FROM TB_CONGESTION A INNER JOIN TB_PLACE B ON A.place_Idx = B.idx)";
}else{
	$now_Date = strtotime("2019-10-24 23:21:12");
	$nowDate = date("Y-m-d H:i:s", $now_Date);
	$time1 = (int)$time_Chk;
	$time2 = (int)$time_Chk + 1;
	$timestamp = strtotime($nowDate."-".$time1." hours");
	$timestamp2 = strtotime($nowDate."-".$time2." hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search ="  AND A.reg_Date = (SELECT MAX(A.reg_Date) FROM TB_CONGESTION A INNER JOIN TB_PLACE B ON A.place_Idx = B.idx WHERE (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= '".$fr_date."' AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= '".$to_date."'))";
}
$sql_group = " GROUP BY A.idx, A.lng, A.lat";
$sql_order = " ORDER BY A.lng DESC, A.lat DESC ;";
*/
// 테스트용이므로 현재시간을 10월 24일 23시로 고정
if($time_Chk == "0" || $time_Chk == "" ){
	$time_Chk = "0";
	$now_Date = strtotime("2019-10-24 23:59:59");
	$nowDate = date("Y-m-d H:i:s", $now_Date);
	$time1 = (int)$time_Chk;
	$time2 = (int)$time_Chk + 1;
	$timestamp = strtotime($nowDate."-".$time1." hours");
	$timestamp2 = strtotime($nowDate."-".$time2." hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search ="  AND A.reg_Date = (SELECT MAX(A.reg_Date) FROM TB_CONGESTION A INNER JOIN TB_PLACE B ON A.place_Idx = B.idx WHERE (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= '".$fr_date."' AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= '".$to_date."'))";
}else{
	$now_Date = strtotime("2019-10-24 23:59:59");
	$nowDate = date("Y-m-d H:i:s", $now_Date);
	$time1 = (int)$time_Chk;
	$time2 = (int)$time_Chk + 1;
	$timestamp = strtotime($nowDate."-".$time1." hours");
	$timestamp2 = strtotime($nowDate."-".$time2." hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search ="  AND A.reg_Date = (SELECT MAX(A.reg_Date) FROM TB_CONGESTION A INNER JOIN TB_PLACE B ON A.place_Idx = B.idx WHERE (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= '".$fr_date."' AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= '".$to_date."'))";
}
$sql_group = " GROUP BY A.idx, A.lng, A.lat";
$sql_order = " ORDER BY A.lng DESC, A.lat DESC ;";

/*
if ($time_Chk == "0" || $time_Chk == "" ) {
	$sql_search.="";
}else if($time_Chk == "1"){
	$timestamp = strtotime("-1 hours");
	$timestamp2 = strtotime("-2 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "2"){
	$timestamp = strtotime("-2 hours");
	$timestamp2 = strtotime("-3 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "3"){
	$timestamp = strtotime("-3 hours");
	$timestamp2 = strtotime("-4 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "4"){
	$timestamp = strtotime("-4 hours");
	$timestamp2 = strtotime("-5 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "5"){
	$timestamp = strtotime("-5 hours");
	$timestamp2 = strtotime("-6 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "6"){
	$timestamp = strtotime("-6 hours");
	$timestamp2 = strtotime("-7 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "7"){
	$timestamp = strtotime("-7 hours");
	$timestamp2 = strtotime("-8 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "8"){
	$timestamp = strtotime("-8 hours");
	$timestamp2 = strtotime("-9 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "9"){
	$timestamp = strtotime("-9 hours");
	$timestamp2 = strtotime("-10 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "10"){
	$timestamp = strtotime("-10 hours");
	$timestamp2 = strtotime("-11 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "11"){
	$timestamp = strtotime("-11 hours");
	$timestamp2 = strtotime("-12 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "12"){
	$timestamp = strtotime("-12 hours");
	$timestamp2 = strtotime("-13 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "13"){
	$timestamp = strtotime("-13 hours");
	$timestamp2 = strtotime("-14 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "14"){
	$timestamp = strtotime("-14 hours");
	$timestamp2 = strtotime("-15 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "15"){
	$timestamp = strtotime("-15 hours");
	$timestamp2 = strtotime("-16 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "16"){
	$timestamp = strtotime("-16 hours");
	$timestamp2 = strtotime("-17 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "17"){
	$timestamp = strtotime("-17 hours");
	$timestamp2 = strtotime("-18 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "18"){
	$timestamp = strtotime("-18 hours");
	$timestamp2 = strtotime("-19 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "19"){
	$timestamp = strtotime("-19 hours");
	$timestamp2 = strtotime("-20 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "20"){
	$timestamp = strtotime("-20 hours");
	$timestamp2 = strtotime("-21 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "21"){
	$timestamp = strtotime("-21 hours");
	$timestamp2 = strtotime("-22 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "22"){
	$timestamp = strtotime("-22 hours");
	$timestamp2 = strtotime("-23 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "23"){
	$timestamp = strtotime("-23 hours");
	$timestamp2 = strtotime("-24 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}else if($time_Chk == "24"){
	$timestamp = strtotime("-24 hours");
	$timestamp2 = strtotime("-25 hours");
	$to_date = date("Y-m-d H:i:s", $timestamp);
	$fr_date = date("Y-m-d H:i:s", $timestamp2);
	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= :to_date)";
}
*/
$DB_con = db1();
//총 인구수
	if($area_Code != ""){

		// $chkData = [];
		// $chkData['type'] = "FeatureCollection";
		// $chkData['timestamp'] = date(DATE_ATOM, time());
		// $chkData['features'] = $data;

		$geojson = array(
		   'type'      => 'FeatureCollection',
		   'features'  => array()
		);

		$chk_query = "
			SELECT *
			FROM TB_CONGESTION A
				INNER JOIN TB_PLACE B
					ON A.place_Idx = B.idx
			WHERE B.con_Idx = :con_Idx
				AND A.area_Code = :area_Code;
		";
		$chk_stmt = $DB_con->prepare($chk_query);
		$chk_stmt->bindParam(":con_Idx", $con_Idx);
		$chk_stmt->bindParam(":area_Code", $area_Code);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
		if($chk_num < 1){
			$result = array("result" => "error", "errorMsg" => "내역없음.");
			echo json_encode($result);
		}else{
			$query ="
				SELECT A.idx, A.lng, A.lat, A.cong_Rate, A.tot_Cnt, female_Cnt, female10, female20, female30 ,female40, female50, male_Cnt, male10, male20, male30 ,male40, male50
				FROM TB_CONGESTION A
					INNER JOIN TB_PLACE B
						ON A.place_Idx = B.idx
				WHERE B.con_Idx = :con_Idx
					AND A.area_Code = :area_Code
					{$sql_search}
					{$sql_group}
					{$sql_order}
					;
			";
			/*
			echo $query;
			exit;
			*/
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->bindParam(":area_Code", $area_Code);
			$stmt->execute();
			$data = [];
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];													// 경도
				$lng = $row['lng'];												// 경도
				$lat = $row['lat'];													// 위도
				$cong_Rate = $row['cong_Rate'];								// 혼잡정도
				$tot_Cnt = $row['tot_Cnt'];										// 총인구수
				$fCnt = $row['female_Cnt'];										// 여자인구수
				$mCnt = $row['male_Cnt'];										// 남자인구수
				$female10 = $row['female10'];									// 10대 여자
				$female20 = $row['female20'];									// 20대 여자
				$female30 = $row['female30'];									// 30대 여자
				$female40 = $row['female40'];									// 40대 여자
				$female50 = $row['female50'];									// 50대 여자
				$male10 = $row['male10'];										// 10대 남자
				$male20 = $row['male20'];										// 10대 남자
				$male30 = $row['male30'];										// 10대 남자
				$male40 = $row['male40'];										// 10대 남자
				$male50 = $row['male50'];										// 10대 남자
				$fRate =  (($fCnt / $tot_Cnt) * 100);							// 여성비율
				$mRate = (($mCnt / $tot_Cnt) * 100);						// 남성비율
				$mRate2 = ($mRate / 10);										// 남성비율을 10으로 나누기
				$poiresult = [(double)$lng, (double)$lat];					//좌표를 숫자형으로
				$frate_10 = (($female10 / $fCnt) * 100);						// 여성 10대 비율
				$frate_20 = (($female20 / $fCnt) * 100);						// 여성 20대 비율
				$frate_30 = (($female30 / $fCnt) * 100);						// 여성 30대 비율
				$frate_40 = (($female40 / $fCnt) * 100);						// 여성 40대 비율
				$frate_50 = (($female50 / $fCnt) * 100);						// 여성 50대 비율
				$mrate_10 = (($male10 / $mCnt) * 100);					// 남성 10대 비율
				$mrate_20 = (($male20 / $mCnt) * 100);					// 남성 20대 비율
				$mrate_30 = (($male30 / $mCnt) * 100);					// 남성 30대 비율
				$mrate_40 = (($male40 / $mCnt) * 100);					// 남성 40대 비율
				$mrate_50 = (($male50 / $mCnt) * 100);					// 남성 50대 비율
				$age10 = (double)$female10 + (double)$male10;		// 10대 합
				$age20 = (double)$female20 + (double)$male20;		// 20대 합
				$age30 = (double)$female30 + (double)$male30;		// 30대 합
				$age40 = (double)$female40 + (double)$male40;		// 40대 합
				$age50 = (double)$female50 + (double)$male50;		// 50대 합
				$rate_10 = (($age10 / $tot_Cnt) * 100);						// 10대 비율
				$rate_20 = (($age20 / $tot_Cnt) * 100);						// 20대 비율
				$rate_30 = (($age30 / $tot_Cnt) * 100);						// 30대 비율
				$rate_40 = (($age40 / $tot_Cnt) * 100);						// 40대 비율
				$rate_50 = (($age50 / $tot_Cnt) * 100);						// 50대 비율

				$mresult = array(
					"idx" => (int)$idx,
					 "cong_Rate" => (int)$cong_Rate,
					  "tot_Cnt" => (double)$tot_Cnt,
						"fRate" => round($fRate),
						"mRate" => round($mRate),
						"mRate2" => floor($mRate2),
						"age10" => $age10,
						"age20" => $age20,
						"age30" => $age30,
						"age40" => $age40,
						"age50" => $age50,
						"frate_10" => round($frate_10),
						"frate_20" => round($frate_20),
						"frate_30" => round($frate_30),
						"frate_40" => round($frate_40),
						"frate_50" => round($frate_50),
						"mrate_10" => round($mrate_10),
						"mrate_20" => round($mrate_20),
						"mrate_30" => round($mrate_30),
						"mrate_40" => round($mrate_40),
						"mrate_50" => round($mrate_50),
						"rate_10" => round($rate_10),
						"rate_20" => round($rate_20),
						"rate_30" => round($rate_30),
						"rate_40" => round($rate_40),
						"rate_50" => round($rate_50)
					);
				$features = array(
					"type" => "Feature",
					"geometry" => array(
						"type" => "Point",
						"coordinates" => array(
							(double)$lng,
							(double)$lat
						)
					),
					"properties" => $mresult

				);
				//array_push($geojson['geometry']['coordinates'], $poiresult);
				//array_push($geojson['properties'], $mresult);
				array_push($geojson['features'], $features);
			}
			echo json_encode($geojson,JSON_NUMERIC_CHECK);

		}
	}else{

		$geojson = array(
			 'type'      => 'FeatureCollection',
			 'features'  => array()
		);

		$chk_query = "
			SELECT *
			FROM TB_CONGESTION A
				INNER JOIN TB_PLACE B
					ON A.place_Idx = B.idx
			WHERE B.con_Idx = :con_Idx;
		";
		$chk_stmt = $DB_con->prepare($chk_query);
		$chk_stmt->bindParam(":con_Idx", $con_Idx);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
		if($chk_num < 1){
			$result = array("result" => "error", "errorMsg" => "내역없음.");
			echo json_encode($result);
		}else{
			$query ="
				SELECT A.idx, A.lng, A.lat, A.cong_Rate, A.tot_Cnt, female_Cnt, female10, female20, female30 ,female40, female50, male_Cnt, male10, male20, male30 ,male40, male50
				FROM TB_CONGESTION A
					INNER JOIN TB_PLACE B
						ON A.place_Idx = B.idx
				WHERE B.con_Idx = :con_Idx
					{$sql_search}
					{$sql_group}
					{$sql_order};
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->execute();
			$data = [];
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];													// 경도
				$lng = $row['lng'];												// 경도
				$lat = $row['lat'];													// 위도
				$cong_Rate = $row['cong_Rate'];								// 혼잡정도
				$tot_Cnt = $row['tot_Cnt'];										// 총인구수
				$fCnt = $row['female_Cnt'];										// 여자인구수
				$mCnt = $row['male_Cnt'];										// 남자인구수
				$female10 = $row['female10'];									// 10대 여자
				$female20 = $row['female20'];									// 20대 여자
				$female30 = $row['female30'];									// 30대 여자
				$female40 = $row['female40'];									// 40대 여자
				$female50 = $row['female50'];									// 50대 여자
				$male10 = $row['male10'];										// 10대 남자
				$male20 = $row['male20'];										// 10대 남자
				$male30 = $row['male30'];										// 10대 남자
				$male40 = $row['male40'];										// 10대 남자
				$male50 = $row['male50'];										// 10대 남자
				$fRate =  (($fCnt / $tot_Cnt) * 100);							// 여성비율
				$mRate = (($mCnt / $tot_Cnt) * 100);						// 남성비율
				$mRate2 = ($mRate / 10);										// 남성비율을 10으로 나누기
				$poiresult = [(double)$lng, (double)$lat];					//좌표를 숫자형으로
				$frate_10 = (($female10 / $fCnt) * 100);						// 여성 10대 비율
				$frate_20 = (($female20 / $fCnt) * 100);						// 여성 20대 비율
				$frate_30 = (($female30 / $fCnt) * 100);						// 여성 30대 비율
				$frate_40 = (($female40 / $fCnt) * 100);						// 여성 40대 비율
				$frate_50 = (($female50 / $fCnt) * 100);						// 여성 50대 비율
				$mrate_10 = (($male10 / $mCnt) * 100);					// 남성 10대 비율
				$mrate_20 = (($male20 / $mCnt) * 100);					// 남성 20대 비율
				$mrate_30 = (($male30 / $mCnt) * 100);					// 남성 30대 비율
				$mrate_40 = (($male40 / $mCnt) * 100);					// 남성 40대 비율
				$mrate_50 = (($male50 / $mCnt) * 100);					// 남성 50대 비율
				$age10 = (double)$female10 + (double)$male10;		// 10대 합
				$age20 = (double)$female20 + (double)$male20;		// 20대 합
				$age30 = (double)$female30 + (double)$male30;		// 30대 합
				$age40 = (double)$female40 + (double)$male40;		// 40대 합
				$age50 = (double)$female50 + (double)$male50;		// 50대 합
				$rate_10 = (($age10 / $tot_Cnt) * 100);						// 10대 비율
				$rate_20 = (($age20 / $tot_Cnt) * 100);						// 20대 비율
				$rate_30 = (($age30 / $tot_Cnt) * 100);						// 30대 비율
				$rate_40 = (($age40 / $tot_Cnt) * 100);						// 40대 비율
				$rate_50 = (($age50 / $tot_Cnt) * 100);						// 50대 비율

				$mresult = array(
					"idx" => (int)$idx,
					 "cong_Rate" => (int)$cong_Rate,
					  "tot_Cnt" => (double)$tot_Cnt,
						"fRate" => round($fRate),
						"mRate" => round($mRate),
						"mRate2" => floor($mRate2),
						"age10" => $age10,
						"age20" => $age20,
						"age30" => $age30,
						"age40" => $age40,
						"age50" => $age50,
						"frate_10" => round($frate_10),
						"frate_20" => round($frate_20),
						"frate_30" => round($frate_30),
						"frate_40" => round($frate_40),
						"frate_50" => round($frate_50),
						"mrate_10" => round($mrate_10),
						"mrate_20" => round($mrate_20),
						"mrate_30" => round($mrate_30),
						"mrate_40" => round($mrate_40),
						"mrate_50" => round($mrate_50),
						"rate_10" => round($rate_10),
						"rate_20" => round($rate_20),
						"rate_30" => round($rate_30),
						"rate_40" => round($rate_40),
						"rate_50" => round($rate_50)
					);
				$features = array(
					"type" => "Feature",
					"geometry" => array(
						"type" => "Point",
						"coordinates" => array(
							(double)$lng,
							(double)$lat
						)
					),
					"properties" => $mresult

				);
				//array_push($geojson['geometry']['coordinates'], $poiresult);
				//array_push($geojson['properties'], $mresult);
				array_push($geojson['features'], $features);
			}
			echo json_encode($geojson,JSON_NUMERIC_CHECK);
		}
	}

    dbClose($DB_con);
    $stmt = null;
	$sum_stmt = null;
?>
