<?
/*
* 프로그램				: 지도정보를 수정하는 기능
* 페이지 설명			: 카테고리, 지도명
* 파일명					: mod_con.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$con_Idx = trim($conIdx);									//지도고유번호
$con_Name = trim($conName);							//지도명
if($con_Name == ""){
	$con_Name = "";
}
$category = trim($category);								//지도카테고리
if($category == ""){
	$category = "";
}
$memo = trim($memo);										//지도소개글
if($memo == ""){
	$memo = "";
}
$tag = trim($tag);												//지도태그
if($tag == ""){	
	$tag = "";
}
$open_Bit = trim($openBit);								//지도공개설정여부
if($open_Bit == ""){
	$open_Bit = "";
}
$del_Img_Bit = trim($delimg_Bit);							//썸네일 이미지 삭제여부
if($del_Img_Bit == ""){
	$del_Img_Bit = "0";										//디폴트 삭제안함
}
$now_time = time();											// 추후 파일 디렉토리가 될 예정
/* 이미지 파일 업로드 시작 */
if($_FILES['img']['name'] != ""){
	if(isset($_FILES['img'])){
		$img_f_name = $con_Idx."_".$_FILES['img']['name'];
		$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
		$img_target = "./img/".$img_fname ; 
		move_uploaded_file($_FILES['img']['tmp_name'],$img_target);	

		$img = trim($img_f_name);							//썸네일이미지
	}else{
		$img = "";												//썸네일이미지
	}
}else{
	$img = "";													//썸네일이미지
}
$DB_con = db1();


// 지점고유번호를 확인한다.
$chk_query = "
		SELECT count(idx) as cnt
		FROM TB_CONTENTS
		WHERE idx = :con_Idx ;
	";
$chk_stmt = $DB_con->prepare($chk_query);
$chk_stmt->bindParam(":con_Idx", $con_Idx);
$chk_stmt->execute();
$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
$chk_Cnt = $chk_row['cnt'];
if($chk_Cnt > 0){				//지점이 있는 경우
	$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/img';
	if($del_Img_Bit == "1"){
		$img_query = "
			SELECT idx, member_Idx, img
			FROM TB_CONTENTS
			WHERE idx = :con_Idx
		";
		$img_stmt = $DB_con->prepare($img_query);
		$img_stmt->bindParam(":con_Idx", $con_Idx);
		$img_stmt->execute();
		while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)){
			$org_del_img = $img_row['img'];
			$member_Idx = $img_row['member_Idx'];
			$file_img = $file_dir.'/'.$org_del_img;
			@unlink($file_img);
			del_thumbnail(dirname($file_img), basename($file_img));
			$delImg_query = "
				UPDATE TB_CONTENTS
				SET img = '',
					mod_Date = NOW()
				WHERE member_Idx = :member_Idx
					AND idx = :con_Idx
				LIMIT 1;
			";
			$delImg_stmt = $DB_con->prepare($delImg_query);
			$delImg_stmt->bindParam(":member_Idx", $member_Idx);
			$delImg_stmt->bindParam(":con_Idx", $con_Idx);
			$delImg_stmt->execute();
		}
	}
	$query = "
		SELECT idx, member_Idx, con_Name, img, memo, category, tag, open_Bit
		FROM TB_CONTENTS
		WHERE idx = :con_Idx
	";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":con_Idx", $con_Idx);
	$stmt->execute();
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		$idx = $row['idx'];
		$member_Idx = $row['member_Idx'];
		$org_con_Name = $row['con_Name'];
		if($con_Name == ""){
			$con_Name = $org_con_Name;
		}else{
			$con_Name = $con_Name;
		}
		$org_memo = $row['memo'];
		if($memo == ""){
			$memo = $org_memo;
		}else{
			$memo = $memo;
		}
		$org_category = $row['category'];
		if($category == ""){
			$category = $org_category;
		}else{
			$category = $category;
		}

		$org_tag = $row['tag'];
		if($tag == ""){
			$tag = $org_tag;
		}else{
			$tag = $tag;
		}
		$org_open_Bit = $row['open_Bit'];
		if($open_Bit == ""){
			$open_Bit = $org_open_Bit;
		}else{
			$open_Bit = $open_Bit;
		}
		$org_img = $row['img'];
		if($img == ""){
			$img = $org_img;
		}else{
			if($org_img != ""){
				$file_img = $file_dir.'/'.$org_img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
				$img = $img;
			}else{
				$img = $img;
			}
		}
		$mod_query = "
			UPDATE TB_CONTENTS
			SET category = :category, 
				con_Name = :con_Name,
				img = :img,
				memo = :memo,
				open_Bit = :open_Bit,
				tag = :tag,
				mod_Date = NOW()
			WHERE member_Idx = :member_Idx
				AND idx = :con_Idx
			LIMIT 1;
		";
		$mod_stmt = $DB_con->prepare($mod_query);
		$mod_stmt->bindParam(":category", $category);
		$mod_stmt->bindParam(":con_Name", $con_Name);
		$mod_stmt->bindParam(":img", $img);
		$mod_stmt->bindParam(":memo", $memo);
		$mod_stmt->bindParam(":open_Bit", $open_Bit);
		$mod_stmt->bindParam(":tag", $tag);
		$mod_stmt->bindParam(":member_Idx", $member_Idx);
		$mod_stmt->bindParam(":con_Idx", $con_Idx);
		$mod_stmt->execute();
	}

    dbClose($DB_con);
	$chk_stmt = null;
    $stmt = null;
	$mod_stmt = null;
	$mod_stmt2 = null;
	$result = array("result" => "success");
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점고유번호오류");
}

echo json_encode($result);