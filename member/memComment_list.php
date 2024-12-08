<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$place_Idx = trim($placeIdx);					// 댓글을 등록할 지점 고유번호
	$page = trim($page);								// 페이지 수
	$reg_Date = DU_TIME_YMDHIS;
	$DB_con = db1();
	$chk_Query = "SELECT * FROM TB_MEMBERS_COMMENT WHERE place_Idx = :place_Idx; " ;
	$chk_stmt = $DB_con->prepare($chk_Query);
	$chk_stmt->bindparam(":place_Idx",$place_Idx);
	$chk_stmt->execute();
	$chk_num = $chk_stmt->rowCount();
	if($chk_num > 0){
		/* 전체 카운트 */
		$cntQuery = "";
		$cntQuery = "
			SELECT * 
			FROM TB_MEMBERS_COMMENT
			WHERE place_Idx = :place_Idx
			ORDER BY reg_Date DESC; ";
		$cntStmt = $DB_con->prepare($cntQuery);
		$cntStmt->bindparam(":place_Idx",$place_Idx);
		$cntStmt->execute();
		$totalCnt = $cntStmt->rowCount();
		if ($totalCnt == "") {
			$totalCnt = 0;
		} else {
			$totalCnt =  $totalCnt ;
		}
		$rows = 10;  //페이지 갯수
		$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
		if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
		$from_record = ($page - 1) * $rows; // 시작 열을 구함

		//지도 기본테이블 조회
		$query = "
			SELECT idx, place_Idx, comment, mem_Id, reg_Date
			FROM TB_MEMBERS_COMMENT
			WHERE place_Idx = :place_Idx
			ORDER BY reg_Date DESC
			LIMIT {$from_record}, {$rows};
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":place_Idx",$place_Idx);
		$stmt->execute();
		$Num = $stmt->rowCount();
		if($Num < 1)  { //아닐경우
			$chkResult = "0";
			$listInfoResult = array("totCnt" => "0", "page" => $page);
		} else {
			$chkResult = "1";
		    $listInfoResult = array("totCnt" => (string)$totalCnt, "page" => (string)$page);
			$data = [];
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];											// 댓글고유번호
				$comment = $row['comment'];						// 댓글내용
				$mem_Id = $row['mem_Id'];							// 댓글등록자아이디
				$member_Img = memImgInfo($mem_Id);			// 댓글등록자이미지
				if($member_Img == ''){
					$member_Img = "";
				}
				$reg_Date = $row['reg_Date'];							// 댓글등록일
				$result = ["idx" => $idx, "comment" => $comment, "mem_Id" => $mem_Id, "member_Img" => $member_Img, "reg_Date" => $reg_Date];
				 array_push($data, $result);
			}
			$chkData = [];
			$chkData["result"] = "success";
			$chkData["lists_info"] = $listInfoResult;
			$chkData['lists'] = $data;
		}  
		if ($chkResult  == "1") {
			$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
			echo  urldecode($output);  
		} else if ($chkResult  == "0"  ) {
			$result = array("result" => "success", "lists_info" => $listInfoResult);
			echo json_encode($result, JSON_UNESCAPED_UNICODE); 
		}
	}else{
		$totalCnt = 0;
		$page = 1;
		$result = array("result" => "success", "lists_info" => array("totCnt" => "0", "page" => (string)$page));
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}

	dbClose($DB_con);
	$stmt = null;
	$cntStmt = null;
	$chk_stmt = null;

?>