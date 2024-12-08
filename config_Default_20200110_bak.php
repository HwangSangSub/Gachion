<?
include "./lib/common.php";
$code_Div = trim($type);							//사용구분
$DB_con = db1();
// 좋아요가 많은 장소
$query = "
	SELECT code, code_Name, code_on_Img, code_off_Img
	FROM TB_CONFIG_CODE 
	WHERE code_Div = :code_Div
	ORDER BY code;
	";
$stmt = $DB_con->prepare($query);
$stmt->bindParam("code_Div", $code_Div);
$stmt->execute();
$data = [];
while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$code = $row['code'];								// 코드
	$code_Name = $row['code_Name'];				// 코드명
	$code_on_Img = $row['code_on_Img'];					// 코드이미지 (ON)
	if($code_on_Img == ""){
		$code_on_Img = "";
	}else{
		$code_on_Img = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_on_Img;
	}
	$code_off_Img = $row['code_off_Img'];					// 코드이미지 (OFF)
	if($code_off_Img == ""){
		$code_off_Img = "";
	}else{
		$code_off_Img = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_off_Img;
	}
	$result = ["code" => $code, "code_Name" => $code_Name, "code_on_Img" => $code_on_Img, "code_off_Img" => $code_off_Img];
	 array_push($data, $result);
}
$chkData = [];
$chkData["result"] = "success";
$chkData["lists"] = $data;
$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
echo  urldecode($output);

dbClose($DB_con);
$Stmt = null;	

?>


