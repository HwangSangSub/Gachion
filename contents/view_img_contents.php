<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 등록된 지도 혹은 지점의 이미지를 보여줌 
* 페이지 설명			: 등록된 지도 혹은 지점으 전체 이미지를 보여줌
* 파일명					: view_img_contents.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($conIdx);							// 지도고유번호 
$place_Idx = trim($placeIdx);						// 지점고유번호 
$DB_con = db1();
if($con_Idx != "" && $place_Idx == ""){
	$chk_query = "
		SELECT img
		FROM TB_CONTENTS
		WHERE idx = :con_Idx
			OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y');";
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
				OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
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
			$img = [];
			$img_File = [];
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				$img = $row['img'];							// 이미지
				if($img == ''){
					$img_Cnt = "0";
					$img = array('');
				}else{
					$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$img;
					if(is_dir($m_file)){
						// 핸들 획득
						$handle  = opendir($m_file);
						$filename = readdir($handle);
						$files = array();
						// 디렉터리에 포함된 파일을 저장한다.
						while (false !== ($filename = readdir($handle))) {
							if($filename == "." || $filename == ".."){
								continue;
							}
							// 파일인 경우만 목록에 추가한다.
							$f_dir = $m_file . "/" . $filename;
							if(is_file($f_dir)){
								$files[] = $filename;
							}
						}
						// 핸들 해제 
						closedir($handle);
						// 정렬, 역순으로 정렬하려면 rsort 사용
						rsort($files);
						// 파일명을 출력한다.
						foreach ($files as $f) {
							$img_FileName = "https://places.gachita.kr/contents/place_img/".$img."/".$f;
							array_push($img_File, $img_FileName);
						} 
						$img_Cnt = count($files);
						$img = $img_File;
					}else{
						$img_Cnt = "0";
						$img = array('');
					}
				}
			}
			$chkData = [];
			$chkData["result"] = "success";
			$chkData["lists"] = $img;
			$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
			echo  urldecode($output); 
		}
	}
}else if($con_Idx == "" && $place_Idx != ""){
	$chk_query = "
		SELECT img
		FROM TB_PLACE
		WHERE idx = :place_Idx";
	$chk_stmt =$DB_con->prepare($chk_query);
	$chk_stmt->bindParam("place_Idx", $place_Idx);
	$chk_stmt->execute();
	$chk_Cnt = $chk_stmt->rowCount();
	if($chk_Cnt < 1){
		$result = array("result" => "error", "errorMsg" => "등록되지 않는 지점입니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else{
		$img = [];
		$img_File = [];
		while($row=$chk_stmt->fetch(PDO::FETCH_ASSOC)){
			$img = $row['img'];							// 이미지
			if($img == ''){
				$img_Cnt = "0";
				$img = array('');
			}else{
				$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$img;
				if(is_dir($m_file)){
					// 핸들 획득
					$handle  = opendir($m_file);
					$filename = readdir($handle);
					$files = array();
					// 디렉터리에 포함된 파일을 저장한다.
					while (false !== ($filename = readdir($handle))) {
						if($filename == "." || $filename == ".."){
							continue;
						}
						// 파일인 경우만 목록에 추가한다.
						$f_dir = $m_file . "/" . $filename;
						if(is_file($f_dir)){
							$files[] = $filename;
						}
					}
					// 핸들 해제 
					closedir($handle);
					// 정렬, 역순으로 정렬하려면 rsort 사용
					rsort($files);
					// 파일명을 출력한다.
					foreach ($files as $f) {
						$img_FileName = "https://places.gachita.kr/contents/place_img/".$img."/".$f;
						array_push($img_File, $img_FileName);
					} 
					$img_Cnt = count($files);
					$img = $img_File;
				}else{
					$img_Cnt = "0";
					$img = array('');
				}
			}
		}
		$chkData = [];
		$chkData["result"] = "success";
		$chkData["lists"] = $img;
		$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
		echo  urldecode($output); 
	}
}else if($con_Idx != "" && $place_Idx != ""){
	$result = array("result" => "error", "errorMsg" => "요청값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}else{
	$result = array("result" => "error", "errorMsg" => "요청값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
dbClose($DB_con);
?>



