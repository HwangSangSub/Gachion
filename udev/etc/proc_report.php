<?

	include "../lib/common.php"; 
	include "../../lib/functionDB.php";  //공통 db함수
	include "../lib/alertLib.php";
	
	$idx = trim($idx);
	$con_Idx =  trim($con_Idx);
	$place_Idx = trim($place_Idx);
	$penalty_Bit = trim($penalty_Bit);
	$mem_Id = trim($reg_Id);
	$mIdx = memIdxInfo($mem_Id);			// 회원고유번호
	$DB_con = db1();
	
	if ($penalty_Bit == "Y") {							// Y 신고부적합

		$upQquery = "UPDATE TB_MEMBERS_REPORT SET  admin_Bit = 'Y', penalty_Bit = 'Y' WHERE  idx = :idx  LIMIT 1;";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		$preUrl = "reg_report.php?mode=mod&idx=".$idx;
		$message = "mod";
		proc_msg($message, $preUrl);

	} else if ($penalty_Bit == "A") {					// A 패널티 A : 해당 유저가 좋아요 했을 경우 취소처리

		$upQquery = "UPDATE TB_MEMBERS_REPORT SET  admin_Bit = 'Y', penalty_Bit = 'A', admin_Date = NOW() WHERE  idx = :idx  LIMIT 1;";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		// 좋아요 취소처리
		$chk_Query = "SELECT idx FROM TB_MEMBERS_LIKE WHERE member_Idx = :member_Idx AND mem_Id = :mem_Id AND con_Idx = :con_Idx AND place_Idx = :place_Idx AND use_Bit = 'Y'; " ;
		$chk_stmt = $DB_con->prepare($chk_Query);
		$chk_stmt->bindParam(":member_Idx", $mIdx);
		$chk_stmt->bindparam(":mem_Id",$mem_Id);
		$chk_stmt->bindparam(":con_Idx",$con_Idx);
		$chk_stmt->bindparam(":place_Idx",$place_Idx);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
		if($chk_num < 1){
		}else{
			//담기취소하기
			$insquery ="UPDATE TB_MEMBERS_LIKE SET use_Bit = 'N', cancle_Date = NOW() WHERE place_Idx = :place_Idx AND mem_Id = :mem_Id AND use_Bit = 'Y' AND member_Idx = :member_Idx LIMIT 1;";
			$insstmt = $DB_con->prepare($insquery);
			$insstmt->bindParam(":member_Idx", $mIdx);
			$insstmt->bindparam(":place_Idx",$place_Idx);
			$insstmt->bindParam(":mem_Id", $mem_Id);
			$insstmt->execute();
			//$mIdx = $DB_con->lastInsertId();  //저장된 idx 값
			if($place_Idx != ""){
				$chk_p_query = "
					SELECT idx, member_Idx, like_Cnt
					FROM TB_PLACE
					WHERE idx = :idx;
				";
				$chk_p_stmt = $DB_con->prepare($chk_p_query);
				$chk_p_stmt->bindparam(":idx",$place_Idx);
				$chk_p_stmt->execute();
				$chk_p_row=$chk_p_stmt->fetch(PDO::FETCH_ASSOC);
				$like_t_Cnt = $chk_p_row['like_Cnt'];
				$member_Idx = $chk_p_row['member_Idx'];
				$like_Cnt = (int)$like_t_Cnt - 1;
				if($like_Cnt < 0){
					$like_Cnt = 0;
				}
				//담긴횟수 업데이트
				$insquery ="UPDATE TB_PLACE SET like_Cnt = :like_Cnt, mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :place_Idx LIMIT 1;";
				$insstmt = $DB_con->prepare($insquery);
				$insstmt->bindParam(":member_Idx", $member_Idx);
				$insstmt->bindparam(":place_Idx",$place_Idx);
				$insstmt->bindParam(":like_Cnt", $like_Cnt);
				$insstmt->execute();

				 // 해당 지점의 정보 가져오기 
				$info_query = "
					SELECT idx, con_Idx, place_Name, tLike_Idx, like_Cnt, lng, lat, reg_Id, reg_Date
					FROM TB_PLACE
					WHERE idx = :place_Idx;
				";
				$info_stmt = $DB_con->prepare($info_query);
				$info_stmt->bindParam(":place_Idx", $place_Idx);
				$info_stmt->execute();
				$info_num = $info_stmt->rowCount();
				if($info_num > 0){
					while($info_row=$info_stmt->fetch(PDO::FETCH_ASSOC)){
						$info_con_Idx = $info_row['con_Idx'];
						$info_place_Idx = $info_row['idx'];
						$info_place_Name = $info_row['place_Name'];
						$info_tLike_Idx = $info_row['tLike_Idx'];
						$info_like_Cnt = $info_row['like_Cnt'];
						$info_lng = $info_row['lng'];
						$info_lat = $info_row['lat'];
						$info_reg_Id = $info_row['reg_Id'];
						$info_reg_Date = $info_row['reg_Date'];
						//통합좋아요 그룹코드 등록하기
						$p_up_query ="
							UPDATE TB_TOTAL_LIKE 
							SET like_Cnt = :like_Cnt
							WHERE like_Idx = :like_Idx 	 
								AND con_Idx = :info_con_Idx 
								AND place_Idx = :info_place_Idx LIMIT 1;";
						$p_up_stmt = $DB_con->prepare($p_up_query);
						$p_up_stmt->bindparam(":like_Cnt",$like_Cnt);
						$p_up_stmt->bindparam(":like_Idx",$info_tLike_Idx);
						$p_up_stmt->bindParam(":info_con_Idx", $info_con_Idx);
						$p_up_stmt->bindParam(":info_place_Idx", $info_place_Idx);
						$p_up_stmt->execute();
					}
				}
			}

			$his_Query = "SELECT idx FROM TB_HISTORY WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND history = '좋아요' AND reg_Id = :reg_Id; " ;
			$his_stmt = $DB_con->prepare($his_Query);
			$his_stmt->bindparam(":con_Idx",$con_Idx);
			$his_stmt->bindparam(":place_Idx",$place_Idx);
			$his_stmt->bindparam(":reg_Id",$mem_Id);
			$his_stmt->execute();
			$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
			$his_idx = $his_row['idx'];
			$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
			$his_del_stmt = $DB_con->prepare($his_del_query);
			$his_del_stmt->bindParam(":idx", $his_idx);
			$his_del_stmt->execute();

			$preUrl = "reg_report.php?mode=mod&idx=".$idx;
			$message = "mod";
			proc_msg($message, $preUrl);
		}


	} else if ($penalty_Bit == "B") {					// B 패널티 B : 해당 지도의 메인/검색 리스트 미노출

		$upQquery = "UPDATE TB_MEMBERS_REPORT SET  admin_Bit = 'Y', penalty_Bit = 'B', admin_Date = NOW() WHERE  idx = :idx  LIMIT 1;";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		// 관리자공개여부 : 비공개처리하기
		$upQquery = "UPDATE TB_CONTENTS SET  admin_Bit = '1', mod_Date = NOW() WHERE  idx = :con_Idx  LIMIT 1;";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":con_Idx", $con_Idx);
		$upStmt->execute();

		$preUrl = "reg_report.php?mode=mod&idx=".$idx;
		$message = "mod";
		proc_msg($message, $preUrl);

	}
	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;



?>