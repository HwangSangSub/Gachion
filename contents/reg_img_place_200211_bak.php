<?
/*
* 프로그램				: 지점에 이미지등록하는 기능
* 페이지 설명			: 지점에 이미지등록하는 기능
* 파일명					: reg_img_place.php
* 관련DB					: TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$place_Idx = trim($placeIdx);							//지점고유번호
$DB_con = db1();
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
	if(isset($_FILES['img'])){
		$query = "
			SELECT img
			FROM TB_PLACE
			WHERE idx = :place_Idx
		";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":place_Idx", $place_Idx);
		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$img = $row['img'];
		if($img == ""){
			$now_time = time();													// 추후 파일 디렉토리가 될 예정
			$insQuery = "
				update TB_PLACE 
				set 
					img ='".$now_time."' 
				where 
					idx =	".$place_Idx." 
			";		
			$DB_con->exec($insQuery);  
		}else{
			$now_time = $img;													// 추후 파일 디렉토리가 될 예정
		}							
		// 이미지 경로
		$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
		if(!is_dir($file_dir)){
			mkdir($file_dir, 0777, true);
		}
		// 이미지 ------------------------------------------------------------------
		$org_chk_code_Img = $file_dir.'/'.$chk_code_Img;
		foreach ($_FILES['img']['name'] as $f => $name) {   
			$img_ok = array("image/png", "image/gif", "image/jpg", "image/jpeg", "image/bmp", "image/GIF", "image/PNG", "image/JPG", "image/JPEG", "image/BMP");
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
				}
			}else{
			}
			
			$name = $_FILES['img']['name'][$f];
			$uploadName = explode('.', $name);
			// $fileSize = $_FILES['upload']['size'][$f];
			// $fileType = $_FILES['upload']['type'][$f];
			$uploadname = time().$f.'.'.$uploadFName;
			$uploadFile = $file_dir."/".$uploadname;
			move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile);
		}
		dbClose($DB_con);
		$stmt = null;
		$chkStmt = null;
		$his_stmt = null;
		$result = array("result" => "success");
	}else{
		$result = array("result" => "error", "errorMsg" => "등록할이미지없음");
	}
}else{
	$result = array("result" => "error", "errorMsg" => "지점이미지등록실패");
}
	echo json_encode($result);
?>



