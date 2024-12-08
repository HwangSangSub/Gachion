<?php
/*======================================================================================================================

* 프로그램				:  연장신청 승인처리
* 페이지 설명			:  연장신청 승인처리
* 파일명					:  memberExtendProc.php

========================================================================================================================*/
include "../../lib/common.php";
include "../../lib/functionDB.php";  //공통 db함수

$idx = trim($idx);					//고유번호 (노선번호)

$DB_con = db1();

$regDate = DU_TIME_YMDHIS;  //시간등록

//노선확인
$chkQuery = "SELECT idx, mem_Id, exte_Date FROM TB_EXTEND_LIST WHERE idx = :idx LIMIT 1 ";
//echo $chkQuery."<BR>";
//exit;
$chkStmt = $DB_con->prepare($chkQuery);
$chkStmt->bindparam(":idx",$idx);
$chkStmt->execute();
$chkNum = $chkStmt->rowCount();
//echo $mapNum."<BR>";

if($chkNum < 1)  { //아닐경우
	$result['success']	= false;
	$result['Msg']	= "해당 요청건은 없습니다. 다시 확인 후 진행해주세요.";
} else {
	while($chkRow=$chkStmt->fetch(PDO::FETCH_ASSOC)) {
		$idx = $chkRow['idx'];					// 생성노선번호
		$exte_Date = $chkRow['exte_Date'];			// 생성노선상태값
		$mem_Id = $chkRow['mem_Id'];			// 생성노선상태값
		$chkUQuery = "SELECT use_Date FROM TB_MEMBERS WHERE mem_Id = :mem_Id LIMIT 1 ; ";
		//echo $chkQuery."<BR>";
		//exit;
		$chkUStmt = $DB_con->prepare($chkUQuery);
		$chkUStmt->bindparam(":mem_Id",$mem_Id);
		$chkUStmt->execute();
		$chkURow=$chkUStmt->fetch(PDO::FETCH_ASSOC);
		$use_Date = $chkURow['use_Date'];			// 생성노선상태값
		if($exte_Date == '1'){
			$timestamp = strtotime("+30 days", strtotime($use_Date));
		}else if($exte_Date == '2'){
			$timestamp = strtotime("+60 days", strtotime($use_Date));
		}else if($exte_Date == '3'){
			$timestamp = strtotime("+90 days", strtotime($use_Date));
		}
		//사용일! 입력하기
		$useDate = date("Y-m-d", $timestamp);	

		//승인처리 및 승인일 입력
		$upQquery1 = "
			UPDATE TB_MEMBERS 
			SET use_Date = :use_Date, mod_Date = NOW() 
			WHERE mem_Id = :mem_Id ;";
		$upStmt1 = $DB_con->prepare($upQquery1);
		$upStmt1->bindparam(":mem_Id",$mem_Id);
		$upStmt1->bindparam(":use_Date",$useDate);
		$upStmt1->execute();

		//승인처리 및 승인일 입력
		$upQquery2 = "
			UPDATE TB_EXTEND_LIST 
			SET exte_Bit = 'Y', mod_Date = NOW() 
			WHERE idx = :idx ;";
		$upStmt2 = $DB_con->prepare($upQquery2);
		$upStmt2->bindparam(":idx",$idx);
		$upStmt2->execute();
	}
	$result['success']	= true;
	$result['Msg']	= "해당 요청을 승인하였습니다.";
}

					
	
dbClose($DB_con);

$chkStmt = null;
$chkUStmt = null;
$upStmt1 = null;
$upStmt2 = null;
	
echo json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
?>