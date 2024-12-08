<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 등록된 지도에
* 페이지 설명			: 등록된 지점 목록을 보여줌(지도에 표시하기 위함)
* 파일명					: view_img_contents.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($con_Idx);						// 지도고유번호 
$DB_con = db1();
if($con_Idx != ''){
	$chk_query = "
		SELECT img
		FROM TB_CONTENTS
		WHERE idx = :con_Idx";
	$chk_stmt =$DB_con->prepare($chk_query);
	$chk_stmt->bindParam("con_Idx", $con_Idx);
	$chk_stmt->execute();
	$chk_Cnt = $chk_stmt->rowCount();
	if($chk_Cnt < 1){
		$result = array("result" => "error", "errorMsg" => "등록되지 않는 지도입니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else{
		$query = "
			SELECT img
			FROM TB_PLACE
			WHERE con_Idx = :con_Idx
			ORDER BY reg_Date DESC;
		";
		$stmt =$DB_con->prepare($query);
		$stmt->bindParam("con_Idx", $con_Idx);
		$stmt->execute();
		$Cnt = $stmt->rowCount();
		if($Cnt < 1){
			$result = array("result" => "error", "errorMsg" => "등록된 지점이 없습니다.");
			echo json_encode($result, JSON_UNESCAPED_UNICODE); 
		}else{
			$data = [];
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				$img = $row['img'];
				if($img == ''){
					continue;
				}
				$p__Img = "https://places.gachita.kr/contents/place_img/".$img;
				 array_push($data, $p__Img);
			}
			$chkData = [];
			$chkData["result"] = "success";
			$chkData["lists"] = $data;
			$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
			echo  urldecode($output); 
		}
	}
	
}else{
	$result = array("result" => "error", "errorMsg" => "지도고유번호 빈값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
dbClose($DB_con);
?>



