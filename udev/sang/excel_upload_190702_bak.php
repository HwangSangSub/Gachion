<?
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
include "../lib/functionCoupon.php";    //쿠폰관련 함수
if(isset($_FILES['upload'])){ 
	$target = "./excel_data/".basename($_FILES['upload']['name']) ; 
	if(move_uploaded_file($_FILES['upload']['tmp_name'],$target)) {
		$result = array("result" => "success");
	}else{
		$result = array("result" => "error");
	}
}else{ 
	$result = array("result" => "error");
} 
echo json_encode($result);
?>