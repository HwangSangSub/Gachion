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
	//$nowDate = date("Y-m-d H:i:s", $now_Date);
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
	//$nowDate = date("Y-m-d H:i:s", $now_Date);
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
			$sum_query = "
				SELECT 
					count(A.idx) as tCnt,
					sum(A.female10) as sum_fCnt10,
					sum(A.female20) as sum_fCnt20,
					sum(A.female30) as sum_fCnt30,
					sum(A.female40) as sum_fCnt40,
					sum(A.female50) as sum_fCnt50,
					sum(A.female_Cnt) as sum_fCnt, 
					sum(A.male10) as sum_mCnt10,
					sum(A.male20) as sum_mCnt20,
					sum(A.male30) as sum_mCnt30,
					sum(A.male40) as sum_mCnt40,
					sum(A.male50) as sum_mCnt50,
					sum(A.male_Cnt) as sum_mCnt,
					sum(A.tot_Cnt) as sum_tCnt,
					max(A.female10) as max_fCnt10,
					max(A.female20) as max_fCnt20,
					max(A.female30) as max_fCnt30,
					max(A.female40) as max_fCnt40,
					max(A.female50) as max_fCnt50,
					max(A.female_Cnt) as max_fCnt, 
					max(A.male10) as max_mCnt10,
					max(A.male20) as max_mCnt20,
					max(A.male30) as max_mCnt30,
					max(A.male40) as max_mCnt40,
					max(A.male50) as max_mCnt50,
					max(A.male_Cnt) as max_mCnt,
					max(A.tot_Cnt) as max_tCnt
				FROM TB_CONGESTION A
					INNER JOIN TB_PLACE B
						ON A.place_Idx = B.idx
				WHERE B.con_Idx = :con_Idx
					AND A.area_Code = :area_Code
					{$sql_search};
			";
			$sum_stmt = $DB_con->prepare($sum_query);
			$sum_stmt->bindParam(":con_Idx", $con_Idx);
			$sum_stmt->bindParam(":area_Code", $area_Code);
			$sum_stmt->execute();
			$sum_row=$sum_stmt->fetch(PDO::FETCH_ASSOC);
			$tCnt = $sum_row['tCnt'];									// 행수

			$max_tCnt = $sum_row['max_tCnt'];						// 총합(최대)
			$max_fCnt = $sum_row['max_fCnt'];						// 여성합(최대)
			$max_mCnt = $sum_row['max_mCnt'];					// 남성합(최대)
			$maxtCnt = (double)$max_tCnt;
			$maxfCnt = (double)$max_fCnt;
			$maxmCnt = (double)$max_mCnt;

			$sum_tCnt = $sum_row['sum_tCnt'];						// 총합(최대)
			$sum_fCnt = $sum_row['sum_fCnt'];						// 여성합(최대)
			$sum_mCnt = $sum_row['sum_mCnt'];					// 남성합(최대)
			$sumtCnt = (double)$sum_tCnt;
			$sumfCnt = (double)$sum_fCnt;
			$summCnt = (double)$sum_mCnt;
