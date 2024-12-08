<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 콘텐츠 정보 통계확인
* 페이지 설명			: 지역별 총인구수, 지역별 전체혼잡도, 지역별 남여성별비율
* 파일명					: info_contents.php
* 관련DB					: TB_CONGESTION, TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($con_Idx);						// 콘텐츠고유번호 
$area_Code = trim($area_Code);				// 지역코드

$DB_con = db1();
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

$now_Date = strtotime("2019-10-24 23:21:12");
$sql_search ="  AND A.reg_Date = (SELECT MAX(A.reg_Date) FROM TB_CONGESTION A INNER JOIN TB_PLACE B ON A.place_Idx = B.idx WHERE (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= '2019-10-24 22:21:12' AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= '2019-10-24 23:21:12'))";
/*
$time_Chk = "0";
$nowDate = date("Y-m-d H:i:s", $now_Date);
$time1 = (int)$time_Chk;
$time2 = (int)$time_Chk + 1;
$timestamp = strtotime($nowDate."-".$time1." hours");
$timestamp2 = strtotime($nowDate."-".$time2." hours");
$to_date = date("Y-m-d H:i:s", $timestamp);
$fr_date = date("Y-m-d H:i:s", $timestamp2);
$sql_search ="  AND A.reg_Date = (SELECT MAX(A.reg_Date) FROM TB_CONGESTION A INNER JOIN TB_PLACE B ON A.place_Idx = B.idx WHERE (DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') >= '".$fr_date."' AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d %H:%i:%s') <= '".$to_date."'))";*/

