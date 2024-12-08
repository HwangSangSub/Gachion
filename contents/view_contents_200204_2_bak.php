<?
/*
* 프로그램				: 지도리스트
* 페이지 설명			: 지도리스트를 조회할 수 있다.
* 파일명					: view_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$category = trim($category);					// 카테고리
if($category == ""){
	$category = "all";
}
$search = trim($search);							// 검색어
$mem_Id = trim($memId);						// 로그인한 회원 아이디
if(memChk($mem_Id) == "0"){
	$mem_Id = "GUEST";							//회원아이디가 없는 경우 비회원으로 처리
}else{
	$mIdx = memIdxInfo($mem_Id);			// 회원고유번호
}
$page = trim($page);								// 페이지 수
//$reg_Date = DU_TIME_YMDHIS;		//등록일


if ($category != "") {
	if($category == "all"){
		$category = "1=1";
	}else{
		$category = "a.category in (".$category.")";
	}
    
    $DB_con = db1();
	$data = [];
	if($search != ""){	//검색어가 있을 경우 검색값 추가  
		/* 전체 카운트 */
		$cntQuery = "";
		$cntQuery = "
			SELECT a.idx 
			FROM TB_CONTENTS a
				INNER JOIN TB_PLACE b ON a.idx = b.con_Idx
			WHERE ".$category."
				AND (a.con_Name like '%".$search."%'OR a.memo like '%".$search."%' OR a.tag like '%".$search."%' OR b.place_Name like '%".$search."%')
				AND a.open_Bit = '0'
            GROUP BY a.idx
			ORDER BY a.mod_Date DESC;";
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
			SELECT a.idx, a.con_Name, a.con_Lv, a.category, a.img, a.tag, a.thumbnail_Bit, a.like_Cnt, a.kml_File, a.memo, a.reg_Id, a.reg_Date, a.end_Date
			FROM TB_CONTENTS a
				INNER JOIN TB_PLACE b ON a.idx = b.con_Idx
			WHERE ".$category."
				AND (a.con_Name like '%".$search."%'OR a.memo like '%".$search."%' OR a.tag like '%".$search."%' OR b.place_Name like '%".$search."%')
				AND a.open_Bit = '0'
            GROUP BY a.idx
			ORDER BY a.mod_Date DESC
			LIMIT {$from_record}, {$rows};
			";
		$stmt = $DB_con->prepare($query);
		$stmt->execute();
		$Num = $stmt->rowCount();
	}else{
		/* 전체 카운트 */
		$cntQuery = "";
		$cntQuery = "
			SELECT a.idx 
			FROM TB_CONTENTS a
				INNER JOIN TB_PLACE b ON a.idx = b.con_Idx
			WHERE ".$category."
				AND a.open_Bit = '0'
            GROUP BY a.idx
			ORDER BY a.mod_Date DESC;";
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
			SELECT a.idx, a.con_Name, a.con_Lv, a.category, a.img, a.tag, a.thumbnail_Bit, a.like_Cnt, a.kml_File, a.memo, a.reg_Id, a.reg_Date, a.end_Date
			FROM TB_CONTENTS a
				INNER JOIN TB_PLACE b ON a.idx = b.con_Idx
			WHERE ".$category."
				AND a.open_Bit = '0'
            GROUP BY a.idx
			ORDER BY a.mod_Date DESC
			LIMIT {$from_record}, {$rows};
			";
		$stmt = $DB_con->prepare($query);
				$stmt->execute();
		$Num = $stmt->rowCount();
	}
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
			if($pin_Cnt == ''){
				$pin_Cnt = "0";
			}
			if($mem_Id != 'GUEST'){
				$subs_query = "
					SELECT *
					FROM TB_MEMBERS_SUBSCRIBE
					WHERE mem_Id = :mem_Id
						AND con_Idx = :con_Idx
						AND member_Idx = :member_Idx
						AND use_Bit = 'Y'
					ORDER BY reg_Date DESC;
					";
				$subs_stmt = $DB_con->prepare($subs_query);
				$subs_stmt->bindParam(":mem_Id", $mem_Id);
				$subs_stmt->bindParam(":member_Idx", $mIdx);
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
			$thumbnail_Bit = $row['thumbnail_Bit'];							//썸네일 사용여부
			if($thumbnail_Bit == "1"){
				$img_cquery = "
					SELECT  sum(case when img = '' then 0 else 1 end) as cnt
					FROM TB_PLACE
					WHERE con_Idx = :con_Idx
					ORDER BY reg_Date DESC;
					";
				$img_cstmt = $DB_con->prepare($img_cquery);
				$img_cstmt->bindParam(":con_Idx", $idx);
				$img_cstmt->execute();
				$img_crow=$img_cstmt->fetch(PDO::FETCH_ASSOC);
				$img_Cnt = $img_crow['cnt'];
				if($img_Cnt == ""){
					$img_Cnt = "0";
				}
				$p_Img = [];
				$img_Name = $row['img'];										// 이미지
				if($img_Name == ""){											// 이미지가 없을 경우 
					$locat_Img = "";
					array_push($p_Img, $locat_Img);
					$img = $p_Img;
				}else{
					$locat_Img = "https://places.gachita.kr/contents/img/".$img_Name;
					array_push($p_Img, $locat_Img);
					$img = $p_Img;
				}
			}else{
				$img_cquery = "
					SELECT  sum(case when img = '' then 0 else 1 end) as cnt
					FROM TB_PLACE
					WHERE con_Idx = :con_Idx
					ORDER BY reg_Date DESC;
					";
				$img_cstmt = $DB_con->prepare($img_cquery);
				$img_cstmt->bindParam(":con_Idx", $idx);
				$img_cstmt->execute();
				$img_crow=$img_cstmt->fetch(PDO::FETCH_ASSOC);
				$img_Cnt = $img_crow['cnt'];
				if($img_Cnt == ""){
					$img_Cnt = "0";
				}
				// 이미지확인
				$img_query = "
					SELECT img as pimg
					FROM TB_PLACE
					WHERE con_Idx = :con_Idx
					ORDER BY reg_Date DESC;
					";
				$img_stmt = $DB_con->prepare($img_query);
				$img_stmt->bindParam(":con_Idx", $idx);
				$img_stmt->execute();
				$img_row=$img_stmt->fetch(PDO::FETCH_ASSOC);
				$pimg = $img_row['pimg'];
				$p_Img = [];
				$img_File = [];
				if($img_Cnt < 1){
					$img_Cnt = "0";
					$img = array('');
				}else{
					$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$pimg;
					if(is_dir($m_file)){
						// 핸들 획득
						$handle  = opendir($m_file);
						$filename = readdir($handle);
						$files = array();
						// 디렉터리에 포함된 파일을 저장한다.
						while (false !== ($filename = readdir($handle))) {
							if($filename == "." || $filename == ".."){
								continue;
							}
							// 파일인 경우만 목록에 추가한다.
							$f_dir = $m_file . "/" . $filename;
							if(is_file($f_dir)){
								$files[] = $filename;
							}
						}
						// 핸들 해제 
						closedir($handle);
						// 정렬, 역순으로 정렬하려면 rsort 사용
						rsort($files);
						// 파일명을 출력한다.
						$img_idx = 0;
						foreach ($files as $f) {
							$img_FileName = "https://places.gachita.kr/contents/place_img/".$pimg."/".$f;
							array_push($img_File, $img_FileName);
							$img_idx++;
							if($img_idx > 3){
								break;
							}
						} 
						$img_Cnt = count($files);
						$img = $img_File;
					}else{
						$img_Cnt = "0";
						$img = array('');
					}
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

			$mresult = ["con_Idx" => $idx, "con_Name" => $con_Name, "con_Lv" => $con_Lv, "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img,  "img_Cnt" => (string)$img_Cnt, "img" => $img, "tag" => $tag, "kml_File" => $kml_File, "memo" => $memo, "pin_Cnt" => (string)$pin_Cnt, "subs_bit" => $subs_bit, "member_Img" => $member_Img, "reg_Id" => $reg_Id, "nick_Name" =>$reg_Nname, "reg_Date" => $regDate, "end_Date" => $endDate];
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
		$result = array("result" => "success", "lists_info" => $listInfoResult, "errorMsg" => "자료가 없습니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "카테고리값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