/*			
			$maxtCnt = ((double)$max_tCnt / 5);					// 1단계 최대(총합)
			$maxtCnt2 = ((double)$maxtCnt * 2);					// 2단계 최대(총합)
			$maxtCnt3 = ((double)$maxtCnt * 3);					// 3단계 최대(총합)
			$maxtCnt4 = ((double)$maxtCnt * 4);					// 4단계 최대(총합)
			$maxtCnt5 = ((double)$maxtCnt * 5);					// 5단계 최대(총합)

			$maxfCnt = ((double)$max_fCnt / 5);					// 1단계 최대(여성)
			$maxfCnt2 = ((double)$maxfCnt * 2);					// 2단계 최대(여성)
			$maxfCnt3 = ((double)$maxfCnt * 3);					// 3단계 최대(여성)
			$maxfCnt4 = ((double)$maxfCnt * 4);					// 4단계 최대(여성)
			$maxfCnt5 = ((double)$maxfCnt * 5);					// 5단계 최대(여성)

			$maxmCnt = ((double)$max_mCnt / 5);					// 1단계 최대(남성)
			$maxmCnt2 = ((double)$maxmCnt * 2);				// 2단계 최대(남성)
			$maxmCnt3 = ((double)$maxmCnt * 3);				// 3단계 최대(남성)
			$maxmCnt4 = ((double)$maxmCnt * 4);				// 4단계 최대(남성)
			$maxmCnt5 = ((double)$maxmCnt * 5);				// 5단계 최대(남성)
*/
			$max_fCnt10 = $sum_row['max_fCnt10'];				// 여성10대(최대)
			$max_fCnt20 = $sum_row['max_fCnt20'];				// 여성20대(최대)
			$max_fCnt30 = $sum_row['max_fCnt30'];				// 여성30대(최대)
			$max_fCnt40 = $sum_row['max_fCnt40'];				// 여성40대(최대)
			$max_fCnt50 = $sum_row['max_fCnt50'];				// 여성50대(최대)
			$maxfCnt10 = (double)$max_fCnt10;
			$maxfCnt20 = (double)$max_fCnt20;
			$maxfCnt30 = (double)$max_fCnt30;
			$maxfCnt40 = (double)$max_fCnt40;
			$maxfCnt50 = (double)$max_fCnt50;	
			
			$sum_fCnt10 = $sum_row['sum_fCnt10'];				// 여성10대(최대)
			$sum_fCnt20 = $sum_row['sum_fCnt20'];				// 여성20대(최대)
			$sum_fCnt30 = $sum_row['sum_fCnt30'];				// 여성30대(최대)
			$sum_fCnt40 = $sum_row['sum_fCnt40'];				// 여성40대(최대)
			$sum_fCnt50 = $sum_row['sum_fCnt50'];				// 여성50대(최대)
			$sumfCnt10 = (double)$sum_fCnt10;
			$sumfCnt20 = (double)$sum_fCnt20;
			$sumfCnt30 = (double)$sum_fCnt30;
			$sumfCnt40 = (double)$sum_fCnt40;
			$sumfCnt50 = (double)$sum_fCnt50;
/*
			$maxfCnt10_1 = ($maxfCnt10 / 5);						// 1단계 최대(여성 10대)
			$maxfCnt10_2 = ($maxfCnt10_1 * 2);					// 2단계 최대(여성 10대)
			$maxfCnt10_3 = ($maxfCnt10_1 * 3);					// 3단계 최대(여성 10대)
			$maxfCnt10_4 = ($maxfCnt10_1 * 4);					// 4단계 최대(여성 10대)
			$maxfCnt10_5 = ($maxfCnt10_1 * 5);					// 5단계 최대(여성 10대)
			$maxfCnt20_1 = ($maxfCnt20 / 5);						// 1단계 최대(여성 20대)
			$maxfCnt20_2 = ($maxfCnt20_1 * 2);					// 2단계 최대(여성 20대)
			$maxfCnt20_3 = ($maxfCnt20_1 * 3);					// 3단계 최대(여성 20대)
			$maxfCnt20_4 = ($maxfCnt20_1 * 4);					// 4단계 최대(여성 20대)
			$maxfCnt20_5 = ($maxfCnt20_1 * 5);					// 5단계 최대(여성 20대)
			$maxfCnt30_1 = ($maxfCnt30 / 5);						// 1단계 최대(여성 30대)
			$maxfCnt30_2 = ($maxfCnt30_1 * 2);					// 2단계 최대(여성 30대)
			$maxfCnt30_3 = ($maxfCnt30_1 * 3);					// 3단계 최대(여성 30대)
			$maxfCnt30_4 = ($maxfCnt30_1 * 4);					// 4단계 최대(여성 30대)
			$maxfCnt30_5 = ($maxfCnt30_1 * 5);					// 5단계 최대(여성 30대)
			$maxfCnt40_1 = ($maxfCnt40 / 5);						// 1단계 최대(여성 40대)
			$maxfCnt40_2 = ($maxfCnt40_1 * 2);					// 2단계 최대(여성 40대)
			$maxfCnt40_3 = ($maxfCnt40_1 * 3);					// 3단계 최대(여성 40대)
			$maxfCnt40_4 = ($maxfCnt40_1 * 4);					// 4단계 최대(여성 40대)
			$maxfCnt40_5 = ($maxfCnt40_1 * 5);					// 5단계 최대(여성 40대)
			$maxfCnt50_1 = ($maxfCnt50 / 5);						// 1단계 최대(여성 40대)
			$maxfCnt50_2 = ($maxfCnt50_1 * 2);					// 2단계 최대(여성 40대)
			$maxfCnt50_3 = ($maxfCnt50_1 * 3);					// 3단계 최대(여성 40대)
			$maxfCnt50_4 = ($maxfCnt50_1 * 4);					// 4단계 최대(여성 40대)
			$maxfCnt50_5 = ($maxfCnt50_1 * 5);					// 5단계 최대(여성 40대)
*/
			$max_mCnt10 = $sum_row['max_mCnt10'];				// 남성10대(최대)
			$max_mCnt20 = $sum_row['max_mCnt20'];				// 남성20대(최대)
			$max_mCnt30 = $sum_row['max_mCnt30'];				// 남성30대(최대)
			$max_mCnt40 = $sum_row['max_mCnt40'];				// 남성40대(최대)
			$max_mCnt50 = $sum_row['max_mCnt50'];				// 남성50대(최대)
			$maxmCnt10 = (double)$max_mCnt10;
			$maxmCnt20 = (double)$max_mCnt20;
			$maxmCnt30 = (double)$max_mCnt30;
			$maxmCnt40 = (double)$max_mCnt40;
			$maxmCnt50 = (double)$max_mCnt50;

			$sum_mCnt10 = $sum_row['sum_mCnt10'];				// 남성10대(최대)
			$sum_mCnt20 = $sum_row['sum_mCnt20'];				// 남성20대(최대)
			$sum_mCnt30 = $sum_row['sum_mCnt30'];				// 남성30대(최대)
			$sum_mCnt40 = $sum_row['sum_mCnt40'];				// 남성40대(최대)
			$sum_mCnt50 = $sum_row['sum_mCnt50'];				// 남성50대(최대)
			$summCnt10 = (double)$sum_mCnt10;
			$summCnt20 = (double)$sum_mCnt20;
			$summCnt30 = (double)$sum_mCnt30;
			$summCnt40 = (double)$sum_mCnt40;
			$summCnt50 = (double)$sum_mCnt50;
