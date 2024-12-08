<?
/*
* 프로그램				: 지점에 이미지등록하는 기능
* 페이지 설명			: 지점에 이미지등록하는 기능
* 파일명					: reg_img_place.php
* 관련DB					: TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$place_Idx = trim($placeIdx);							// 지점고유번호
$mode = trim($mode);									// 삭제시 del 필요
$DB_con = db1();
if($mode == "del"){
	$img = trim($img);
	//지점 확인
	$chk_query = "
			SELECT count(idx) as cnt
			FROM TB_PLACE
			WHERE idx = :place_Idx ;
		";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->bindParam(":place_Idx", $place_Idx);
	$chk_stmt->execute();
	$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
	$chk_Cnt = $chk_row['cnt'];
	if($chk_Cnt > 0){				//지점이 있는 경우
		$query = "
				SELECT member_Idx, con_Idx, reg_Id, img
				FROM TB_PLACE
				WHERE idx = :place_Idx ;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":place_Idx", $place_Idx);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$img_dir = $row['img'];
		$member_Idx = $row['member_Idx'];
		$con_Idx = $row['con_Idx'];
		$reg_Id = $row['reg_Id'];
/*
		$his_Query = "SELECT idx FROM TB_HISTORY WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND history = '사진업데이트' AND reg_Id = :reg_Id; " ;
		$his_stmt = $DB_con->prepare($his_Query);
		$his_stmt->bindparam(":con_Idx",$con_Idx);
		$his_stmt->bindparam(":place_Idx",$place_Idx);
		$his_stmt->bindparam(":reg_Id",$reg_Id);
		$his_stmt->execute();
		$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
		$his_idx = $his_row['idx'];
		$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
		$his_del_stmt = $DB_con->prepare($his_del_query);
		$his_del_stmt->bindParam(":idx", $his_idx);
		$his_del_stmt->execute();
*/
		//echo $img." / ".$file_dir;
		$imgexplode = explode( '/', $img );
		//print_r($imgexplode);
		//echo $file_img;
		//exit;
		// 파일삭제
		$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$img_dir;
		$file_img = $file_dir.'/'.$imgexplode['6'];
		@unlink($file_img);
		del_thumbnail(dirname($file_img), basename($file_img));
		$code_on_ImgFile = '';
		$result = array("result" => "success");
	}else{
		$result = array("result" => "error", "errorMsg" => "지점고유번호오류");
	}
}else{
	//지점 확인
	$chk_query = "
			SELECT count(idx) as cnt
			FROM TB_PLACE
			WHERE idx = :place_Idx ;
		";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->bindParam(":place_Idx", $place_Idx);
	$chk_stmt->execute();
	$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
	$chk_Cnt = $chk_row['cnt'];
	if($chk_Cnt > 0){				//지점이 있는 경우
		// 이미지가 없는 경우는 빈값처리
		// print_r($_FILES['img']);
		if(isset($_FILES['img'])){
			$query = "
				SELECT member_Idx, con_Idx, reg_Id, img
				FROM TB_PLACE
				WHERE idx = :place_Idx
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":place_Idx", $place_Idx);
			$stmt->execute();
			$row=$stmt->fetch(PDO::FETCH_ASSOC);
			$img = $row['img'];
			$member_Idx = $row['member_Idx'];
			$con_Idx = $row['con_Idx'];
			$reg_Id = $row['reg_Id'];
			if($img == ""){
				$now_time = time();													// 추후 파일 디렉토리가 될 예정
				$insQuery = "
					UPDATE TB_PLACE 
					SET 
						img ='".$now_time."' 
					WHERE 
						idx =	".$place_Idx." 
				";		
				$DB_con->exec($insQuery);  
			}else{
				$now_time = $img;													// 추후 파일 디렉토리가 될 예정
			}	
			$history = "사진업데이트";
			$con_Id = contentsIdInfo($con_Idx);	//지도를 등록한 아이디
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_Date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $member_Idx);
			$his_stmt->bindParam(":mem_Id", $reg_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":con_Idx", $con_Idx);
			$his_stmt->bindParam(":place_Idx", $place_Idx);
			$his_stmt->bindParam(":reg_Id", $reg_Id);
			$his_stmt->execute();						
			// 이미지 경로
			$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
			if(!is_dir($file_dir)){
				mkdir($file_dir, 0777, true);
			}
			// 이미지 ------------------------------------------------------------------
			$org_chk_code_Img = $file_dir.'/'.$chk_code_Img;
			foreach ($_FILES['img']['name'] as $f => $name) {   
				$img_ok = array("image/png", "image/gif", "image/jpg", "image/jpeg", "image/bmp", "image/GIF", "image/PNG", "image/JPG", "image/JPEG", "image/BMP", "image/webp", "image/WEBP");
				$detectedType = $_FILES['img']['type'][$f];
				if(in_array($detectedType, $img_ok)){
					if($detectedType == "image/png"){
						$uploadFName = "png";
					}else if($detectedType == "image/gif"){
						$uploadFName = "gif";
					}else if($detectedType == "image/jpg"){
						$uploadFName = "jpg";
					}else if($detectedType == "image/jpeg"){
						$uploadFName = "jpeg";
					}else if($detectedType == "image/bmp"){
						$uploadFName = "bmp";
					}else if($detectedType == "image/GIF"){
						$uploadFName = "gif";
					}else if($detectedType == "image/PNG"){
						$uploadFName = "png";
					}else if($detectedType == "image/JPG"){
						$uploadFName = "jpg";
					}else if($detectedType == "image/JPEG"){
						$uploadFName = "jpeg";
					}else if($detectedType == "image/BMP"){
						$uploadFName = "bmp";
					}else if($detectedType == "image/webp"){
						$uploadFName = "webp";
					}else if($detectedType == "image/WEBP"){
						$uploadFName = "webp";
					}
				}else{
				}
				// echo $uploadFName;
				
				$name = $_FILES['img']['name'][$f];
				$uploadName = explode('.', $name);
				// $fileSize = $_FILES['upload']['size'][$f];
				// $fileType = $_FILES['upload']['type'][$f];
				$uploadname = time().$f.'.'.$uploadFName;
				$uploadFile = $file_dir."/".$uploadname;
				move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile);
				// echo $uploadFile;
			}
			$result = array("result" => "success");
		}else{
			$result = array("result" => "error", "errorMsg" => "등록할이미지없음");
		}
	}else{
		$result = array("result" => "error", "errorMsg" => "지점이미지등록실패");
	}
}
dbClose($DB_con);
$stmt = null;
$chkStmt = null;
$his_stmt = null;
echo json_encode($result);
?>



