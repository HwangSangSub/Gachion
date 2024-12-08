<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 지도리스트
* 페이지 설명			: 지도리스트를 조회할 수 있다.
* 파일명					: view_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$category = trim($category);					// 검색어
$mem_Id = trim($memId);						// 로그인한 회원 아이디
if($mem_Id == ''){
	$mem_Id = 'GUEST';							// 비회원 인 경우 GUEST
}
$page = trim($page);								// 페이지 수
$mode = trim($mode);						// 로그인한 회원 아이디
if($mode == ''){
	$mode = '';							// 비회원 인 경우 GUEST
}
//$reg_Date = DU_TIME_YMDHIS;		//등록일


if ($category != "") {
	if($category == "all"){
		$category = "1=1";
	}else{
		$category = "category in (".$category.")";
	}
    
    $DB_con = db1();
	if($mode == '1'){	//즐겨찾기만 보는경우
		$data = [];
		//회원조회
		if($mem_Id != 'GUEST'){
			// 구독한 지도고유번호 조회하기
			$cnt_sub_Query = "
				SELECT a.idx, a.con_Idx
				FROM TB_MEMBERS_SUBSCRIBE a
					INNER JOIN TB_MEMBERS_FAVORITES b ON a.con_Idx = b.con_Idx AND b.use_bit = 'Y'
				WHERE a.mem_Id = :mem_Id AND a.use_Bit = 'Y'
				ORDER BY a.reg_Date DESC;";
			$cnt_sub_Stmt = $DB_con->prepare($cnt_sub_Query);
			$cnt_sub_Stmt->bindParam(":mem_Id", $mem_Id);
			$cnt_sub_Stmt->execute();
			$sub_Cnt = $cnt_sub_Stmt->rowCount();
			if($sub_Cnt < 1){	//구독지도가 없음.
				$result = array("result" => "success", "msg" => "구독한 지도 중 즐겨찾기 한 지도가 없습니다.");
				echo json_encode($result, JSON_UNESCAPED_UNICODE); 
			}else{
				if($sub_Cnt == 1){
					$sub_row=$cnt_sub_Stmt->fetch(PDO::FETCH_ASSOC);
					$sub_Idx = $sub_row['con_Idx'];
					$my_con_idx = "AND idx in ( ".$sub_Idx.")";
				}else{
					$sub_Idx = [];
					while($sub_row=$cnt_sub_Stmt->fetch(PDO::FETCH_ASSOC)){
						$sub_con_Idx = $sub_row['con_Idx'];
						 array_push($sub_Idx, $sub_con_Idx);
					}
					$myconidx = implode( ', ', $sub_Idx );
					$my_con_idx = "AND idx in ( ".$myconidx.")";
				}
				/* 전체 카운트 */
				$cntQuery = "
					SELECT idx 
					FROM TB_CONTENTS
					WHERE ".$category."
						".$my_con_idx."
					ORDER BY reg_Date DESC;";
				$cntStmt = $DB_con->prepare($cntQuery);
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
					SELECT idx, con_Name, con_Lv, category, img, tag, like_Cnt, kml_File, memo, reg_Id, reg_Date, end_Date
					FROM TB_CONTENTS
					WHERE ".$category."
						".$my_con_idx."
					ORDER BY reg_Date DESC
					LIMIT {$from_record}, {$rows};
					";
				$stmt = $DB_con->prepare($query);
				$stmt->execute();
				$Num = $stmt->rowCount();
				if($Num < 1)  { //아닐경우
					$chkResult = "0";
					$listInfoResult = array("totCnt" => "0", "page" => "0");
				} else {
					
					$chkResult = "1";
					$listInfoResult = array("totCnt" => (string)$totalCnt, "page" => (string)$page);
					while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
						$idx = $row['idx'];											// 지도고유번호
						// 지점수 확인
						$cnt_query = "
							SELECT count(idx) as cnt
							FROM TB_PLACE
							WHERE con_Idx = :con_Idx
							ORDER BY reg_Date DESC;
							";
						$cnt_stmt = $DB_con->prepare($cnt_query);
						$cnt_stmt->bindParam(":con_Idx", $idx);
						$cnt_stmt->execute();
						$cnt_row=$cnt_stmt->fetch(PDO::FETCH_ASSOC);
						$pin_Cnt = $cnt_row['cnt'];								// 지도내 지점 갯수
						if($mem_Id != 'GUEST'){
							$subs_query = "
								SELECT *
								FROM TB_MEMBERS_SUBSCRIBE
								WHERE mem_Id = :mem_Id
									AND con_Idx = :con_Idx
									AND use_Bit = 'Y'
								ORDER BY reg_Date DESC;
								";
							$subs_stmt = $DB_con->prepare($subs_query);
							$subs_stmt->bindParam(":mem_Id", $mem_Id);
							$subs_stmt->bindParam(":con_Idx", $idx);
							$subs_stmt->execute();
							$subs_num = $subs_stmt->rowCount();
							if($subs_num < 1){
								$subs_bit = 'N';
							}else{
								$subs_bit = 'Y';
							}
						}else{
							$subs_bit = 'N';
						}
						if($mem_Id != 'GUEST'){
							$favo_query = "
								SELECT *
								FROM TB_MEMBERS_FAVORITES
								WHERE mem_Id = :mem_Id
									AND con_Idx = :con_Idx
									AND use_Bit = 'Y'
								ORDER BY reg_Date DESC;
								";
							$favo_stmt = $DB_con->prepare($favo_query);
							$favo_stmt->bindParam(":mem_Id", $mem_Id);
							$favo_stmt->bindParam(":con_Idx", $idx);
							$favo_stmt->execute();
							$favo_num = $favo_stmt->rowCount();
							if($favo_num < 1){
								$favo_bit = 'N';
							}else{
								$favo_bit = 'Y';
							}
						}else{
							$favo_bit = 'N';
						}
						$con_Name = $row['con_Name'];						// 지도명
						$con_Lv = $row['con_Lv'];								// 지도등급 (0: 본사, 1: 일반회원, 2: 제휴회원)
						$category = $row['category'];							// 카테고리
						// 카테고리명
						$category_Cquery = "
							SELECT code_Name
							FROM TB_CONFIG_CODE
							WHERE code = :code
								AND code_Div = 'category'
							ORDER BY reg_Date DESC;
							";
						$category_Cstmt = $DB_con->prepare($category_Cquery);
						$category_Cstmt->bindParam(":code", $category);
						$category_Cstmt->execute();
						while($category_Crow=$category_Cstmt->fetch(PDO::FETCH_ASSOC)) {
							$code_Name = $category_Crow['code_Name'];
							$category_query = "
								SELECT code_on_Img as code_Img
								FROM TB_CONFIG_CODE
								WHERE code_Name = :code_Name
									AND code_Div = 'categorylist'
								ORDER BY reg_Date DESC;
								";
							$category_stmt = $DB_con->prepare($category_query);
							$category_stmt->bindParam(":code_Name", $code_Name);
							$category_stmt->execute();
							while($category_row=$category_stmt->fetch(PDO::FETCH_ASSOC)){
								$code_Img = $category_row['code_Img'];
								if($code_Img == ''){
									$code_Img = '';
								}else{
									$code_ImgFile = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_Img;
									$code_Img = $code_ImgFile;
								}
							}
						}
						$img_num = 0;
						// 이미지확인
						$img_query = "
							SELECT img as pimg
							FROM TB_PLACE
							WHERE con_Idx = :con_Idx
							ORDER BY reg_Date DESC
							LIMIT 4;
							";
						$img_stmt = $DB_con->prepare($img_query);
						$img_stmt->bindParam(":con_Idx", $idx);
						$img_stmt->execute();
						$img_num = $img_stmt->rowCount();
						$p_Img = [];
						if($img_num > 3){
							while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)) {
								$pimg = $img_row['pimg'];							// 지점에 등록된 이미지
								if($pimg == ''){
									continue;
								}
								$p_locat_Img = "https://places.gachita.kr/contents/place_img/".$pimg;
								 array_push($p_Img, $p_locat_Img);
							}
							$img = $p_Img;
							$img_Cnt = $img_num;										// 이미지타입(1: 썸네일이미지, 2: 지도이미지, 3: 사진4개이상)
						}else{
							$img_Name = $row['img'];								// 이미지
							$img_Cnt = $img_num;	
							if($img_Name == ""){											// 이미지가 없을 경우 
								$img = "";					
							}else{
								$locat_Img = "https://places.gachita.kr/contents/img/".$img_Name;
								array_push($p_Img, $locat_Img);
								$img = $p_Img;
							}
						}
						$tag = $row['tag'];										// 지점 태그
						if($tag == ''){
							$tag = '';
						}else{
							$tag = "#".str_replace(",",",#",$tag);
						}
						$like_Cnt = $row['like_Cnt'];							// 좋아요 수
						$kml_File = $row['kml_File'];							// kml 영역파일 유무
						if($kml_File == ''){
							$kml_File = '';
						}
						$memo = $row['memo'];								// 한줄메모
						if($memo == ''){
							$memo = '';
						}
						$reg_Id = $row['reg_Id'];									// 등록자
						$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
						$member_Img = memImgInfo($reg_Id);				// 회원이미지
						if($member_Img == ''){
							$member_Img = "";
						}
						if($reg_Nname == ''){
							$reg_Nname = "";
						}
						$reg_Date = $row['reg_Date'];							// 등록일
						$regDate = substr($reg_Date, 0, 10);
						$end_Date = $row['end_Date'];							// 등록기간종료일
						if($con_Lv == "1"){
							$endDate = ""; 
						}else{
							$endDate = substr($end_Date, 0, 10); 
						}

						$mresult = ["con_Idx" => $idx, "con_Name" => $con_Name, "con_Lv" => $con_Lv, "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img,  "img_Cnt" => (string)$img_Cnt, "img" => $img, "kml_File" => $kml_File, "memo" => $memo, "pin_Cnt" => (string)$pin_Cnt, "favo_bit" => $favo_bit, "member_Img" => $member_Img, "reg_Id" => $reg_Id, "nick_Name" =>$reg_Nname, "reg_Date" => $regDate];
						 array_push($data, $mresult);

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
					/*$chkData2["result"] = "error";
					$chkData2["lists_info"] = $listInfoResult;  //페이지 관련
					$chkData2["errorMsg"] = $listInfoResult;  //페이지 관련
					$output = str_replace('\\\/', '/', json_encode($chkData2, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);  */
					$result = array("result" => "success", "lists_info" => $listInfoResult);
					echo json_encode($result, JSON_UNESCAPED_UNICODE); 
				}
			}
		}else{
			$result = array("result" => "success", "msg" => "비회원은 회원가입 후 이용해주세요.");
			echo json_encode($result, JSON_UNESCAPED_UNICODE); 
		}
	}else{
		$data = [];
		if($mem_Id != 'GUEST'){
			// 구독한 지도고유번호 조회하기
			$cnt_sub_Query = "
				SELECT idx, con_Idx
				FROM TB_MEMBERS_SUBSCRIBE
				WHERE mem_Id = :mem_Id AND use_Bit = 'Y'
				ORDER BY reg_Date DESC;";
			$cnt_sub_Stmt = $DB_con->prepare($cnt_sub_Query);
			$cnt_sub_Stmt->bindParam(":mem_Id", $mem_Id);
			$cnt_sub_Stmt->execute();
			$sub_Cnt = $cnt_sub_Stmt->rowCount();
			if($sub_Cnt < 1){	//구독지도가 없음.
				$result = array("result" => "success", "msg" => "구독한 지도가 없습니다.");
				echo json_encode($result, JSON_UNESCAPED_UNICODE); 
			}else{
				if($sub_Cnt == 1){
					$sub_row=$cnt_sub_Stmt->fetch(PDO::FETCH_ASSOC);
					$sub_Idx = $sub_row['con_Idx'];
					$my_con_idx = "AND idx in ( ".$sub_Idx.")";
				}else{
					$sub_Idx = [];
					while($sub_row=$cnt_sub_Stmt->fetch(PDO::FETCH_ASSOC)){
						$sub_con_Idx = $sub_row['con_Idx'];
						 array_push($sub_Idx, $sub_con_Idx);
					}
					$myconidx = implode( ', ', $sub_Idx );
					$my_con_idx = "AND idx in ( ".$myconidx.")";
				}
				/* 전체 카운트 */
				$cntQuery = "
					SELECT idx 
					FROM TB_CONTENTS
					WHERE ".$category."
						".$my_con_idx."
					ORDER BY reg_Date DESC;";
				$cntStmt = $DB_con->prepare($cntQuery);
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
					SELECT idx, con_Name, con_Lv, category, img, tag, like_Cnt, kml_File, memo, reg_Id, reg_Date, end_Date
					FROM TB_CONTENTS
					WHERE ".$category."
						".$my_con_idx."
					ORDER BY reg_Date DESC
					LIMIT {$from_record}, {$rows};
					";
				$stmt = $DB_con->prepare($query);
				$stmt->execute();
				$Num = $stmt->rowCount();
				if($Num < 1)  { //아닐경우
					$chkResult = "0";
					$listInfoResult = array("totCnt" => "0", "page" => "0");
				} else {
					
					$chkResult = "1";
					$listInfoResult = array("totCnt" => (string)$totalCnt, "page" => (string)$page);
					while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
						$idx = $row['idx'];											// 지도고유번호
						// 지점수 확인
						$cnt_query = "
							SELECT count(idx) as cnt
							FROM TB_PLACE
							WHERE con_Idx = :con_Idx
							ORDER BY reg_Date DESC;
							";
						$cnt_stmt = $DB_con->prepare($cnt_query);
						$cnt_stmt->bindParam(":con_Idx", $idx);
						$cnt_stmt->execute();
						$cnt_row=$cnt_stmt->fetch(PDO::FETCH_ASSOC);
						$pin_Cnt = $cnt_row['cnt'];								// 지도내 지점 갯수
						if($mem_Id != 'GUEST'){
							$subs_query = "
								SELECT *
								FROM TB_MEMBERS_SUBSCRIBE
								WHERE mem_Id = :mem_Id
									AND con_Idx = :con_Idx
									AND use_Bit = 'Y'
								ORDER BY reg_Date DESC;
								";
							$subs_stmt = $DB_con->prepare($subs_query);
							$subs_stmt->bindParam(":mem_Id", $mem_Id);
							$subs_stmt->bindParam(":con_Idx", $idx);
							$subs_stmt->execute();
							$subs_num = $subs_stmt->rowCount();
							if($subs_num < 1){
								$subs_bit = 'N';
							}else{
								$subs_bit = 'Y';
							}
						}else{
							$subs_bit = 'N';
						}
						if($mem_Id != 'GUEST'){
							$favo_query = "
								SELECT *
								FROM TB_MEMBERS_FAVORITES
								WHERE mem_Id = :mem_Id
									AND con_Idx = :con_Idx
									AND use_Bit = 'Y'
								ORDER BY reg_Date DESC;
								";
							$favo_stmt = $DB_con->prepare($favo_query);
							$favo_stmt->bindParam(":mem_Id", $mem_Id);
							$favo_stmt->bindParam(":con_Idx", $idx);
							$favo_stmt->execute();
							$favo_num = $favo_stmt->rowCount();
							if($favo_num < 1){
								$favo_bit = 'N';
							}else{
								$favo_bit = 'Y';
							}
						}else{
							$favo_bit = 'N';
						}
						$con_Name = $row['con_Name'];						// 지도명
						$con_Lv = $row['con_Lv'];								// 지도등급 (0: 본사, 1: 일반회원, 2: 제휴회원)
						$category = $row['category'];							// 카테고리
						// 카테고리명
						$category_Cquery = "
							SELECT code_Name
							FROM TB_CONFIG_CODE
							WHERE code = :code
								AND code_Div = 'category'
							ORDER BY reg_Date DESC;
							";
						$category_Cstmt = $DB_con->prepare($category_Cquery);
						$category_Cstmt->bindParam(":code", $category);
						$category_Cstmt->execute();
						while($category_Crow=$category_Cstmt->fetch(PDO::FETCH_ASSOC)) {
							$code_Name = $category_Crow['code_Name'];
							$category_query = "
								SELECT code_on_Img as code_Img
								FROM TB_CONFIG_CODE
								WHERE code_Name = :code_Name
									AND code_Div = 'categorylist'
								ORDER BY reg_Date DESC;
								";
							$category_stmt = $DB_con->prepare($category_query);
							$category_stmt->bindParam(":code_Name", $code_Name);
							$category_stmt->execute();
							while($category_row=$category_stmt->fetch(PDO::FETCH_ASSOC)){
								$code_Img = $category_row['code_Img'];
								if($code_Img == ''){
									$code_Img = '';
								}else{
									$code_ImgFile = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_Img;
									$code_Img = $code_ImgFile;
								}
							}
						}
						$img_num = 0;
						// 이미지확인
						$img_query = "
							SELECT img as pimg
							FROM TB_PLACE
							WHERE con_Idx = :con_Idx
							ORDER BY reg_Date DESC
							LIMIT 4;
							";
						$img_stmt = $DB_con->prepare($img_query);
						$img_stmt->bindParam(":con_Idx", $idx);
						$img_stmt->execute();
						$img_num = $img_stmt->rowCount();
						$p_Img = [];
						if($img_num > 3){
							while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)) {
								$pimg = $img_row['pimg'];							// 지점에 등록된 이미지
								if($pimg == ''){
									continue;
								}
								$p_locat_Img = "https://places.gachita.kr/contents/place_img/".$pimg;
								 array_push($p_Img, $p_locat_Img);
							}
							$img = $p_Img;
							$img_Cnt = $img_num;										// 이미지타입(1: 썸네일이미지, 2: 지도이미지, 3: 사진4개이상)
						}else{
							$img_Name = $row['img'];								// 이미지
							$img_Cnt = $img_num;	
							if($img_Name == ""){											// 이미지가 없을 경우 
								$img = "";					
							}else{
								$locat_Img = "https://places.gachita.kr/contents/img/".$img_Name;
								array_push($p_Img, $locat_Img);
								$img = $p_Img;
							}
						}
						$tag = $row['tag'];										// 지점 태그
						if($tag == ''){
							$tag = '';
						}else{
							$tag = "#".str_replace(",",",#",$tag);
						}
						$like_Cnt = $row['like_Cnt'];							// 좋아요 수
						$kml_File = $row['kml_File'];							// kml 영역파일 유무
						if($kml_File == ''){
							$kml_File = '';
						}
						$memo = $row['memo'];								// 한줄메모
						if($memo == ''){
							$memo = '';
						}
						$reg_Id = $row['reg_Id'];									// 등록자
						$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
						$member_Img = memImgInfo($reg_Id);				// 회원이미지
						if($member_Img == ''){
							$member_Img = "";
						}
						if($reg_Nname == ''){
							$reg_Nname = "";
						}
						$reg_Date = $row['reg_Date'];							// 등록일
						$regDate = substr($reg_Date, 0, 10);
						$end_Date = $row['end_Date'];							// 등록기간종료일
						if($con_Lv == "1"){
							$endDate = ""; 
						}else{
							$endDate = substr($end_Date, 0, 10); 
						}

						$mresult = ["con_Idx" => $idx, "con_Name" => $con_Name, "con_Lv" => $con_Lv, "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img,  "img_Cnt" => (string)$img_Cnt, "img" => $img, "kml_File" => $kml_File, "memo" => $memo, "pin_Cnt" => (string)$pin_Cnt, "favo_bit" => $favo_bit, "member_Img" => $member_Img, "reg_Id" => $reg_Id, "nick_Name" =>$reg_Nname, "reg_Date" => $regDate];
						 array_push($data, $mresult);

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
					/*$chkData2["result"] = "error";
					$chkData2["lists_info"] = $listInfoResult;  //페이지 관련
					$chkData2["errorMsg"] = $listInfoResult;  //페이지 관련
					$output = str_replace('\\\/', '/', json_encode($chkData2, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);  */
					$result = array("result" => "success", "lists_info" => $listInfoResult);
					echo json_encode($result, JSON_UNESCAPED_UNICODE); 
				}
			}
		}else{
			$result = array("result" => "success", "msg" => "비회원은 회원가입 후 이용해주세요.");
			echo json_encode($result, JSON_UNESCAPED_UNICODE); 
		}
	}
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "카테고리값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