/*
			$maxmCnt10_1 = ($maxmCnt10 / 5);				// 1단계 최대(남성)
			$maxmCnt10_2 = ($maxmCnt10_1 * 2);				// 2단계 최대(남성)
			$maxmCnt10_3 = ($maxmCnt10_1 * 3);				// 3단계 최대(남성)
			$maxmCnt10_4 = ($maxmCnt10_1 * 4);				// 4단계 최대(남성)
			$maxmCnt10_5 = ($maxmCnt10_1 * 5);				// 5단계 최대(남성)
			$maxmCnt20_1 = ($maxmCnt20 / 5);				// 1단계 최대(남성)
			$maxmCnt20_2 = ($maxmCnt20_1 * 2);				// 2단계 최대(남성)
			$maxmCnt20_3 = ($maxmCnt20_1 * 3);				// 3단계 최대(남성)
			$maxmCnt20_4 = ($maxmCnt20_1 * 4);				// 4단계 최대(남성)
			$maxmCnt20_5 = ($maxmCnt20_1 * 5);				// 5단계 최대(남성)
			$maxmCnt30_1 = ($maxmCnt30 / 5);				// 1단계 최대(남성)
			$maxmCnt30_2 = ($maxmCnt30_1 * 2);				// 2단계 최대(남성)
			$maxmCnt30_3 = ($maxmCnt30_1 * 3);				// 3단계 최대(남성)
			$maxmCnt30_4 = ($maxmCnt30_1 * 4);				// 4단계 최대(남성)
			$maxmCnt30_5 = ($maxmCnt30_1 * 5);				// 5단계 최대(남성)
			$maxmCnt40_1 = ($maxmCnt40 / 5);				// 1단계 최대(남성)
			$maxmCnt40_2 = ($maxmCnt40_1 * 2);				// 2단계 최대(남성)
			$maxmCnt40_3 = ($maxmCnt40_1 * 3);				// 3단계 최대(남성)
			$maxmCnt40_4 = ($maxmCnt40_1 * 4);				// 4단계 최대(남성)
			$maxmCnt40_5 = ($maxmCnt40_1 * 5);				// 5단계 최대(남성)
			$maxmCnt50_1 = ($maxmCnt50 / 5);				// 1단계 최대(남성)
			$maxmCnt50_2 = ($maxmCnt50_1 * 2);				// 2단계 최대(남성)
			$maxmCnt50_3 = ($maxmCnt50_1 * 3);				// 3단계 최대(남성)
			$maxmCnt50_4 = ($maxmCnt50_1 * 4);				// 4단계 최대(남성)
			$maxmCnt50_5 = ($maxmCnt50_1 * 5);				// 5단계 최대(남성)
*/

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
				if($maxtCnt5 < $tot_Cnt){
					$t_Weight = 1;
				}else if($maxtCnt4 < $tot_Cnt){
					$t_Weight = 2;
				}else if($maxtCnt3 < $tot_Cnt){
					$t_Weight = 2;
				}else if($maxtCnt2 < $tot_Cnt){
					$t_Weight = 2;
				}else{
					$t_Weight = 3;
				}
				$tot_Cnt = $row['tot_Cnt'];										// 총인구수
				$fCnt = $row['female_Cnt'];										// 여자인구수
				$mCnt = $row['male_Cnt'];										// 남자인구수
				$t_Cnt = $tot_Cnt * $t_Weight;								// 총인구수(가중치)
				$f_Cnt = $fCnt * $t_Weight;									// 여자인구수(가중치)
				$m_Cnt = $mCnt * $t_Weight;									// 남자인구수(가중치)

				$maxfCnt = (double)$max_fCnt;
				$maxmCnt = (double)$max_mCnt;

				$female10 = $row['female10'];									// 10대 여자
				$female20 = $row['female20'];									// 20대 여자
				$female30 = $row['female30'];									// 30대 여자
				$female40 = $row['female40'];									// 40대 여자
				$female50 = $row['female50'];									// 50대 여자
