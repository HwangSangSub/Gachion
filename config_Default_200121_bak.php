<?
include "./lib/common.php";
$code_Div = trim($type);							//사용구분
$mem_Id = trim($memId);							//로그인한 아이디
if($mem_Id == ''){
	$mem_Id = 'GUEST';								// 비회원 인 경우 GUEST
}
$DB_con = db1();
// 좋아요가 많은 장소
if($code_Div == "category"){

	$memquery = "
		SELECT mem_Category
		FROM TB_MEMBERS_CONFIG 
		WHERE mem_Id = :mem_Id;
		";
	$memstmt = $DB_con->prepare($memquery);
	$memstmt->bindParam("mem_Id", $mem_Id);
	$memstmt->execute();
	$memrow=$memstmt->fetch(PDO::FETCH_ASSOC);
	$mem_Code = $memrow['mem_Category'];
	if($mem_Code == 'all' || $mem_Code == ''){
		$member_Category = "0";
	}else{
		$member_Category = explode(",",$mem_Code);
	}
	$query = "
		SELECT code, code_Name, code_on_Img, code_off_Img,use_guest_Bit
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
		if($member_Category == "0" || in_array($code, $member_Category)){
			$use_Bit = 'Y';
		}else{
			$use_Bit = 'N';
		}
		$use_guest_Bit = $row['use_guest_Bit'];
		if($use_guest_Bit == "0"){
			$use_guest_Bit = "Y";
		}else{
			$use_guest_Bit = "N";
		}
		$result = ["code" => $code, "code_Name" => $code_Name, "code_on_Img" => $code_on_Img, "code_off_Img" => $code_off_Img, "use_Bit" => $use_Bit, "use_guest_Bit" => $use_guest_Bit];
		 array_push($data, $result);
	}
	$chkData = [];
	$chkData["result"] = "success";
	$chkData["lists"] = $data;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);
}else{
	$result = array("result" => "error", "errorMsg" => "사용구분값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}

dbClose($DB_con);
$Stmt = null;	

?>


