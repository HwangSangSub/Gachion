<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$mem_Id = trim($memId);			// 아이디
	$mIdx = memIdxInfo($mem_Id);	// 회원고유번호
	$con_Idx = trim($conIdx);			// 담기 할 지도고유번호
	$place_Idx = trim($placeIdx);		// 담기 할 지도고유번호
	$mode = trim($mode);				// 담기해지인 경우 'del' 보내기
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	$DB_con = db1();
	if($mode == "del"){		//담기 해지인 경우
		if($mem_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :mem_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_Query = "SELECT idx FROM TB_MEMBERS_SHARE WHERE member_Idx = :member_Idx AND mem_Id = :mem_Id AND con_Idx = :con_Idx AND place_Idx = :place_Idx AND use_Bit = 'Y'; " ;
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindParam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->bindparam(":con_Idx",$con_Idx);
				$chk_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num < 1){
					$result = array("result" => "error", "errorMsg" => "담기중이 아닙니다.");
				}else{
					//담기취소하기
					$insquery ="UPDATE TB_MEMBERS_SHARE SET use_Bit = 'N', cancle_Date = NOW() WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND mem_Id = :mem_Id AND use_Bit = 'Y' AND member_Idx = :member_Idx LIMIT 1;";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindparam(":con_Idx",$con_Idx);
					$insstmt->bindparam(":place_Idx",$place_Idx);
					$insstmt->bindParam(":mem_Id", $mem_Id);
					$insstmt->execute();
					//$mIdx = $DB_con->lastInsertId();  //저장된 idx 값
					if($place_Idx != ""){
						$chk_p_query = "
							SELECT idx, member_Idx, share_Cnt
							FROM TB_PLACE
							WHERE idx = :idx;
						";
						$chk_p_stmt = $DB_con->prepare($chk_p_query);
						$chk_p_stmt->bindparam(":idx",$place_Idx);
						$chk_p_stmt->execute();
						$chk_p_row=$chk_p_stmt->fetch(PDO::FETCH_ASSOC);
						$share_t_Cnt = $chk_p_row['share_Cnt'];
						$member_Idx = $chk_p_row['member_Idx'];
						$share_Cnt = (int)$share_t_Cnt - 1;
						//담긴횟수 업데이트
						$insquery ="UPDATE TB_PLACE SET share_Cnt = :share_Cnt, mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :place_Idx LIMIT 1;";
						$insstmt = $DB_con->prepare($insquery);
						$insstmt->bindParam(":member_Idx", $member_Idx);
						$insstmt->bindparam(":place_Idx",$place_Idx);
						$insstmt->bindParam(":share_Cnt", $share_Cnt);
						$insstmt->execute();
					}

					$his_Query = "SELECT idx FROM TB_HISTORY WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND history = '담기' AND reg_Id = :reg_Id; " ;
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

					$result = array("result" => "success");
				}
			}
		}else{
			$result = array("result" => "error");
		}
	}else{
		if($mem_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :mem_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_my_Query = "
					SELECT con_Idx
					FROM TB_PLACE
					WHERE idx = :place_Idx;";
				$chk_my_stmt = $DB_con->prepare($chk_my_Query);
				$chk_my_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_my_stmt->execute();
				$chk_my_row=$chk_my_stmt->fetch(PDO::FETCH_ASSOC);
				$con_my_Idx = $chk_my_row['con_Idx'];
				if($con_my_Idx == $con_Idx){
					$result = array("result" => "error", "errorMsg" => "같은 지도에 담을 수 없습니다.");
				}else{
					$chk_Query = "
						SELECT *
						FROM TB_MEMBERS_SHARE
						WHERE mem_Id = :mem_Id
							AND use_Bit = 'Y'
							AND con_Idx = :con_Idx
							AND place_Idx = :place_Idx
							AND member_Idx = :member_Idx;";
					$chk_stmt = $DB_con->prepare($chk_Query);
					$chk_stmt->bindParam(":member_Idx", $mIdx);
					$chk_stmt->bindparam(":mem_Id",$mem_Id);
					$chk_stmt->bindparam(":con_Idx",$con_Idx);
					$chk_stmt->bindparam(":place_Idx",$place_Idx);
					$chk_stmt->execute();
					$chk_num = $chk_stmt->rowCount();
					if($chk_num > 0){
						$result = array("result" => "error", "errorMsg" => "이미 내 지도에 담은 지점입니다.");
					}else{
						//지점담아가기
						$insquery = "INSERT INTO TB_MEMBERS_SHARE (member_Idx, mem_Id, con_Idx, place_Idx, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :reg_Date);";
						$insstmt = $DB_con->prepare($insquery);
						$insstmt->bindParam(":member_Idx", $mIdx);
						$insstmt->bindParam(":mem_Id", $mem_Id);
						$insstmt->bindParam(":con_Idx", $con_Idx);
						$insstmt->bindParam(":place_Idx", $place_Idx);
						$insstmt->bindParam(":reg_Date", $reg_Date);
						$insstmt->execute();
						$sIdx = $DB_con->lastInsertId();  //저장된 idx 값
					
						if($sIdx != ""){
							$chk_p_query = "
								SELECT idx, member_Idx, category, place_Name, place_Icon, memo, tel, img, otime_Day, otime_Week, addr, lng, lat, share_Cnt, like_Cnt, tLike_Idx
								FROM TB_PLACE
								WHERE idx = :idx;
							";
							$chk_p_stmt = $DB_con->prepare($chk_p_query);
							$chk_p_stmt->bindparam(":idx",$place_Idx);
							$chk_p_stmt->execute();
							$chk_p_row=$chk_p_stmt->fetch(PDO::FETCH_ASSOC);
							$share_t_Cnt = $chk_p_row['share_Cnt'];	
							$category = $chk_p_row['category'];
							$member_Idx = $chk_p_row['member_Idx'];
							$place_Name = $chk_p_row['place_Name'];
							$place_Icon = $chk_p_row['place_Icon'];
							$memo = $chk_p_row['memo'];
							$tel = $chk_p_row['tel'];
							$org_img = $chk_p_row['img'];
							$otime_Day = $chk_p_row['otime_Day'];
							$otime_Week = $chk_p_row['otime_Week'];
							$addr = $chk_p_row['addr'];
							$lng = $chk_p_row['lng'];
							$lat = $chk_p_row['lat'];
							$like_Cnt = $chk_p_row['like_Cnt'];
							$tLike_Idx = $chk_p_row['tLike_Idx'];
							$share_Cnt = (int)$share_t_Cnt + 1;
							$now_time = time();										// 추후 파일 디렉토리가 될 예정
							if($org_img != ""){
								$org_file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$img;
								if(is_dir($org_file_dir)){
									$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
									copy($org_file_dir, $file_dir);
									$img = $now_time;
								}
							}else{
								$img = "";
							}
							// TB_PLACE : 지점담긴수 + 업데이트 일자 적용
							$upquery ="UPDATE TB_PLACE SET share_Cnt = :share_Cnt, mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :place_Idx LIMIT 1;";
							$upstmt = $DB_con->prepare($upquery);
							$upstmt->bindParam(":member_Idx", $member_Idx);
							$upstmt->bindparam(":place_Idx",$place_Idx);
							$upstmt->bindParam(":share_Cnt", $share_Cnt);
							$upstmt->execute();
							// 담긴지점은 새로운 지점으로 생성 단 정보는 담아온 지점의 정보(댓글제외)
							$new_p_insquery = "
								INSERT INTO TB_PLACE (con_Idx, member_Idx, org_place_Idx, category, place_Name, place_Icon, memo, tel, img, otime_Day, otime_Week, addr, lng, lat, tLike_Idx, like_Cnt, share_Cnt, reg_Id, mod_Date, reg_date) 
								VALUES (:con_Idx, :member_Idx, :org_place_Idx, :category, :place_Name, :place_Icon, :memo, :tel, :img, :otime_Day, :otime_Week, :addr, :lng, :lat, :tLike_Idx, :like_Cnt, :share_Cnt, :reg_Id, NOW(), NOW());";
							$new_p_insstmt = $DB_con->prepare($new_p_insquery);
							$new_p_insstmt->bindParam(":con_Idx", $con_Idx);
							$new_p_insstmt->bindParam(":member_Idx", $mIdx);
							$new_p_insstmt->bindParam(":org_place_Idx", $place_Idx);
							$new_p_insstmt->bindParam(":category", $category);
							$new_p_insstmt->bindParam(":place_Name", $place_Name);
							$new_p_insstmt->bindParam(":place_Icon", $place_Icon);
							$new_p_insstmt->bindParam(":memo", $memo);
							$new_p_insstmt->bindParam(":tel", $tel);
							$new_p_insstmt->bindParam(":img", $img);
							$new_p_insstmt->bindParam(":otime_Day", $otime_Day);
							$new_p_insstmt->bindParam(":otime_Week", $otime_Week);
							$new_p_insstmt->bindParam(":addr", $addr);
							$new_p_insstmt->bindParam(":lng", $lng);
							$new_p_insstmt->bindParam(":lat", $lat);
							$new_p_insstmt->bindParam(":tLike_Idx", $tLike_Idx);
							$new_p_insstmt->bindParam(":like_Cnt", $like_Cnt);
							$new_p_insstmt->bindParam(":share_Cnt", $share_Cnt);
							$new_p_insstmt->bindParam(":reg_Id", $mem_Id);
							$new_p_insstmt->execute();
							if($con_Idx != ""){
								// TB_CONTENTS : 지점담긴수 + 업데이트 일자 적용
								$con_query ="UPDATE TB_CONTENTS SET mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :con_Idx LIMIT 1;";
								$con_stmt = $DB_con->prepare($con_query);
								$con_stmt->bindParam(":member_Idx", $mIdx);
								$con_stmt->bindparam(":con_Idx",$con_Idx);
								$con_stmt->execute();
							}
						}
						$history = "담기";
						$con_Id = contentsIdInfo($con_Idx);	//지도를 등록한 아이디
						$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, :reg_Date)";
						$his_stmt = $DB_con->prepare($his_query);
						$his_stmt->bindParam(":member_Idx", $mIdx);
						$his_stmt->bindParam(":mem_Id", $con_Id);
						$his_stmt->bindParam(":history", $history);
						$his_stmt->bindParam(":con_Idx", $con_Idx);
						$his_stmt->bindParam(":place_Idx", $place_Idx);
						$his_stmt->bindParam(":reg_Id", $mem_Id);
						$his_stmt->bindParam(":reg_Date", $reg_Date);
						$his_stmt->execute();
						$result = array("result" => "success");
					}
				}
			}
		}else{
			$result = array("result" => "error");
		}
	}


	dbClose($DB_con);
	$stmt = null;
	$insstmt = null;
	$his_stmt = null;
  echo json_encode($result); 

?>