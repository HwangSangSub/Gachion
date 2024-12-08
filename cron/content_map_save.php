#!/usr/bin/php -q
<?php
/*======================================================================================================================

* 프로그램			: 지도 맵 이미지 캡처
* 페이지 설명		: 지도 맵 이미지 캡처
* 파일명				: content_map_save.php

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

	include 'inc/dbcon.php';

	$DB_con = db1();

	function mapImg($url, $param=array()){
		$url = $url.'?'.http_build_query($param, '', '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$contents = curl_exec($ch); 
		//print_r($contents);
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
		return $contents;
	}

	// 지도 맵 이미지 캡처 대기 리스트 조회
	$Query = " 
		SELECT COUNT(*) as cnt
		FROM TB_CONTENTS_MAP
		WHERE status = 'READY'
	;";
	//echo $Query1."<BR>";
	//exit;
	$Stmt = $DB_con->prepare($Query);
	$Stmt->execute();
	$row=$Stmt->fetch(PDO::FETCH_ASSOC);
	$cnt =  $row['cnt'];						// 대기수
	if($cnt == ''){$cnt = 0;}
	if($cnt < 1)  { //아닐경우
		$result = array("result" => "error", "Msg" => "내역없음.");
	} else {
		$Query2 = " 
			SELECT *
			FROM TB_CONTENTS_MAP
			WHERE status = 'READY'
		;";
		//echo $Query2."<BR>";
		//exit;
		$Stmt2 = $DB_con->prepare($Query2);
		$Stmt2->execute();
		while($row2=$Stmt2->fetch(PDO::FETCH_ASSOC)) {
			$idx =  $row2['idx'];						
			if($idx != ""){
				// 작업시작처리 (중복체크)
				$upQuery1 = "UPDATE TB_CONTENTS_MAP SET status = 'START' WHERE idx = :idx";
				$upStmt1 = $DB_con->prepare($upQuery1);
				$upStmt1->bindparam(":idx",$idx);
				$upStmt1->execute();
			}
			$conIdx =  $row2['con_Idx'];				
			$status = $row2['status'];
			$reg_Date = $row2['reg_Date'];
			//	echo "success";
			$mapImg = mapImg('https://places.gachita.kr/node/exec2.php',array("conIdx" => $conIdx));
			if($mapImg == "success"){
				//완료처리
				$upQuery2 = "UPDATE TB_CONTENTS_MAP SET status = 'COMPLETE' WHERE idx = :idx";
				$upStmt2 = $DB_con->prepare($upQuery2);
				$upStmt2->bindparam(":idx",$idx);
				$upStmt2->execute();
			}else{
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