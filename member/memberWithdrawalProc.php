<?
header('Content-Type: application/json; charset=UTF-8');
/*======================================================================================================================

* 프로그램			: 회원탈퇴처리
* 페이지 설명		: 회원탈퇴처리
* 파일명				: memberWithdrawalProc.php

========================================================================================================================*/

include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
include "../lib/functionWithdrawal.php";  //회원탈퇴 관련

$mem_Id = trim($memId);				//아이디

if ($mem_Id != "") {  //아이디가 있을 경우
	$DB_con = db1();

	//회원 이미지 삭제
	memImgDel($mem_Id);

	//탈퇴처리
	$chkProcNum = memDate($mem_Id);

	if ($chkProcNum == "0") {
		$result = array("result" => "error", "errorMsg" => "이미 탈퇴한 회원 혹은 가입되지 않은 회원 입니다.");
	} else {
		$msg = "회원이 정상적으로 탈퇴 처리되었습니다. 저희 리얼플레이스를 이용해 주셔서 감사합니다.";
		$result = array("result" => "success", "msg" => $msg);
	}
}

dbClose($DB_con);

echo str_replace('\\/', '/', json_encode($result, JSON_UNESCAPED_UNICODE));
