<?
/*
* 프로그램				: 지도리스트
* 페이지 설명			: 지도리스트를 조회할 수 있다.
* 파일명					: list_my_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$mem_Id = trim($memId);						// 로그인한 회원 아이디
if(memChk($mem_Id) == "0"){
	$mem_Id = "GUEST";							//회원아이디가 없는 경우 비회원으로 처리
}else{
	$mIdx = memIdxInfo($mem_Id);	// 회원고유번호
}
//$reg_Date = DU_TIME_YMDHIS;		//등록일
$DB_con = db1();
if($mem_Id != ""){
	$memCQuery = "
		SELECT idx
		FROM TB_MEMBERS
		WHERE mem_Id = :mem_Id
			AND idx = :idx
			AND b_Disply = 'N'
		LIMIT 1;
	";
	$memCStmt = $DB_con->prepare($memCQuery);
	$memCStmt->bindParam(":mem_Id", $mem_Id);
	$memCStmt->bindParam(":idx", $mIdx);
	$memCStmt->execute();
	$memCNum = $memCStmt->rowCount();
	//회원 탈퇴 기본 저장 중복 등록을 맞기 위해서 체크 함
	if ($memCNum > 0) {
		$contentMQuery = "
			SELECT *
			FROM TB_CONTENTS
			WHERE reg_Id = :reg_Id AND member_Idx = :member_Idx AND delete_Bit = '0'
			ORDER BY mod_Date DESC LIMIT 1
			;
		";
		$contentMStmt = $DB_con->prepare($contentMQuery);
		$contentMStmt->bindParam(":reg_Id", $mem_Id);
		$contentMStmt->bindParam(":member_Idx", $mIdx);
		$contentMStmt->execute();		
		while($contentMRow=$contentMStmt->fetch(PDO::FETCH_ASSOC)){
			$McIdx = $contentMRow['idx'];
			$McName = $contentMRow['con_Name'];
			$Mcategory = $contentMRow['category'];
			// 카테고리명
			$category_Cquery = "
				SELECT code_Name
				FROM TB_CONFIG_CODE
				WHERE code = :code
					AND code_Div = 'category'
				ORDER BY reg_Date DESC;
				";
			$category_Cstmt = $DB_con->prepare($category_Cquery);
			$category_Cstmt->bindParam(":code", $Mcategory);
			$category_Cstmt->execute();
			while($category_Crow=$category_Cstmt->fetch(PDO::FETCH_ASSOC)) {
				$code_MName = $category_Crow['code_Name'];
				$category_query = "
					SELECT code_on_Img as code_Img
					FROM TB_CONFIG_CODE
					WHERE code_Name = :code_Name
						AND code_Div = 'categorylist'
					ORDER BY reg_Date DESC;
					";
				$category_stmt = $DB_con->prepare($category_query);
				$category_stmt->bindParam(":code_Name", $code_MName);
				$category_stmt->execute();
				while($category_row=$category_stmt->fetch(PDO::FETCH_ASSOC)){
					$code_MImg = $category_row['code_Img'];
					if($code_MImg == ''){
						$code_MImg = '';
					}else{
						$code_MImgFile = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_MImg;
						$code_MImg = $code_MImgFile;
					}
				}
			}
			//해당 지도의 지점 수
			$cnt_Mquery = "
				SELECT count(idx) as cnt
				FROM TB_PLACE
				WHERE con_Idx = :con_Idx
					AND delete_Bit = '0'
					OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
				ORDER BY reg_Date DESC;
				";
			$cnt_Mstmt = $DB_con->prepare($cnt_Mquery);
			$cnt_Mstmt->bindParam(":con_Idx", $McIdx);
			$cnt_Mstmt->execute();
			$cnt_Mrow=$cnt_Mstmt->fetch(PDO::FETCH_ASSOC);
			$Mpin_Cnt = $cnt_Mrow['cnt'];								// 지도내 지점 갯수
			if($Mpin_Cnt == ''){
				$Mpin_Cnt = "0";
			}

			$bdata = array("idx" => $McIdx, "con_Name" => $McName, "category" => $Mcategory, "code_Name" => $code_MName, "code_Img" => $code_MImg, "pin_Cnt" => (string)$Mpin_Cnt);
		}
		$contentQuery = "
			SELECT *
			FROM TB_CONTENTS
			WHERE reg_Id = :reg_Id
				AND idx <> :idx
				AND member_Idx = :member_Idx
				AND delete_Bit = '0'
			ORDER BY mod_Date DESC
			;
		";
		$contentStmt = $DB_con->prepare($contentQuery);
		$contentStmt->bindParam(":reg_Id", $mem_Id);
		$contentStmt->bindParam(":idx", $McIdx);
		$contentStmt->bindParam(":member_Idx", $mIdx);
		$contentStmt->execute();
		$data = [];
		while($contentRow=$contentStmt->fetch(PDO::FETCH_ASSOC)){
			$cIdx = $contentRow['idx'];
			$cName = $contentRow['con_Name'];
			$category = $contentRow['category'];
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
			//해당 지도의 지점 수
			$cnt_query = "
				SELECT count(idx) as cnt
				FROM TB_PLACE
				WHERE con_Idx = :con_Idx
					AND delete_Bit = '0'
					OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
				ORDER BY reg_Date DESC;
				";
			$cnt_stmt = $DB_con->prepare($cnt_query);
			$cnt_stmt->bindParam(":con_Idx", $cIdx);
			$cnt_stmt->execute();
			$cnt_row=$cnt_stmt->fetch(PDO::FETCH_ASSOC);
			$pin_Cnt = $cnt_row['cnt'];								// 지도내 지점 갯수
			if($pin_Cnt == ''){
				$pin_Cnt = "0";
			}

			$result = ["idx" => $cIdx, "con_Name" => $cName, "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img, "pin_Cnt" => (string)$pin_Cnt];
			 array_push($data, $result);
		}


		$chkData = [];
		$chkData["result"] = "success";
		$chkData["basic_content"] = $bdata;
		$chkData["lists"] = $data;
		$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
		echo  urldecode($output);  
	}else{
		$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
		echo str_replace('\\/', '/', json_encode($result, JSON_UNESCAPED_UNICODE));
	}
}else{
	$result = array("result" => "error", "errorMsg" => "아이디빈값오류");
	echo str_replace('\\/', '/', json_encode($result, JSON_UNESCAPED_UNICODE));
}

dbClose($DB_con);
?>



