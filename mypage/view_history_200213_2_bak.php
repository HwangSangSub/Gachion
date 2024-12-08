<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 회원들의 히스토리내역 보여줌
* 페이지 설명			: 회원들의 히스토리내역 보여줌
* 파일명					: view_history.php
* 관련DB					: TB_HISTORY
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$login_Id = trim($memId);					//회원아이디
$mode = trim($mode);						//조회타입(0: 전체, 1: 내 활동, 2: 관심 유저 내역만)
if($mode == ""){
	$mode = "0";
}
if ($login_Id != "") {
    
    $DB_con = db1();
	// 관심유저 수 조회하기
	$like_cnt_query = "
		SELECT COUNT(*) as cnt
		FROM TB_MEMBERS_INTEREST as a
		WHERE a.reg_Id = :reg_Id					
			AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
			AND a.use_Bit = 'Y';
		";
	
	$like_cnt_stmt = $DB_con->prepare($like_cnt_query);
	$like_cnt_stmt->bindParam(":reg_Id", $login_Id);
	$like_cnt_stmt->execute();
	$like_cnt_row=$like_cnt_stmt->fetch(PDO::FETCH_ASSOC);
	$like_cnt = $like_cnt_row['cnt'];
	if($like_cnt == ""){
		$like_cnt = "0";
	}
	if($mode == "1"){							//내 활동 만
		$query = "
			SELECT idx, con_Idx, place_Idx, mem_Id, history, reg_Id, reg_Date
			FROM TB_HISTORY 
			WHERE reg_Id = :reg_Id
			ORDER BY reg_Date DESC;
			";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":reg_Id", $login_Id);
		$stmt->execute();
	}else if($mode == "2"){					// 관심 유저 만
		// 관심유저 아이디 조회하기
		$like_query = "
			SELECT a.mem_Id
			FROM TB_MEMBERS_INTEREST as a
			WHERE a.reg_Id = :reg_Id
				AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
				AND a.use_Bit = 'Y';
			";
		
		$like_stmt = $DB_con->prepare($like_query);
		$like_stmt->bindParam(":reg_Id", $login_Id);
		$like_stmt->execute();
		$like_Id = "";
		$int = 1;
		while($like_row=$like_stmt->fetch(PDO::FETCH_ASSOC)){
			if($int == 1){
				$like_Id = "'".$like_row['mem_Id']."'";
			}else{
				$like_Id = $like_Id.", '".$like_row['mem_Id']."'";
			}
			$int++;
		}
		$query = "
			SELECT idx, con_Idx, place_Idx, mem_Id, history, reg_Id, reg_Date
			FROM TB_HISTORY 
			WHERE reg_Id IN (".$like_Id.")
				AND history NOT IN ('신고')
			ORDER BY reg_Date DESC;
			";
		
		$stmt = $DB_con->prepare($query);
		$stmt->execute();
	}else{
		$query = "
			SELECT idx, con_Idx, place_Idx, mem_Id, history, reg_Id, reg_Date
			FROM TB_HISTORY 
			WHERE reg_Id = :reg_Id OR mem_Id = :mem_Id
			ORDER BY reg_Date DESC;
			";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":reg_Id", $login_Id);
		$stmt->bindParam(":mem_Id", $login_Id);
		$stmt->execute();
	}
	$data = [];
    $chknum = $stmt->rowCount();
    if($chknum < 1)  { // 등록된 히스토리가 없는 경우
		$result = array("result" => "success", "like_cnt" => (string)$like_cnt, "msg" => "등록된 히스토리가 없습니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
    } else {  //등록된 히스토리가 있을 경우
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$idx = $row['idx'];																			// 히스토리 고유번호
			$con_Idx = $row['con_Idx'];																// 히스토리 지도고유번호
			if($con_Idx == ''){
				$con_Idx = '';
				$con_Name = '';
			}else{
				$con_Name = contentsNameInfo($con_Idx);
				if($con_Name == ''){
					$con_Name = "";
				}
			}
			$place_Idx = $row['place_Idx'];															// 히스토리 지점고유번호
			if($place_Idx == ''){
				$place_Idx = '';
				$place_Name = '';
			}else{
				$place_Name = placeNameInfo($place_Idx);
			}
			$reg_Id = $row['reg_Id'];																	// 히스토리 등록자
			$mem_Id = $row['mem_Id'];															// 히스토리 아이디
			$history = $row['history'];																// 히스토리내용
			$history_con = historyInfo($idx, $login_Id, $reg_Id, $history);								// 히스토리정리
			if($history_con == ''){
				$history_con ="";
			}
			$member_Img = memImgInfo($mem_Id);											// 회원이미지
			if($member_Img == ''){
				$member_Img = "";
			}
			$reg_Date = $row['reg_Date'];															// 히스토리등록일

			$result = ["mem_Id" => $mem_Id, "member_Img" => $member_Img, "con_Idx" => $con_Idx, "con_Name" => $con_Name, "place_Idx" => $place_Idx, "place_Name" => $place_Name, "history" => $history_con, "reg_Id" => $reg_Id, "reg_Date" => $reg_Date];
			array_push($data, $result);
		}
		$chkData = [];
		$chkData["result"] = "success";
		$chkData["like_cnt"] = (string)$like_cnt;
		$chkData["history"] = $data;
		$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
		echo  urldecode($output);
    }
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



