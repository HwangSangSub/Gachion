<?php
$idx = $_POST['idx'];
include "/hd/webFolder/places/udev/lib/common.php"; 
$DB_con = db1();
/*
	$_tmp = explode("data:image/png;base64,", $_POST['imgUpload']);
	echo $_tmp[1];
	$imageData = base64_decode($_tmp[1]);
	//$imageData = base64_decode($_POST['imgUpload']);
	$photo = imagecreatefromstring($imageData);
	 
	$new_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/map_img/contents/';
	 
	if(!is_dir($new_dir)){
	  @mkdir($new_dir);
	}
	 
	imagejpg($photo,$new_dir.$_POST['filename'],100);
*/
$_tmp = explode("data:image/png;base64,", $_POST['imgUpload']);

$filename = $_POST['filename'];
if(count($_tmp) == 2) {
	$imageData = base64_decode($_tmp[1]);
	if(is_file($filename)){
		unlink($filename);
		$fp = fopen($filename, "wb");
		if($fp) {
			fwrite($fp, $imageData);
			fclose($fp);
			echo "success";
			$query = "
				UPDATE TB_CONTENTS
				SET map_Img = '".$filename."'
				WHERE idx = :idx;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindparam(":idx",$idx);
			$stmt->execute();
		} else {
			echo "failed";
		}
	}else{
		$fp = fopen($filename, "wb");
		if($fp) {
			fwrite($fp, $imageData);
			fclose($fp);
			echo "success";
			$query = "
				UPDATE TB_CONTENTS
				SET map_Img = '".$filename."'
				WHERE idx = :idx;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindparam(":idx",$idx);
			$stmt->execute();
		} else {
			echo "failed";
		}
	}
}
dbClose($DB_con);
?>