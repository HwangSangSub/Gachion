<?
/*
* 프로그램				: 콘텐츠 정보 통계확인
* 페이지 설명			: 지역별 총인구수, 지역별 전체혼잡도, 지역별 남여성별비율
* 파일명					: info_contents.php
* 관련DB					: TB_CONGESTION, TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($con_Idx);						//콘텐츠고유번호 

$DB_con = db1();
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
	$area_query = "
		SELECT area_Code
		FROM TB_CONTENTS
		WHERE idx = :con_Idx;
	";
	$area_stmt = $DB_con->prepare($area_query);
	$area_stmt->bindParam(":con_Idx", $con_Idx);
	$area_stmt->execute();
	$area_row=$area_stmt->fetch(PDO::FETCH_ASSOC);
	$area_Code = $area_row['area_Code'];											// 총사람수
	$areaCode = explode( ',', $area_Code);
	$poi_cnt = count($areaCode);
	$data = [];
	for($i = 0; $i < $poi_cnt; $i++){
		$chk_query2 = "
				SELECT *
				FROM TB_CONGESTION
				WHERE area_Code = :area_Code;
		";
		$chk_stmt2 = $DB_con->prepare($chk_query2);
		$chk_stmt2->bindParam(":area_Code", $areaCode[$i]);
		$chk_stmt2->execute();
		$chk_num2 = $chk_stmt2->rowCount();
		if($chk_num2 < 1){
			$mresult = ["area_Name" => $areaCode[$i], "people_Cnt" => "0", "male_Rate" => "0", "female_Rate" => "0","cong_Cnt" => "0"];
			 array_push($data, $mresult);
		}else{
			$query = "
				SELECT count(idx) as tCnt, SUM(tot_Cnt) as pCnt, SUM(cong_Rate) as cCnt, SUM(male_Cnt) as mCnt, SUM(female_Cnt) as fCnt
				FROM TB_CONGESTION
				WHERE area_Code = :area_Code;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":area_Code", $areaCode[$i]);
			$stmt->execute();
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$tCnt = $row['tCnt'];								// 총행수
				$pCnt = $row['pCnt'];							// 총인구수
				$mCnt = $row['mCnt'];							// 남자인구수
				$fCnt = $row['fCnt'];								// 여자인구수
				$cCnt = $row['cCnt'];								// 총혼잡정도점수
				$mRate = (($mCnt / $pCnt) * 100);
				$fRate =  (($fCnt / $pCnt) * 100);
				$cong_Cnt = ((int)$cCnt / (int)$tCnt);		// 평균혼잡정도
				$mresult = ["area_Name" => $areaCode[$i], "people_Cnt" => (string)$pCnt, "male_Rate" => (string)(round($mRate)), "female_Rate" => (string)(round($fRate)),"cong_Cnt" => (string)$cong_Cnt];
				 array_push($data, $mresult);
			}
		}
	}

	//총 핀의 갯수
	$cnt_query = "
		SELECT count(idx) as cnt
		FROM TB_PLACE
		WHERE con_Idx = :con_Idx;
	";
	$cnt_stmt = $DB_con->prepare($cnt_query);
	$cnt_stmt->bindParam(":con_Idx", $con_Idx);
	$cnt_stmt->execute();
	$cnt_row=$cnt_stmt->fetch(PDO::FETCH_ASSOC);
	$pin_cnt = $cnt_row['cnt'];											// 총 핀의 갯수

	$chkData = [];
	$chkData["result"] = "success";
	$chkData["pin_cnt"] = $pin_cnt;
	$chkData["lists"] = $data;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);
}

    dbClose($DB_con);
    $stmt = null;
	$sum_stmt = null;
	$cnt_stmt = null;
?>