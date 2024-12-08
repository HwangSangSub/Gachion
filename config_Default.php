<?
include "./lib/common.php";
include "./lib/functionDB.php";
$code_Div = trim($type);							//사용구분
$mem_Id = trim($memId);							//로그인한 아이디
if(memChk($mem_Id) == "0"){
	$mem_Id = "GUEST";							//회원아이디가 없는 경우 비회원으로 처리
}
$DB_con = db1();
// 좋아요가 많은 장소
if($code_Div == "category"){
	$memquery = "
		SELECT a.mem_Category
		FROM TB_MEMBERS_CONFIG a
			INNER JOIN TB_MEMBERS b ON a.idx = b.idx AND b.b_Disply = 'N'
		WHERE b.mem_Id = :mem_Id;
		";
	$memstmt = $DB_con->prepare($memquery);
	$memstmt->bindParam("mem_Id", $mem_Id);
	$memstmt->execute();
	$memrow=$memstmt->fetch(PDO::FETCH_ASSOC);
	$mem_Code = $memrow['mem_Category'];
	if($mem_Code == ''){
		$member_Category = "GUEST";
	}else{
		if($mem_Code == "all"){
			$member_Category = "0";
		}else{
			$member_Category = explode(",",$mem_Code);
		}
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
		$use_guest_Bit = $row['use_guest_Bit'];
		if($use_guest_Bit == "0"){
			$use_guest_Bit = "Y";
		}else{
			$use_guest_Bit = "N";
		}
		if($member_Category == "GUEST"){
			$use_Bit = $use_guest_Bit;
		}else if($member_Category == "0"){
			$use_Bit = 'Y';
		}else if(in_array($code, $member_Category)){
			$use_Bit = 'Y';
		}else{
			$use_Bit = 'N';
		}
		$result = ["code" => $code, "code_Name" => $code_Name, "code_on_Img" => $code_on_Img, "code_off_Img" => $code_off_Img, "use_Bit" => $use_Bit];
		 array_push($data, $result);
	}
	$chkData = [];
	$chkData["result"] = "success";
	$chkData["lists"] = $data;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);
}else if($code_Div == "placeicon"){	
	$chk_query = "
		SELECT *
		FROM TB_CONFIG_CODE 
		WHERE code_Div = 'category'
		";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->execute();
	$cate_Cnt = $chk_stmt->rowCount();
	if($cate_Cnt < 1){
		$cate_Cnt = 0;
	}else{
		$category = [];
		while($chk_row=$chk_stmt->fetch(PDO::FETCH_ASSOC)) {
			$code = $chk_row['code'];
			$code_Name = $chk_row['code_Name'];
			$cresult =  ["code" => $code, "name" => $code_Name, "lists" => array()];
			array_push($category, $cresult);
		}
	}
	$query = "
		SELECT code_Sub_Div, code, code_Name, code_on_Img, code_off_Img, use_Bit
		FROM TB_CONFIG_CODE 
		WHERE code_Div = :code_Div
			AND use_Bit = '0'
		ORDER BY idx ASC;
		";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam("code_Div", $code_Div);
	$stmt->execute();
	$data = [];
	$idx = 1;
	$c_code_Sub_Div = '';
	$c_Cnt = count($category);
	function search($array, $key, $value){
		$results = array();
		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}
			foreach ($array as $subarray) {
				$results = array_merge($results, search($subarray, $key, $value));
			}
		}
		if($results == ""){
			$results = $value;
		}else{
			$results = $key;
		}
		return $results;
	}
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$code_Sub_Div = $row['code_Sub_Div'];
		$code = $row['code'];								// 코드
		$code_Name = $row['code_Name'];				// 코드명
		$code_on_Img = $row['code_on_Img'];					// 코드이미지 (ON)
		if($code_on_Img == ""){
			$code_Img = "";
		}else{
			$code_Img = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_on_Img;
		}
		$mresult =  ["code" => $code, "code_Name" => $code_Name, "code_Img" => $code_Img];
		for($i = 0; $i < $c_Cnt; $i++){
			if($category[$i]["code"] == $code_Sub_Div){
				array_push($category[$i]["lists"], $mresult);
			}
		}
	}
	$chkData = [];
	$chkData["result"] = "success";
	$chkData["cateList"] = $category;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);
}else if($code_Div == "report"){	
	$query = "
		SELECT code, code_Name, use_Bit
		FROM TB_CONFIG_CODE 
		WHERE code_Div = :code_Div
		ORDER BY code DESC;
		";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam("code_Div", $code_Div);
	$stmt->execute();
	$data = [];
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$code = $row['code'];								// 코드
		$code_Name = $row['code_Name'];				// 코드명
		$use_Bit = $row['use_Bit'];
		if($use_Bit == "0"){
			$use_Bit = "Y";
		}else{
			$use_Bit = "N";
		}
		$result = ["code" => $code, "code_Name" => $code_Name, "use_Bit" => $use_Bit];
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


