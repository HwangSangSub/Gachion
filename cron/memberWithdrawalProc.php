#!/usr/bin/php -q
<?php
/*======================================================================================================================

* 프로그램			: 쿠폰 유효기간 확인 후 사용완료 및 기간만료 처리 (매일 00:00분 처리)
* 페이지 설명		: 쿠폰 유효기간 확인 후 사용완료 및 기간만료 처리
* 파일명				: memberWithdrawalProc.php

========================================================================================================================*/

// register_globals off 처리
@extract($_GET);
@extract($_POST);
@extract($_SERVER);
@extract($_ENV);
@extract($_SESSION);
@extract($_COOKIE);
@extract($_REQUEST);
@extract($_FILES);

ob_start();

header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

//구글 fcm키
define("GOOGLE_API_KEY", "AAAAMcrX0Us:APA91bGxkQmw8lrvxlB2w1sLp-zwr-LxiId1u1DSIRKMygN041qutEOxH2oMI5_q_QwGLXx4ve6r1fU47B59T9C-Fx6H438D9V-BW2RzT0WpLhtkiVXgsJsmf-x7j6DUfUB30Li-wcOs");
	include 'inc/dbcon.php';


	$DB_con = db1();

    $now_Time = date('Y-m-d');	 //등록일

	//사용 가능한 쿠폰이면서 유효만료기간이 지난 쿠폰 조회
	$Query = "";
	$Query .= "SELECT A.idx FROM TB_COUPON A";
	$Query .= " WHERE A.b_Disply = 'Y' AND A.cou_Edate < ':now_Time';";
	//echo $Query1."<BR>";
	//exit;
	$Stmt = $DB_con->prepare($Query);
	$Stmt->bindparam(":now_Time",$now_Time);
	$Stmt->execute();
	$row=$Stmt->fetch(PDO::FETCH_ASSOC);
	//$cnt =  $row['cnt'];						// 유효만료기간지난 쿠폰 조회
	
	$num = $Stmt->rowCount();
	//echo $num."<BR>";
	if($num == ''){$num = 0;}
	if($num < 1)  { //아닐경우
		$result = array("result" => "error", "Msg" => "사용완료 및 기간만료 쿠폰이 없습니다.");
	} else {
		$Query2 = "";
		$Query2 .= "SELECT A.idx, A.cou_PType, B.cou_MemId, B.cou_SMemId FROM TB_COUPON A INNER JOIN TB_COUPON_USE B ON A.idx = B.cou_Idx";
		$Query2 .= " WHERE A.b_Disply = 'Y' AND A.cou_Edate < ':now_Time' AND B.cou_Use = 'N' GROUP BY A.idx, A.cou_PType, B.cou_MemId, B.cou_SMemId;";
		//echo $Query1."<BR>";
		//exit;
		$Stmt2 = $DB_con->prepare($Query2);
		$Stmt2->bindparam(":now_Time",$now_Time);
		$Stmt2->execute();
		while($row2=$Stmt2->fetch(PDO::FETCH_ASSOC)) {
			$cou_Idx =  $row2['idx'];						// 쿠폰고유번호
			$cou_PType =  $row2['cou_PType'];				// 쿠폰종류 (0: 캐시적립형 / 1: 수수료할인형)
			$cou_MemId = $row2['cou_MemId'];
			$cou_SMemId = $row2['cou_SMemId'];
			if($cou_PType == 1){	// 수수료할인형				(구분하는 이유는 캐시적립은 자동으로 사용완료처리, 수수료할인형은 수동으로 완료처리 및 기간만료임을 수정)
				//쿠폰 사용 불가 처리 
				$upQuery1 = "UPDATE TB_COUPON SET b_Disply = 'N' WHERE idx = :idx";
				$upStmt1 = $DB_con->prepare($upQuery1);
				$upStmt1->bindparam(":idx",$cou_Idx);
				$upStmt1->execute();

				//사용자의 등록된 쿠폰을 기간만료 처리
				$upQuery2 = "UPDATE TB_COUPON_USE SET cou_Use = 'Y', cou_Chk = '1' WHERE cou_Idx = :cou_Idx";
				$upStmt2 = $DB_con->prepare($upQuery2);
				$upStmt2->bindparam(":cou_Idx",$cou_Idx);
				$upStmt2->execute();
			
				//등록 쿠폰 수 감소
				$chkQuery = "SELECT mem_Coupon FROM TB_MEMBERS_ETC WHERE mem_SId = :mem_SId AND mem_Id = :mem_Id";
				$chkStmt = $DB_con->prepare($chkQuery);
				$chkStmt->bindparam(":mem_SId",$cou_SMemId);
				$chkStmt->bindparam(":mem_Id",$cou_MemId);
				$chkStmt->execute();
				while($chkrow = $chkStmt->fetch(PDO::FETCH_ASSOC)){
					$mem_Coupon =  $chkrow['mem_Coupon'];						// 등록쿠폰 수 
				}
				$memCoupon = ((int)$mem_Coupon - 1);
				$upQuery3 = "UPDATE TB_MEMBERS_ETC SET mem_Coupon = :mem_Coupon WHERE mem_SId = :mem_SId AND mem_Id = :mem_Id";
				$upStmt3 = $DB_con->prepare($upQuery3);
				$upStmt3->bindparam(":mem_Coupon",$memCoupon);
				$upStmt3->bindparam(":mem_SId",$cou_SMemId);
				$upStmt3->bindparam(":mem_Id",$cou_MemId);
				$upStmt3->execute();
			}else{					// 캐시적립형
				//쿠폰 사용 불가 처리 
				$upQuery1 = "UPDATE TB_COUPON SET b_Disply = 'N' WHERE idx = :idx";
				$upStmt1 = $DB_con->prepare($upQuery1);
				$upStmt1->bindparam(":idx",$cou_Idx);
				$upStmt1->execute();
			}
		}
		$result = array("result" => "success");
	}

	dbClose($DB_con);
	$Stmt = null;
	$upStmt1 = null;
	$upStmt2 = null;
	echo "
".str_replace('\\/', '/', json_encode($result, JSON_UNESCAPED_UNICODE));
?>