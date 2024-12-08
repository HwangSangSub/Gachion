<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$place_Idx = trim($placeIdx);			// 지점고유번호
	$mode = trim($mode);					// 
	
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	
	$DB_con = db1();
	if ($place_Idx != "" ) {  // 지점고유번호가 있는경우
		$placeQuery = "SELECT idx FROM TB_PLACE WHERE idx = :place_Idx; " ;
		$placestmt = $DB_con->prepare($placeQuery);
		$placestmt->bindparam(":place_Idx",$place_Idx);
		$placestmt->execute();
		$placenum = $placestmt->rowCount();
		if($placenum < 1){
			$result = array("result" => "error", "errorMsg" => "지점이 없습니다.");
			echo json_encode($result); 
		}else{
			$query = "
				SELECT mem_Id, reg_Date
				FROM TB_MEMBERS_LIKE
				WHERE place_Idx = :place_Idx
					AND use_Bit = 'Y'
				ORDER BY reg_Date DESC;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":place_Idx", $place_Idx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "좋아요 내역이 없습니다.");
				echo json_encode($result); 
			}else{
				$data = [];
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
					$mem_Id = $row['mem_Id'];
					$reg_Date = $row['reg_Date'];
					$mem_Nname = memNickInfo($mem_Id);											// 회원닉네임
					$member_Img = memImgInfo($mem_Id);											// 회원이미지
					$result = ["mem_Id" => $mem_Id, "mem_Nname" => $mem_Nname, "member_Img" => $member_Img, "reg_Date" => $reg_Date];
					 array_push($data, $result);
				}
				$chkData = [];
				$chkData["result"] = "success";
				$chkData['lists'] = $data;
				$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
				echo  urldecode($output);
			}
		}
	}else{
		$result = array("result" => "error");
		echo json_encode($result); 
	}

	dbClose($DB_con);
	$stmt = null;
	$insstmt = null;
	$his_stmt = null;

?>