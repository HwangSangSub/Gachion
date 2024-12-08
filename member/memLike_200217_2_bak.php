<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$mem_Id = trim($memId);					// 아이디
	$mIdx = memIdxInfo($mem_Id);			// 회원고유번호
	$place_Idx = trim($placeIdx);				// 좋아요 할 지점고유번호
	$con_Idx = conIdxInfo($place_Idx);		// 좋아요 할 지점의 지도고유번호
	if($con_Idx == ""){
		$con_Idx = "";
	}
	$mode = trim($mode);						// 좋아요취소인 경우 'del' 보내기
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
				$chk_Query = "SELECT idx FROM TB_MEMBERS_LIKE WHERE member_Idx = :member_Idx AND mem_Id = :mem_Id AND con_Idx = :con_Idx AND place_Idx = :place_Idx AND use_Bit = 'Y'; " ;
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindParam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->bindparam(":con_Idx",$con_Idx);
				$chk_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num < 1){
					$result = array("result" => "error", "errorMsg" => "좋아요 중이 아닙니다.");
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
				$chk_Query = "
					SELECT *
					FROM TB_MEMBERS_LIKE
					WHERE mem_Id = :mem_Id
						AND use_Bit = 'Y'
						AND place_Idx = :place_Idx
						AND member_Idx = :member_Idx;";
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindParam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num > 0){
					$result = array("result" => "error", "errorMsg" => "이미 좋아요 한 지점입니다.");
				}else{
					//지점 좋아요하기
					$insquery = "INSERT INTO TB_MEMBERS_LIKE (member_Idx, mem_Id, con_Idx, place_Idx, reg_Date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :reg_Date);";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindParam(":mem_Id", $mem_Id);
					$insstmt->bindParam(":con_Idx", $con_Idx);
					$insstmt->bindParam(":place_Idx", $place_Idx);
					$insstmt->bindParam(":reg_Date", $reg_Date);
					$insstmt->execute();
					$sIdx = $DB_con->lastInsertId();  //저장된 idx 값
					$sIdx = "1";
					if($sIdx != ""){
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
						$like_Cnt = (int)$like_t_Cnt + 1;
						//담긴횟수 업데이트
						$insquery ="UPDATE TB_PLACE SET like_Cnt = :like_Cnt, mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :place_Idx LIMIT 1;";
						$insstmt = $DB_con->prepare($insquery);
						$insstmt->bindParam(":member_Idx", $member_Idx);
						$insstmt->bindparam(":place_Idx",$place_Idx);
						$insstmt->bindParam(":like_Cnt", $like_Cnt);
						$insstmt->execute();
						/*
						 *	::: 통합좋아요 처리하기 :::
						 *  과정 : 지점좋아요 선택 =>> 해당 지점의 좌표와 지점명으로 아래의 조건일 경우에만 통합좋아요 테이블 지정
						 *  조건 1 : 좌표를 기준으로 100M 이내 지점이 있는 검색.
						 *  조건 2 : 조건 1을 만족하면서 지점명이 비슷한 경우에만 리스트에 추가한다. (다른 지점이 해당 조건에 만족하는 경우 계속 리스트에 추가)
						 *  이후 : 만약 이미 등록되어 있는 경우에는 해당지점 좋아요 Cnt만 업데이트 시킬 것.
						 *  지점삭제시 : 삭제된 지점이 통합좋아요에 등록이 되어 있다면 해당 정보를 삭제 시킬 것.
						 *  통합좋아요 : cnt 해당 그룹을 조회하여 합산하여 내려보낸다.
						 *  해당 지점의 표시방식은 통합좋아요 테이블에 포함여부를 확인할 수 있는 값으로 폰에서 확인하게 한다.
						 *  만약 포함되어 있다면 지점의 모습은 일반 핀이 아닌 통합좋아요 리스트 출력으로 한다.
						 */
						 // 해당 지점의 정보 가져오기 
						$info_query = "
							SELECT idx, con_Idx, place_Name, like_Cnt, lng, lat, reg_Id, reg_Date
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
								$info_like_Cnt = $info_row['like_Cnt'];
								$info_lng = $info_row['lng'];
								$info_lat = $info_row['lat'];
								$info_reg_Id = $info_row['reg_Id'];
								$info_reg_Date = $info_row['reg_Date'];
								// 통합좋아요에 등록된 값 확인하기
								$t_like_query = "
									SELECT count(idx) as cnt
									FROM TB_TOTAL_LIKE
									WHERE ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) ) < 0.1;
								";
								$t_like_stmt = $DB_con->prepare($t_like_query);
								$t_like_stmt->bindParam(":locatLat", $info_lat);
								$t_like_stmt->bindParam(":locatLng", $info_lng);
								$t_like_stmt->execute();
								$t_like_row=$t_like_stmt->fetch(PDO::FETCH_ASSOC);
								$t_like_chk = $t_like_row['cnt'];
								if($t_like_chk < 1){	
									// 통합좋아요에 등록이 되어 있지 않음.
									$t_like_chk_query = "
									SELECT idx, like_idx, place_Name
									FROM TB_TOTAL_LIKE
									WHERE ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) ) < 0.1
									ORDER BY reg_Date ASC;
									";
									$t_like_chk_stmt = $DB_con->prepare($t_like_chk_query);
									$t_like_chk_stmt->bindParam(":locatLat", $info_lat);
									$t_like_chk_stmt->bindParam(":locatLng", $info_lng);
									$t_like_chk_stmt->execute();
									$t_like_chk_num = $t_like_chk_stmt->rowCount();
									if($t_like_chk_num > 0){
										while($t_like_chk_row=$t_like_chk_stmt->fetch(PDO::FETCH_ASSOC)){
											$idx = $t_like_chk_row['idx'];
											$likeIdx = $t_like_chk_row['like_Idx'];
											$likeName = $t_like_chk_row['place_Name'];
											$org_nm_cnt = strlen($info_place_Name);
											//echo "/".$org_nm_cnt;
											$placeName = str_replace($likeName, "", $info_place_Name); 
											//echo "/".$placeName;
											$nm_cnt = strlen($placeName);
											//echo "/".$nm_cnt;
											if($org_nm_cnt != $nm_cnt) {  
												// 길이가 차이난다는것은 해당 지점명에 통합좋아요지점명의 일부가 들어가 있음으로 판단하여 같은 지점이라고 처리한다.
												$info_like_Idx = $likeIdx;
												break;
											} else {   
												$last_iidx_query = "
													SELECT max(like_Idx) as like_Idx
													FROM TB_TOTAL_LIKE
													ORDER BY like_Idx ASC;
												";
												$last_iidx_stmt = $DB_con->prepare($last_iidx_query);
												$last_iidx_stmt->execute();
												$last_iidx_row=$last_iidx_stmt->fetch(PDO::FETCH_ASSOC);
												$llikeIdx = $last_iidx_row['like_Idx'];
												$info_like_Idx = (int)$llikeIdx + 1;
											} 
										}
									}else{
										$last_iidx_query = "
											SELECT max(like_Idx) as like_Idx
											FROM TB_TOTAL_LIKE
											ORDER BY like_Idx ASC;
										";
										$last_iidx_stmt = $DB_con->prepare($last_iidx_query);
										$last_iidx_stmt->execute();
										$last_iidx_row=$last_iidx_stmt->fetch(PDO::FETCH_ASSOC);
										$llikeIdx = $last_iidx_row['like_Idx'];
										$info_like_Idx = (int)$llikeIdx + 1;
									}
									$t_like_ins_query ="INSERT INTO TB_TOTAL_LIKE (like_Idx, lng, lat, con_Idx, place_Idx, place_Name, like_Cnt, reg_Id, reg_Date, like_reg_Date) VALUES (:like_Idx, :lng, :lat, :con_Idx, :place_Idx, :place_Name, :like_Cnt, :reg_Id, :reg_Date, NOW())";
									$t_like_ins_stmt = $DB_con->prepare($t_like_ins_query);
									$t_like_ins_stmt->bindParam(":like_Idx", $info_like_Idx);
									$t_like_ins_stmt->bindParam(":lng", $info_lng);
									$t_like_ins_stmt->bindParam(":lat", $info_lat);
									$t_like_ins_stmt->bindParam(":con_Idx", $info_con_Idx);
									$t_like_ins_stmt->bindParam(":place_Idx", $info_place_Idx);
									$t_like_ins_stmt->bindParam(":place_Name", $info_place_Name);
									$t_like_ins_stmt->bindParam(":like_Cnt", $info_like_Cnt);
									$t_like_ins_stmt->bindParam(":reg_Id", $info_reg_Id);
									$t_like_ins_stmt->bindParam(":reg_Date", $info_reg_Date);
									$t_like_ins_stmt->execute();

									//통합좋아요 그룹코드 등록하기
									$p_up_query ="
										UPDATE TB_PLACE 
										SET tLike_Idx = :tLike_Idx
										WHERE member_Idx = :member_Idx 
											AND idx = :place_Idx LIMIT 1;";
									$p_up_stmt = $DB_con->prepare($p_up_query);
									$p_up_stmt->bindParam(":member_Idx", $member_Idx);
									$p_up_stmt->bindparam(":place_Idx",$place_Idx);
									$p_up_stmt->bindParam(":tLike_Idx", $info_like_Idx);
									$p_up_stmt->execute();
								}else{
									// 통합좋아요에 등록이 되어 있음.
									$t_like_chk_query = "
										SELECT idx, like_Idx, place_Name, con_Idx, place_Idx
										FROM TB_TOTAL_LIKE
										WHERE ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) ) < 0.1;
									";
									$t_like_chk_stmt = $DB_con->prepare($t_like_chk_query);
									$t_like_chk_stmt->bindParam(":locatLat", $info_lat);
									$t_like_chk_stmt->bindParam(":locatLng", $info_lng);
									$t_like_chk_stmt->execute();
									$t_like_chk_num = $t_like_chk_stmt->rowCount();
									if($t_like_chk_num > 0){
										while($t_like_chk_row=$t_like_chk_stmt->fetch(PDO::FETCH_ASSOC)){
											$idx = $t_like_chk_row['idx'];
											$likeIdx = $t_like_chk_row['like_Idx'];
											$likeName = $t_like_chk_row['place_Name'];
											$conIdx = $t_like_chk_row['con_Idx'];
											$placeIdx = $t_like_chk_row['place_Idx'];
											//echo "/".$info_place_Name;
											$org_nm_cnt = strlen($info_place_Name);
											//echo "/".$org_nm_cnt;
											$placeName = str_replace($likeName, "", $info_place_Name); 
											//echo "/".$placeName;
											$nm_cnt = strlen($placeName);
											//echo "/".$nm_cnt;
											if($org_nm_cnt != $nm_cnt) {   
												// 길이가 차이난다는것은 해당 지점명에 통합좋아요지점명의 일부가 들어가 있음으로 판단하여 같은 지점이라고 처리한다.
												$info_like_Idx = $likeIdx;
												break;
											} else {   
												$last_iidx_query = "
													SELECT max(like_Idx) as like_Idx
													FROM TB_TOTAL_LIKE
													ORDER BY like_Idx ASC;
												";
												$last_iidx_stmt = $DB_con->prepare($last_iidx_query);
												$last_iidx_stmt->execute();
												$last_iidx_row=$last_iidx_stmt->fetch(PDO::FETCH_ASSOC);
												$llikeIdx = $last_iidx_row['like_Idx'];
												$info_like_Idx = (int)$llikeIdx + 1;
											} 
										}
									}else{
										$last_iidx_query = "
											SELECT max(like_Idx) as like_Idx
											FROM TB_TOTAL_LIKE
											ORDER BY like_Idx ASC;
										";
										$last_iidx_stmt = $DB_con->prepare($last_iidx_query);
										$last_iidx_stmt->execute();
										$last_iidx_row=$last_iidx_stmt->fetch(PDO::FETCH_ASSOC);
										$llikeIdx = $last_iidx_row['like_Idx'];
										$info_like_Idx = (int)$llikeIdx + 1;
									}
									
									if($conIdx == $info_con_Idx && $placeIdx == $info_place_Idx){
										$upPquery ="UPDATE TB_TOTAL_LIKE SET like_Cnt = :like_Cnt WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx LIMIT 1;";
										$upPstmt = $DB_con->prepare($upPquery);
										$upPstmt->bindParam(":con_Idx", $info_con_Idx);
										$upPstmt->bindParam(":place_Idx", $info_place_Idx);
										$upPstmt->bindParam(":like_Cnt", $info_like_Cnt);
										$upPstmt->execute();
									}else{
										$t_like_ins_query ="INSERT INTO TB_TOTAL_LIKE (like_Idx, lng, lat, con_Idx, place_Idx, place_Name, like_Cnt, reg_Id, reg_Date, like_reg_Date) VALUES (:like_Idx, :lng, :lat, :con_Idx, :place_Idx, :place_Name, :like_Cnt, :reg_Id, :reg_Date, NOW())";
										$t_like_ins_stmt = $DB_con->prepare($t_like_ins_query);
										$t_like_ins_stmt->bindParam(":like_Idx", $info_like_Idx);
										$t_like_ins_stmt->bindParam(":lng", $info_lng);
										$t_like_ins_stmt->bindParam(":lat", $info_lat);
										$t_like_ins_stmt->bindParam(":con_Idx", $info_con_Idx);
										$t_like_ins_stmt->bindParam(":place_Idx", $info_place_Idx);
										$t_like_ins_stmt->bindParam(":place_Name", $info_place_Name);
										$t_like_ins_stmt->bindParam(":like_Cnt", $info_like_Cnt);
										$t_like_ins_stmt->bindParam(":reg_Id", $info_reg_Id);
										$t_like_ins_stmt->bindParam(":reg_Date", $info_reg_Date);
										$t_like_ins_stmt->execute();
									}
									//통합좋아요 그룹코드 등록하기
									$p_up_query ="
										UPDATE TB_PLACE 
										SET tLike_Idx = :tLike_Idx
										WHERE member_Idx = :member_Idx 
											AND idx = :place_Idx LIMIT 1;";
									$p_up_stmt = $DB_con->prepare($p_up_query);
									$p_up_stmt->bindParam(":member_Idx", $member_Idx);
									$p_up_stmt->bindparam(":place_Idx",$place_Idx);
									$p_up_stmt->bindParam(":tLike_Idx", $likeIdx);
									$p_up_stmt->execute();

								}
							}
						}
					}
					$history = "좋아요";
					$con_Id = contentsIdInfo($con_Idx);	//지도를 등록한 아이디
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_Date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, :reg_Date)";
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