/*
				$mfrate_10 = (($female10 / $tot_Cnt) * 100);						// 여성 10대 비율
				if(80 < $mfrate_10){
					$mfrate_10 = 5;
				}else if(60 < $mfrate_10){
					$mfrate_10 = 4;
				}else if(40 < $mfrate_10){
					$mfrate_10 = 3;
				}else if(20 < $mfrate_10){
					$mfrate_10 = 2;
				}else{
					$mfrate_10 = 1;
				}
				$mfrate_20 = (($female20 / $tot_Cnt) * 100);						// 여성 20대 비율
				if(80 < $mfrate_20){
					$mfrate_20 = 5;
				}else if(60 < $mfrate_20){
					$mfrate_20 = 4;
				}else if(40 < $mfrate_20){
					$mfrate_20 = 3;
				}else if(20 < $mfrate_20){
					$mfrate_20 = 2;
				}else{
					$mfrate_20 = 1;
				}
				$mfrate_30 = (($female30 / $maxfCnt30) * 100);						// 여성 30대 비율
				if(80 < $mfrate_30){
					$mfrate_30 = 5;
				}else if(60 < $mfrate_30){
					$mfrate_30 = 4;
				}else if(40 < $mfrate_30){
					$mfrate_30 = 3;
				}else if(20 < $mfrate_30){
					$mfrate_30 = 2;
				}else{
					$mfrate_30 = 1;
				}
				$mfrate_40 = (($female40 / $maxfCnt40) * 100);						// 여성 40대 비율
				if(80 < $mfrate_40){
					$mfrate_40 = 5;
				}else if(60 < $mfrate_40){
					$mfrate_40 = 4;
				}else if(40 < $mfrate_40){
					$mfrate_40 = 3;
				}else if(20 < $mfrate_40){
					$mfrate_40 = 2;
				}else{
					$mfrate_40 = 1;
				}
				$mfrate_50 = (($female50 / $maxfCnt50) * 100);						// 여성 50대 비율
				if(80 < $mfrate_50){
					$mfrate_50 = 5;
				}else if(60 < $mfrate_50){
					$mfrate_50 = 4;
				}else if(40 < $mfrate_50){
					$mfrate_50 = 3;
				}else if(20 < $mfrate_50){
					$mfrate_50 = 2;
				}else{
					$mfrate_50 = 1;
				}
*/
				$male10 = $row['male10'];										// 10대 남자
				$male20 = $row['male20'];										// 20대 남자
				$male30 = $row['male30'];										// 30대 남자
				$male40 = $row['male40'];										// 40대 남자
				$male50 = $row['male50'];										// 50대 남자
