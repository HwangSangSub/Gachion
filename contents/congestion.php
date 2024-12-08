<?
/*
* 프로그램				: 혼잡도 정보를 저장
* 페이지 설명			: 혼잡도 정보를 저장
* 파일명					: congestion.php
* 관련DB					: TB_CONGESTION
*/
include "../lib/common.php";
$area_Code = trim($area_Code);					//kml 다각형 이름
$place_Idx = trim($place_Idx);						//핀고유번호
if ($area_Code != "") {
    
    $DB_con = db1();
	
	//회원 기본테이블 저장
	if($place_Idx != ""){
		//전체회원수확인
		$sum_query = "
			SELECT SUM(tot_Cnt) as cnt
			FROM TB_CONGESTION
			WHERE area_Code = :area_Code
				AND place_Idx = :place_Idx;
		";
		$sum_stmt = $DB_con->prepare($sum_query);
		$sum_stmt->bindParam(":area_Code", $area_Code);
		$sum_stmt->bindParam(":place_Idx", $place_Idx);
		$sum_stmt->execute();
		$sum_row=$sum_stmt->fetch(PDO::FETCH_ASSOC);
		$total_cnt = $sum_row['cnt'];											// 총사람수

		//관련리스트 보기
		$chk_query = "
			SELECT idx, tot_Cnt
			FROM TB_CONGESTION
			WHERE area_Code = :area_Code
				AND place_Idx = :place_Idx;
		";
		$chk_stmt = $DB_con->prepare($chk_query);
		$chk_stmt->bindParam(":area_Code", $area_Code);
		$chk_stmt->bindParam(":place_Idx", $place_Idx);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
	}else{
		//전체회원수확인
		$sum_query = "
			SELECT SUM(tot_Cnt) as cnt
			FROM TB_CONGESTION
			WHERE area_Code = :area_Code
		";
		$sum_stmt = $DB_con->prepare($sum_query);
		$sum_stmt->bindParam(":area_Code", $area_Code);
		$sum_stmt->execute();
		$sum_row=$sum_stmt->fetch(PDO::FETCH_ASSOC);
		$total_cnt = $sum_row['cnt'];											// 총사람수

		//관련리스트 보기
		$chk_query = "
			SELECT idx, tot_Cnt
			FROM TB_CONGESTION
			WHERE area_Code = :area_Code;
		";
		$chk_stmt = $DB_con->prepare($chk_query);
		$chk_stmt->bindParam(":area_Code", $area_Code);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
	}

	while($chk_row=$chk_stmt->fetch(PDO::FETCH_ASSOC)){
		$idx = $chk_row['idx'];													// 고유번호
		$tot_Cnt = $chk_row['tot_Cnt'];										// 총사람수
		$cong_Rate = (($tot_Cnt / ($total_cnt / $chk_num)) * 100);	// 혼잡정도
		$int_Rate = (int)$cong_Rate;

		if(80 < $int_Rate){
			$congRate = '5';
		}else if(60 < $int_Rate){
			$congRate = '4';
		}else if(40 < $int_Rate){
			$congRate = '3';
		}else if(20 < $int_Rate){
			$congRate = '2';
		}else{
			$congRate = '1';
		}
		/*
		if($int_Rate < '20'){
			$congRate = '1';
		}else if('20' <= $int_Rate < '40'){
			$congRate = '2';
		}else if('40' <= $int_Rate < '60'){
			$congRate = '3';
		}else if('60' <= $int_Rate < '80'){
			$congRate = '4';
		}else if('80' <= $int_Rate){
			$congRate = '5';
		}
		*/
		//혼잡정도 업데이트
		$up_query = "
			UPDATE TB_CONGESTION
			SET cong_Rate = :cong_Rate
			WHERE idx = :idx
			LIMIT 1;
		";
		$up_stmt = $DB_con->prepare($up_query);
		$up_stmt->bindParam(":cong_Rate", $congRate);
		$up_stmt->bindParam(":idx", $idx);
		$up_stmt->execute();
	}
	$result = array("result" => "success");
    dbClose($DB_con);
	$sum_stmt = null;
	$chk_stmt = null;
    $up_stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "해당 지역 코드 오류");
}
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
?>