if($chk_num < 1){
	$result = array("result" => "error", "errorMsg" => "내역없음.");
}else{
	$query = "
		SELECT count(A.idx) as tCnt
			, SUM(A.tot_Cnt) as pCnt
			, SUM(A.female10) as fCnt10
			, SUM(A.female20) as fCnt20
			, SUM(A.female30) as fCnt30
			, SUM(A.female40) as fCnt40
			, SUM(A.female50) as fCnt50
			, SUM(A.male10) as mCnt10
			, SUM(A.male20) as mCnt20
			, SUM(A.male30) as mCnt30
			, SUM(A.male40) as mCnt40
			, SUM(A.male50) as mCnt50
			, SUM(A.male_Cnt) as mtCnt
			, SUM(A.female_Cnt) as ftCnt
			, SUM(A.n_Cnt) as n_Cnt
			, SUM(A.ne_Cnt) as ne_Cnt
			, SUM(A.e_Cnt) as e_Cnt
			, SUM(A.se_Cnt) as se_Cnt
			, SUM(A.s_Cnt) as s_Cnt
			, SUM(A.sw_Cnt) as sw_Cnt
			, SUM(A.w_Cnt) as w_Cnt
			, SUM(A.nw_Cnt) as nw_Cnt
		FROM TB_CONGESTION A
			INNER JOIN TB_PLACE B
				ON A.place_Idx = B.idx
		WHERE B.con_Idx = :con_Idx
			AND A.area_Code = :area_Code
			{$sql_search}
			;
	";//
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":con_Idx", $con_Idx);
	$stmt->bindParam(":area_Code", $area_Code);
	$stmt->execute();
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$tCnt = $row['tCnt'];								// 총행수
		$pCnt = $row['pCnt'];							// 총인구수

		$fCnt10 = $row['fCnt10'];						// 여자10대
		if($fCnt10 == ""){
			$fCnt10 = 0;
		}else{
			$fCnt10 = $fCnt10;
		}
		$fCnt20 = $row['fCnt20'];						// 여자20대
		if($fCnt20 == ""){
			$fCnt20 = 0;
		}else{
			$fCnt20 = $fCnt20;
		}
		$fCnt30 = $row['fCnt30'];						// 여자30대
		if($fCnt30 == ""){
			$fCnt30 = 0;
		}else{
			$fCnt30 = $fCnt30;
		}
		$fCnt40 = $row['fCnt40'];						// 여자40대
		if($fCnt40 == ""){
			$fCnt40 = 0;
		}else{
			$fCnt40 = $fCnt40;
		}
		$fCnt50 = $row['fCnt50'];						// 여자50대
		if($fCnt50 == ""){
			$fCnt50 = 0;
		}else{
			$fCnt50 = $fCnt50;
		}

		$mCnt10 = $row['mCnt10'];					// 남자10대
		if($mCnt10 == ""){
			$mCnt10 = 0;
		}else{
			$mCnt10 = $mCnt10;
		}
		$mCnt20 = $row['mCnt20'];					// 남자20대
		if($mCnt20 == ""){
			$mCnt20 = 0;
		}else{
			$mCnt20 = $mCnt20;
		}
		$mCnt30 = $row['mCnt30'];					// 남자30대
		if($mCnt30 == ""){
			$mCnt30 = 0;
		}else{
			$mCnt30 = $mCnt30;
		}
		$mCnt40 = $row['mCnt40'];					// 남자40대
		if($mCnt40 == ""){
			$mCnt40 = 0;
		}else{
			$mCnt40 = $mCnt40;
		}
		$mCnt50 = $row['mCnt50'];					// 남자50대
		if($mCnt50 == ""){
			$mCnt50 = 0;
		}else{
			$mCnt50 = $mCnt50;
		}

		$fCnt = $row['ftCnt'];							// 여자인구수
		$mCnt = $row['mtCnt'];							// 남자인구수

		$n_Cnt = $row['n_Cnt'];							// 북쪽유입
		$ne_Cnt = $row['ne_Cnt'];						// 북동쪽유입
		$e_Cnt = $row['e_Cnt'];							// 동쪽유입
		$se_Cnt = $row['se_Cnt'];						// 남동쪽유입
		$s_Cnt = $row['s_Cnt'];							// 남쪽유입
		$sw_Cnt = $row['sw_Cnt'];						// 남서쪽유입
		$w_Cnt = $row['w_Cnt'];							// 서쪽유입
		$nw_Cnt = $row['nw_Cnt'];						// 북서쪽유입
		
		$nesw_Cnt = $n_Cnt + $ne_Cnt + $e_Cnt + $se_Cnt + $s_Cnt + $sw_Cnt + $w_Cnt + $nw_Cnt;

		$n_Rate = (($n_Cnt / $nesw_Cnt) * 100);
		$ne_Rate = (($ne_Cnt / $nesw_Cnt) * 100);
		$e_Rate = (($e_Cnt / $nesw_Cnt) * 100);
		$se_Rate = (($se_Cnt / $nesw_Cnt) * 100);
		$s_Rate = (($s_Cnt / $nesw_Cnt) * 100);
		$sw_Rate = (($sw_Cnt / $nesw_Cnt) * 100);
		$w_Rate = (($w_Cnt / $nesw_Cnt) * 100);
		$nw_Rate = (($nw_Cnt / $nesw_Cnt) * 100);

		$cardinalArray = ["N" => $n_Cnt, "NE" => $ne_Cnt, "E" => $e_Cnt, "SE" => $se_Cnt, "S" => $s_Cnt, "SW" => $sw_Cnt, "W" => $w_Cnt, "NW" => $nw_Cnt];

		$neswArray = ["N" => round($n_Rate), "NE" => round($ne_Rate), "E" => round($e_Rate), "SE" => round($se_Rate), "S" => round($s_Rate), "SW" => round($sw_Rate), "W" => round($w_Rate), "NW" => round($nw_Rate)];

		$cntArray = $cardinalArray;
		rsort($cntArray);
		//print_r($cntArray);
		$cardinal_cnt = count($cntArray);
		$cardinal = [];
		for($i = 0; $i < 5; $i++){
			$cardinal_num = $cntArray[$i];
			array_push($cardinal, $cntArray[$i]);
		}
		$cardinal_idx = [];
		$poi_count = count($cardinal);
		for($j = 0; $j < $poi_count; $j++){
			$strIndex = array_search($cardinal[$j], $cardinalArray);
			array_push($cardinal_idx, $strIndex);
		}
		/*
		print_r($cardinalArray);		// 원본
		print_r($cntArray);				// 정렬
		print_r($cardinal);				// 랭킹5위 끊기
		print_r($cardinal_idx);			// 랭킹5위 인덱스값 가져오기
		print_r($neswArray);			// 비율가져오기
		exit;
		*/
		$fRate10 =  (($fCnt10 / $fCnt) * 100);			// 여자10대비율
		$fRate20 =  (($fCnt20 / $fCnt) * 100);			// 여자20대비율
		$fRate30 =  (($fCnt30 / $fCnt) * 100);			// 여자30대비율
		$fRate40 =  (($fCnt40 / $fCnt) * 100);			// 여자40대비율
		$fRate50 =  (($fCnt50 / $fCnt) * 100);			// 여자50대비율

		$mRate10 =  (($mCnt10 / $mCnt) * 100);		// 남자10대비율
		$mRate20 =  (($mCnt20 / $mCnt) * 100);		// 남자20대비율
		$mRate30 =  (($mCnt30 / $mCnt) * 100);		// 남자30대비율
		$mRate40 =  (($mCnt40 / $mCnt) * 100);		// 남자40대비율
		$mRate50 =  (($mCnt50 / $mCnt) * 100);		// 남자50대비율
		$result = array("result" => "success", "fRate10" => round($fRate10), "fRate20" => round($fRate20), "fRate30" => round($fRate30), "fRate40" => round($fRate40), "fRate50" => round($fRate50), "mRate10" => round($mRate10), "mRate20" => round($mRate20), "mRate30" => round($mRate30), "mRate40" => round($mRate40), "mRate50" => round($mRate50), "rank1" => $cardinal_idx['0'], "rank2" => $cardinal_idx['1'], "rank3" => $cardinal_idx['2'], "rank4" => $cardinal_idx['3'], "rank5" => $cardinal_idx['4'], "rank1_Rate" => $neswArray[$cardinal_idx['0']], "rank2_Rate" => $neswArray[$cardinal_idx['1']], "rank3_Rate" => $neswArray[$cardinal_idx['2']], "rank4_Rate" => $neswArray[$cardinal_idx['3']], "rank5_Rate" => $neswArray[$cardinal_idx['4']], "manyTime" => 23);
	}
}
	echo json_encode($result);
    dbClose($DB_con);
    $stmt = null;
?>