/*
				$mmrate_10 = (($male10 / $maxmCnt10) * 100);						// 남성 10대 비율
				if(80 < $mmrate_10){
					$mmrate_10 = 5;
				}else if(60 < $mmrate_10){
					$mmrate_10 = 4;
				}else if(40 < $mmrate_10){
					$mmrate_10 = 3;
				}else if(20 < $mmrate_10){
					$mmrate_10 = 2;
				}else{
					$mmrate_10 = 1;
				}
				$mmrate_20 = (($male20 / $maxmCnt20) * 100);						// 남성 20대 비율
				if(80 < $mmrate_20){
					$mmrate_20 = 5;
				}else if(60 < $mmrate_20){
					$mmrate_20 = 4;
				}else if(40 < $mmrate_20){
					$mmrate_20 = 3;
				}else if(20 < $mmrate_20){
					$mmrate_20 = 2;
				}else{
					$mmrate_20 = 1;
				}
				$mmrate_30 = (($male30 / $maxmCnt30) * 100);						// 남성 30대 비율
				if(80 < $mmrate_30){
					$mmrate_30 = 5;
				}else if(60 < $mmrate_30){
					$mmrate_30 = 4;
				}else if(40 < $mmrate_30){
					$mmrate_30 = 3;
				}else if(20 < $mmrate_30){
					$mmrate_30 = 2;
				}else{
					$mmrate_30 = 1;
				}
				$mmrate_40 = (($male40 / $maxmCnt40) * 100);						// 남성 40대 비율
				if(80 < $mmrate_40){
					$mmrate_40 = 5;
				}else if(60 < $mmrate_40){
					$mmrate_40 = 4;
				}else if(40 < $mmrate_40){
					$mmrate_40 = 3;
				}else if(20 < $mmrate_40){
					$mmrate_40 = 2;
				}else{
					$mmrate_40 = 1;
				}
				$mmrate_50 = (($male50 / $maxmCnt50) * 100);						// 남성 50대 비율
				if(80 < $mmrate_50){
					$mmrate_50 = 5;
				}else if(60 < $mmrate_50){
					$mmrate_50 = 4;
				}else if(40 < $mmrate_50){
					$mmrate_50 = 3;
				}else if(20 < $mmrate_50){
					$mmrate_50 = 2;
				}else{
					$mmrate_50 = 1;
				}
*/
				$fRate =  ((($f_Cnt) / ($t_Cnt)) * 100);						// 여성비율
				$mRate = ((($m_Cnt) / ($t_Cnt)) * 100);						// 남성비율
				$mRate2 = ($mRate / 10);										// 남성비율을 10으로 나누기
				$poiresult = [(double)$lng, (double)$lat];					// 좌표를 숫자형으로
				$frate_10 = (($female10 / $fCnt) * 100);						// 여성 10대 비율
				if(80 < $frate_10){
					$mfrate_10 = 5;
				}else if(60 < $frate_10){
					$mfrate_10 = 4;
				}else if(40 < $frate_10){
					$mfrate_10 = 3;
				}else if(20 < $frate_10){
					$mfrate_10 = 2;
				}else{
					$mfrate_10 = 1;
				}
				$frate_20 = (($female20 / $fCnt) * 100);						// 여성 20대 비율
				if(80 < $frate_20){
					$mfrate_20 = 5;
				}else if(60 < $frate_20){
					$mfrate_20 = 4;
				}else if(40 < $frate_20){
					$mfrate_20 = 3;
				}else if(20 < $frate_20){
					$mfrate_20 = 2;
				}else{
					$mfrate_20 = 1;
				}
				$frate_30 = (($female30 / $fCnt) * 100);						// 여성 30대 비율
				if(80 < $frate_30){
					$mfrate_30 = 5;
				}else if(60 < $frate_30){
					$mfrate_30 = 4;
				}else if(40 < $frate_30){
					$mfrate_30 = 3;
				}else if(20 < $frate_30){
					$mfrate_30 = 2;
				}else{
					$mfrate_30 = 1;
				}
				$frate_40 = (($female40 / $fCnt) * 100);						// 여성 40대 비율
				if(80 < $frate_40){
					$mfrate_40 = 5;
				}else if(60 < $frate_40){
					$mfrate_40 = 4;
				}else if(40 < $frate_40){
					$mfrate_40 = 3;
				}else if(20 < $frate_40){
					$mfrate_40 = 2;
				}else{
					$mfrate_40 = 1;
				}
				$frate_50 = (($female50 / $fCnt) * 100);						// 여성 50대 비율
				if(80 < $frate_50){
					$mfrate_50 = 5;
				}else if(60 < $frate_50){
					$mfrate_50 = 4;
				}else if(40 < $frate_50){
					$mfrate_50 = 3;
				}else if(20 < $frate_50){
					$mfrate_50 = 2;
				}else{
					$mfrate_50 = 1;
				}
				$mrate_10 = (($male10 / $mCnt) * 100);					// 남성 10대 비율
				if(80 < $mrate_10){
					$mmrate_10 = 5;
				}else if(60 < $mrate_10){
					$mmrate_10 = 4;
				}else if(40 < $mrate_10){
					$mmrate_10 = 3;
				}else if(20 < $mrate_10){
					$mmrate_10 = 2;
				}else{
					$mmrate_10 = 1;
				}
				$mrate_20 = (($male20 / $mCnt) * 100);					// 남성 20대 비율
				if(80 < $mrate_20){
					$mmrate_20 = 5;
				}else if(60 < $mrate_20){
					$mmrate_20 = 4;
				}else if(40 < $mrate_20){
					$mmrate_20 = 3;
				}else if(20 < $mrate_20){
					$mmrate_20 = 2;
				}else{
					$mmrate_20 = 1;
				}
				$mrate_30 = (($male30 / $mCnt) * 100);					// 남성 30대 비율
				if(80 < $mrate_30){
					$mmrate_30 = 5;
				}else if(60 < $mrate_30){
					$mmrate_30 = 4;
				}else if(40 < $mrate_30){
					$mmrate_30 = 3;
				}else if(20 < $mrate_30){
					$mmrate_30 = 2;
				}else{
					$mmrate_30 = 1;
				}
				$mrate_40 = (($male40 / $mCnt) * 100);					// 남성 40대 비율
				if(80 < $mrate_40){
					$mmrate_40 = 5;
				}else if(60 < $mrate_40){
					$mmrate_40 = 4;
				}else if(40 < $mrate_40){
					$mmrate_40 = 3;
				}else if(20 < $mrate_40){
					$mmrate_40 = 2;
				}else{
					$mmrate_40 = 1;
				}
				$mrate_50 = (($male50 / $mCnt) * 100);					// 남성 50대 비율
				if(80 < $mrate_50){
					$mmrate_50 = 5;
				}else if(60 < $mrate_50){
					$mmrate_50 = 4;
				}else if(40 < $mrate_50){
					$mmrate_50 = 3;
				}else if(20 < $mrate_50){
					$mmrate_50 = 2;
				}else{
					$mmrate_50 = 1;
				}
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
				$sfrate_10  = (($female10 / $sumfCnt10) * 10000);		// 여성 10대 비율
				if(80 < $sfrate_10){
					$sfrate_10 = 5;
				}else if(60 < $sfrate_10){
					$sfrate_10 = 4;
				}else if(40 < $sfrate_10){
					$sfrate_10 = 3;
				}else if(20 < $sfrate_10){
					$sfrate_10 = 2;
				}else{
					$sfrate_10 = 1;
				}
				$sfrate_20  = (($female20 / $sumfCnt20) * 10000);		// 여성 20대 비율
				if(80 < $sfrate_20){
					$sfrate_20 = 5;
				}else if(60 < $sfrate_20){
					$sfrate_20 = 4;
				}else if(40 < $sfrate_20){
					$sfrate_20 = 3;
				}else if(20 < $sfrate_20){
					$sfrate_20 = 2;
				}else{
					$sfrate_20 = 1;
				}
				$sfrate_30  = (($female30 / $sumfCnt30) * 10000);		// 여성 30대 비율
				if(80 < $sfrate_30){
					$sfrate_30 = 5;
				}else if(60 < $sfrate_30){
					$sfrate_30 = 4;
				}else if(40 < $sfrate_30){
					$sfrate_30 = 3;
				}else if(20 < $sfrate_30){
					$sfrate_30 = 2;
				}else{
					$sfrate_30 = 1;
				}
				$sfrate_40  = (($female40 / $sumfCnt40) * 10000);		// 여성 40대 비율
				if(80 < $sfrate_40){
					$sfrate_40 = 5;
				}else if(60 < $sfrate_40){
					$sfrate_40 = 4;
				}else if(40 < $sfrate_40){
					$sfrate_40 = 3;
				}else if(20 < $sfrate_40){
					$sfrate_40 = 2;
				}else{
					$sfrate_40 = 1;
				}
				$sfrate_50  = (($female50 / $sumfCnt50) * 10000);		// 여성 50대 비율
				if(80 < $sfrate_50){
					$sfrate_50 = 5;
				}else if(60 < $sfrate_50){
					$sfrate_50 = 4;
				}else if(40 < $sfrate_50){
					$sfrate_50 = 3;
				}else if(20 < $sfrate_50){
					$sfrate_50 = 2;
				}else{
					$sfrate_50 = 1;
				}

				$smrate_10  = (($male10 / $summCnt10) * 10000);		// 남성 10대 비율
				if(80 < $smrate_10){
					$smrate_10 = 5;
				}else if(60 < $smrate_10){
					$smrate_10 = 4;
				}else if(40 < $smrate_10){
					$smrate_10 = 3;
				}else if(20 < $smrate_10){
					$smrate_10 = 2;
				}else{
					$smrate_10 = 1;
				}

				$smrate_20  = (($male20 / $summCnt20) * 10000);		// 남성 20대 비율
				if(80 < $smrate_20){
					$smrate_20 = 5;
				}else if(60 < $smrate_20){
					$smrate_20 = 4;
				}else if(40 < $smrate_20){
					$smrate_20 = 3;
				}else if(20 < $smrate_20){
					$smrate_20 = 2;
				}else{
					$smrate_20 = 1;
				}

				$smrate_30  = (($male30 / $summCnt30) * 10000);		// 남성 30대 비율
				if(80 < $smrate_30){
					$smrate_30 = 5;
				}else if(60 < $smrate_30){
					$smrate_30 = 4;
				}else if(40 < $smrate_30){
					$smrate_30 = 3;
				}else if(20 < $smrate_30){
					$smrate_30 = 2;
				}else{
					$smrate_30 = 1;
				}

				$smrate_40  = (($male40 / $summCnt40) * 10000);		// 남성 40대 비율
				if(80 < $smrate_40){
					$smrate_40 = 5;
				}else if(60 < $smrate_40){
					$smrate_40 = 4;
				}else if(40 < $smrate_40){
					$smrate_40 = 3;
				}else if(20 < $smrate_40){
					$smrate_40 = 2;
				}else{
					$smrate_40 = 1;
				}

				$smrate_50  = (($male50 / $summCnt50) * 10000);		// 남성 50대 비율
				if(80 < $smrate_50){
					$smrate_50 = 5;
				}else if(60 < $smrate_50){
					$smrate_50 = 4;
				}else if(40 < $smrate_50){
					$smrate_50 = 3;
				}else if(20 < $smrate_50){
					$smrate_50 = 2;
				}else{
					$smrate_50 = 1;
				}

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
					"memo1" => "기본값",
					"frate10" => $mfrate_10,
					"frate20" => $mfrate_20,
					"frate30" => $mfrate_30,
					"frate40" => $mfrate_40,
					"frate50" => $mfrate_50,
					"mrate10" => $mmrate_10,
					"mrate20" => $mmrate_20,
					"mrate30" => $mmrate_30,
					"mrate40" => $mmrate_40,
					"mrate50" => $mmrate_50,
					"memo2" => "임의의 가중치",
					"sfrate10" => $sfrate_10,
					"sfrate20" => $sfrate_20,
					"sfrate30" => $sfrate_30,
					"sfrate40" => $sfrate_40,
					"sfrate50" => $sfrate_50,
					"smrate10" => $smrate_10,
					"smrate20" => $smrate_20,
					"smrate30" => $smrate_30,
					"smrate40" => $smrate_40,
					"smrate50" => $smrate_50,
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
