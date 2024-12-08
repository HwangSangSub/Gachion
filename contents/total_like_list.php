<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$place_Idx = trim($placeIdx);			// 지점고유번호
	
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	
	$DB_con = db1();
	if ($place_Idx != "" ) {  // 지점고유번호가 있는경우
		$placeQuery = "SELECT like_Idx FROM TB_TOTAL_LIKE WHERE place_Idx = :place_Idx; " ;
		$placestmt = $DB_con->prepare($placeQuery);
		$placestmt->bindparam(":place_Idx",$place_Idx);
		$placestmt->execute();
		$placenum = $placestmt->rowCount();
		if($placenum < 1){
			$result = array("result" => "error", "errorMsg" => "통합좋아요 내역이 없습니다.");
			echo json_encode($result); 
		}else{
			$placerow=$placestmt->fetch(PDO::FETCH_ASSOC);
			$like_Idx = $placerow['like_Idx'];
			$query = "
				SELECT con_Idx, place_Idx, place_Name, like_Cnt, reg_Id, reg_Date
				FROM TB_TOTAL_LIKE
				WHERE like_Idx = :like_Idx
				ORDER BY like_Cnt DESC
				LIMIT 3;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":like_Idx", $like_Idx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "좋아요 내역이 없습니다.");
				echo json_encode($result); 
			}else{
				$data = [];
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
					$con_Idx = $row['con_Idx'];
					$place_Idx = $row['place_Idx'];
					$place_Name = $row['place_Name'];
					$like_Cnt = $row['like_Cnt'];
					$reg_Id = $row['reg_Id'];
					$reg_Date = $row['reg_Date'];
					$mem_Nname = memNickInfo($reg_Id);											// 회원닉네임
					if($mem_Nname == ""){
						$mem_Nname = "";
					}
					$member_Img = memImgInfo($reg_Id);											// 회원이미지
					if($member_Img == ""){
						$member_Img = "";
					}
					$result = ["con_Idx" => $con_Idx, "place_Idx" => $place_Idx, "place_Name" => $place_Name, "mem_Id" => $reg_Id, "mem_Nname" => $mem_Nname, "member_Img" => $member_Img, "like_Cnt" => $like_Cnt, "reg_Date" => $reg_Date];
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