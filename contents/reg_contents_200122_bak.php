<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 콘텐츠를 등록하는 기능
* 페이지 설명			: 콘텐츠를 등록하는 기능
* 파일명					: reg_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수.
			
$DB_con = db1();

if(isset($_FILES['kmlfile'])){ 

	//고유번호 확인
	$chk_query = "
		SELECT idx
		FROM TB_CONTENTS
		ORDER BY idx DESC;
	";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->execute();
	$chk_row=$chk_stmt->fetch(PDO::FETCH_ASSOC);
	$chk_idx = $chk_row['idx'];
	$idx = ((int)$chk_idx +1).".kml";

	$f_name = $_FILES['kmlfile']['name'];
	$fname = iconv("UTF-8", "EUC-KR", $f_name);
	$target = "./kmlfile/".$idx ; 
	if(move_uploaded_file($_FILES['kmlfile']['tmp_name'],$target)) {

		/* 이미지 파일 업로드 시작 */
		if(isset($_FILES['img'])){
			$img_f_name = $_FILES['img']['name'];
			$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
			$img_target = "./img/".$img_fname ; 
			move_uploaded_file($_FILES['img']['tmp_name'],$img_target);
			$img = trim($_FILES['img']['name']);				//썸네일이미지
		}else{
			$img = "";				//썸네일이미지
		}
		/* 이미지 파일 업로드 종료 */	
		$con_Name = trim($con_Name);					//콘텐츠이름 
		$category = trim($category);						//카테고리 (공통코드로 정리하여 숫자 예 : 1: 클럽, 2: 먹방, 3: 데이트, 등등)
		$tag = trim($tag);										//태그 (예 : 이사아폴리스,돼지고기,양많음,적극추천 등 태그를 , 로 구분하여 넣기.
		$open_Bit = trim($open_Bit);						//공개여부(0:전체공개, 1:구독자만공개, 2:비공개)
		if($open_Bit == ""){
			$open_Bit = "0";
		}else{
			$open_Bit = $open_Bit;
		}
		$kml_File = trim($idx);	//KML이라는 좌표파일이 있을경우 서버에 업로드하여 파일명 안내
		$reg_Id = trim($reg_Id);								//등록자
		$mIdx = memIdxInfo($reg_Id);	// 회원고유번호
		$reg_Date = DU_TIME_YMDHIS;					//등록일

		$chkQuery = "";
		$chkQuery = "SELECT mem_Lv FROM TB_MEMBERS WHERE mem_Id = :mem_Id ";
		$chkStmt = $DB_con->prepare($chkQuery);
		$chkStmt->bindparam(":mem_Id",$reg_Id);
		$chkStmt->execute();
		$chkrow = $chkStmt->fetch(PDO::FETCH_ASSOC);
		$mem_Lv = $chkrow['mem_Lv'];	//회원등급

		if ($reg_Id != "" && $con_Name != "" && $category != ""  ) {
			
			//콘텐츠등록
			$query = "INSERT INTO TB_CONTENTS (member_Idx, con_Name, con_Lv, category, img, tag, open_Bit, kml_File, reg_Id, reg_date, mod_Date ) VALUES (:member_Idx, :con_Name, :con_Lv, :category, :img, :tag, :open_Bit, :kml_File, :reg_Id, :reg_Date, NOW())";
			
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":member_Idx", $mIdx);
			$stmt->bindParam(":con_Name", $con_Name);
			$stmt->bindParam(":con_Lv", $mem_Lv);
			$stmt->bindParam(":category", $category);
			$stmt->bindParam(":img", $img);
			$stmt->bindParam(":tag", $tag);
			$stmt->bindParam(":open_Bit", $open_Bit);
			$stmt->bindParam(":kml_File", $kml_File);
			$stmt->bindParam(":reg_Id", $reg_Id);
			$stmt->bindParam(":reg_Date", $reg_Date);
			$stmt->execute();

			$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

			dbClose($DB_con);
			$stmt = null;
			$result = array("result" => "success");
		} else { //빈값일 경우
			$result = array("result" => "error", "errorMsg" => "콘텐츠등록실패");
		}
		echo json_encode($result);
	}else{
		$result = array("result" => "error", "errorMsg" => "파일업로드실패");
		echo json_encode($result);
	}
	// 파일이 없는 경우 
}else{
	/* 이미지 파일 업로드 시작 */
	if(isset($_FILES['img'])){
		$img_f_name = $_FILES['img']['name'];
		$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
		$img_target = "./img/".$img_fname ; 
		move_uploaded_file($_FILES['img']['tmp_name'],$img_target);
		$img = trim($_FILES['img']['name']);				//썸네일이미지
	}else{
		$img = "";				//썸네일이미지
	}
	/* 이미지 파일 업로드 종료 */
	$con_Name = trim($con_Name);				//콘텐츠이름 
	$category = trim($category);					//카테고리
	$tag = trim($tag);									//태그 (예 : 이사아폴리스,돼지고기,양많음,적극추천 등 태그를 , 로 구분하여 넣기.
	$open_Bit = trim($open_Bit);					//공개여부(0:전체공개, 1:비공개)
	$memo = trim($memo);							//메모
	if($open_Bit == ""){
		$open_Bit = "0";
	}else{
		$open_Bit = $open_Bit;
	}
	$reg_Id = trim($reg_Id);							//등록자
	$mIdx = memIdxInfo($reg_Id);	// 회원고유번호
	$reg_Date = DU_TIME_YMDHIS;				//등록일
	$chkQuery = "";
	$chkQuery = "SELECT mem_Lv FROM TB_MEMBERS WHERE mem_Id = :mem_Id ";
	$chkStmt = $DB_con->prepare($chkQuery);
	$chkStmt->bindparam(":mem_Id",$reg_Id);
	$chkStmt->execute();
	$chkrow = $chkStmt->fetch(PDO::FETCH_ASSOC);
	$mem_Lv = $chkrow['mem_Lv'];	//회원등급
	if ($reg_Id != "" && $con_Name != "" && $category != ""  ) {
		
		$DB_con = db1();
		
		//회원 기본테이블 저장
		$query = "INSERT INTO TB_CONTENTS (member_Idx, con_Name, con_Lv, category, img, tag, open_Bit, memo, reg_Id, reg_date, mod_Date ) VALUES (:member_Idx, :con_Name, :con_Lv, :category, :img, :tag, :open_Bit, :memo, :reg_Id, :reg_Date, NOW())";

		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":con_Name", $con_Name);
		$stmt->bindParam(":con_Lv", $mem_Lv);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":img", $img);
		$stmt->bindParam(":tag", $tag);
		$stmt->bindParam(":open_Bit", $open_Bit);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->bindParam(":reg_Date", $reg_Date);
		$stmt->execute();

		$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

		$history = "지도생성";
		$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :history, :reg_Id, :reg_Date)";
		$his_stmt = $DB_con->prepare($his_query);
		$his_stmt->bindParam(":member_Idx", $mIdx);
		$his_stmt->bindParam(":mem_Id", $reg_Id);
		$his_stmt->bindParam(":history", $history);
		$his_stmt->bindParam(":con_Idx", $mIdx);
		$his_stmt->bindParam(":reg_Id", $reg_Id);
		$his_stmt->bindParam(":reg_Date", $reg_Date);
		$his_stmt->execute();

		dbClose($DB_con);
		$stmt = null;
		$result = array("result" => "success");
	} else { //빈값일 경우
		$result = array("result" => "error", "errorMsg" => "콘텐츠등록실패");
	}
	echo json_encode($result);
}
?>



