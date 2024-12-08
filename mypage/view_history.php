<?
header('Content-Type: application/json; charset=UTF-8');
/*
* 프로그램				: 회원들의 히스토리내역 보여줌
* 페이지 설명			: 회원들의 히스토리내역 보여줌
* 파일명					: view_history.php
* 관련DB					: TB_HISTORY
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$login_Id = trim($memId);					//회원아이디
if (memChk($login_Id) == "0") {
	$login_Id = "GUEST";							//회원아이디가 없는 경우 비회원으로 처리
} else {
	$mIdx = memIdxInfo($login_Id);			// 회원고유번호
}
$mode = trim($mode);						//조회타입(0: 전체, 1: 내 활동, 2: 관심 유저 내역만)
if ($mode == "") {
	$mode = "0";
}
$query = "";
if ($login_Id != "") {

	$DB_con = db1();
	// 관심유저 수 조회하기
	$like_cnt_query = "
		SELECT COUNT(*) as cnt
		FROM TB_MEMBERS_INTEREST as a
		WHERE a.reg_Id = :reg_Id					
			AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
			AND a.use_Bit = 'Y';
		";

	$like_cnt_stmt = $DB_con->prepare($like_cnt_query);
	$like_cnt_stmt->bindParam(":reg_Id", $login_Id);
	$like_cnt_stmt->execute();
	$like_cnt_row = $like_cnt_stmt->fetch(PDO::FETCH_ASSOC);
	$like_cnt = $like_cnt_row['cnt'];
	if ($like_cnt == "") {
		$like_cnt = "0";
	}
	if ($mode == "1") {							//내 활동 만
		$query = "
			SELECT h.idx, h.con_Idx, h.place_Idx, h.mem_Id, h.history, h.reg_Id, h.reg_Date
			FROM TB_HISTORY as h
			WHERE 
                (((SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
					AND ((SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
					AND ((SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL)
					AND ((SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL))
				AND h.reg_Id = :reg_Id	
				AND h.history NOT IN ('지도삭제', '지점삭제')
			ORDER BY h.reg_Date DESC
			LIMIT 50;
			";

		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":reg_Id", $login_Id);
		$stmt->execute();
	} else if ($mode == "2") {					// 관심 유저 만
		// 관심유저 아이디 조회하기
		$like_query = "
			SELECT a.mem_Id
			FROM TB_MEMBERS_INTEREST as a
			WHERE a.reg_Id = :reg_Id
				AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
				AND a.use_Bit = 'Y';
			";

		$like_stmt = $DB_con->prepare($like_query);
		$like_stmt->bindParam(":reg_Id", $login_Id);
		$like_stmt->execute();
		$like_Id = "";
		$int = 1;
		while ($like_row = $like_stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($int == 1) {
				$like_Id = "'" . $like_row['mem_Id'] . "'";
			} else {
				$like_Id = $like_Id . ", '" . $like_row['mem_Id'] . "'";
			}
			$int++;
		}
		if ($like_Id != "") {
			$query = "
				SELECT h.idx, h.con_Idx, h.place_Idx, h.mem_Id, h.history, h.reg_Id, h.reg_Date
				FROM TB_HISTORY as h
				WHERE 
                    (((SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
						AND ((SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
						AND ((SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL)
						AND ((SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL))
					AND h.reg_Id IN (" . $like_Id . ")
					AND h.history NOT IN ('신고', '지점공개여부', '지점삭제', '지도삭제')
				ORDER BY h.reg_Date DESC
				LIMIT 50;
				";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":reg_Id", $login_Id);
			$stmt->execute();
		} else {
			$result = array("result" => "success", "like_cnt" => (string)$like_cnt, "msg" => "관심유저가 없습니다.");
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		}
	} else {
		// 관심유저 아이디 조회하기
		$like_query = "
			SELECT a.mem_Id
			FROM TB_MEMBERS_INTEREST as a
			WHERE a.reg_Id = :reg_Id
				AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
				AND a.use_Bit = 'Y';
			";

		$like_stmt = $DB_con->prepare($like_query);
		$like_stmt->bindParam(":reg_Id", $login_Id);
		$like_stmt->execute();
		$like_Id = "";
		$int = 1;
		while ($like_row = $like_stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($int == 1) {
				$like_Id = "'" . $like_row['mem_Id'] . "'";
			} else {
				$like_Id = $like_Id . ", '" . $like_row['mem_Id'] . "'";
			}
			$int++;
		}
		if ($like_Id != "") {
			$query = "
				SELECT h.idx, h.con_Idx, h.place_Idx, h.mem_Id, h.history, h.reg_Id, h.reg_Date
				FROM TB_HISTORY as h
				WHERE 
                    (((SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
						AND ((SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
						AND ((SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL)
						AND ((SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL))
					AND ((h.reg_Id = :reg_Id OR h.place_Idx IN (SELECT idx FROM TB_PLACE WHERE reg_Id = :reg_Id)) OR (h.reg_Id IN (" . $like_Id . ") AND h.history NOT IN ('신고', '지점공개여부', '지점삭제', '지도삭제')))
					AND h.history NOT IN ('지점삭제', '지도삭제')
				ORDER BY h.reg_Date DESC
				LIMIT 50;
				";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":reg_Id", $login_Id);
			$stmt->execute();
		} else {
			$query = "
				SELECT h.idx, h.con_Idx, h.place_Idx, h.mem_Id, h.history, h.reg_Id, h.reg_Date
				FROM TB_HISTORY as h
				WHERE 
                    (((SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT delete_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
						AND ((SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) = '0' OR (SELECT open_Bit FROM TB_PLACE WHERE idx = h.place_Idx) IS NULL)
						AND ((SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT delete_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL)
						AND ((SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) = '0' OR (SELECT open_Bit FROM TB_CONTENTS WHERE idx = h.con_Idx) IS NULL))
					AND h.reg_Id = :reg_Id
					AND h.history NOT IN ('지점삭제', '지도삭제')
					AND h.place_Idx IN (SELECT idx FROM TB_PLACE WHERE reg_Id = :reg_Id)
				ORDER BY h.reg_Date DESC
				LIMIT 50;
				";

			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":reg_Id", $login_Id);
			$stmt->execute();
		}
	}
	if ($query != "") {
		$data = [];
		$chknum = $stmt->rowCount();

		if ($chknum < 1) { // 등록된 히스토리가 없는 경우
			$result = array("result" => "success", "like_cnt" => (string)$like_cnt, "msg" => "등록된 히스토리가 없습니다.");
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		} else {  //등록된 히스토리가 있을 경우
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];																			// 히스토리 고유번호
				$con_Idx = $row['con_Idx'];																// 히스토리 지도고유번호

				if ($con_Idx == '') {
					$con_Idx = '';
					$con_Name = '';
					$con_Lv = '';
					$memo = '';
					$subs_bit = 'N';
				} else {
					$chk_con_query = "
						SELECT COUNT(*) as cnt
						FROM TB_CONTENTS 
						WHERE idx = :con_Idx
							AND open_Bit = '0'
							AND delete_Bit = '0'
					";
					$chk_con_stmt = $DB_con->prepare($chk_con_query);
					$chk_con_stmt->bindParam(":con_Idx", $con_Idx);
					$chk_con_stmt->execute();
					$chk_con_row = $chk_con_stmt->fetch(PDO::FETCH_ASSOC);
					$con_cnt = $chk_con_row['cnt'];

					if ($con_cnt < 1) {
						continue;
					} else {
						$con_Name = contentsNameInfo($con_Idx);
						if ($con_Name == '') {
							$con_Name = "";
						}
						$con_info_query = "
							SELECT con_Lv, memo
							FROM TB_CONTENTS
							WHERE idx = :idx
								AND delete_Bit = '0'
								AND open_Bit = '0'
						";
						$con_info_stmt = $DB_con->prepare($con_info_query);
						$con_info_stmt->bindParam(":idx", $con_Idx);
						$con_info_stmt->execute();
						$con_info_row = $con_info_stmt->fetch(PDO::FETCH_ASSOC);
						$con_Lv = $con_info_row['con_Lv'];
						if ($con_Lv == "") {
							$con_Lv = "";
						}
						$memo = $con_info_row['memo'];
						if ($memo == "") {
							$memo = "";
						}
						if ($mem_Id != 'GUEST') {
							$subs_query = "
								SELECT *
								FROM TB_MEMBERS_SUBSCRIBE
								WHERE mem_Id = :mem_Id
									AND con_Idx = :con_Idx
									AND member_Idx = :member_Idx
									AND use_Bit = 'Y'
								ORDER BY reg_Date DESC;
								";
							$subs_stmt = $DB_con->prepare($subs_query);
							$subs_stmt->bindParam(":mem_Id", $login_Id);
							$subs_stmt->bindParam(":member_Idx", $mIdx);
							$subs_stmt->bindParam(":con_Idx", $con_Idx);
							$subs_stmt->execute();
							$subs_num = $subs_stmt->rowCount();
							if ($subs_num < 1) {
								$subs_bit = 'N';
							} else {
								$subs_bit = 'Y';
							}
						} else {
							$subs_bit = 'N';
						}
					}
				}
				$place_Idx = $row['place_Idx'];															// 히스토리 지점고유번호
				if ($place_Idx != "") {
					$chk_place_query = "
						SELECT COUNT(*) as cnt
						FROM TB_PLACE 
						WHERE idx = :place_Idx
							AND open_Bit = '0'
							AND delete_Bit = '0'
					";
					$chk_place_stmt = $DB_con->prepare($chk_place_query);
					$chk_place_stmt->bindParam(":place_Idx", $place_Idx);
					$chk_place_stmt->execute();
					$chk_place_row = $chk_place_stmt->fetch(PDO::FETCH_ASSOC);
					$place_cnt = $chk_place_row['cnt'];
					if ($place_cnt < 1) {
						continue;
					} else {
						if ($place_Idx == '') {
							$place_Idx = '';
							$place_Name = '';
						} else {
							$place_Name = placeNameInfo($place_Idx);
						}
					}
				} else {
					$place_Idx = '';
					$place_Name = '';
				}
				$reg_Id = $row['reg_Id'];																	// 히스토리 등록자
				$mem_Id = $row['mem_Id'];															// 히스토리 아이디
				$history = $row['history'];																// 히스토리내용
				$history_con = historyInfo($idx, $login_Id, $reg_Id, $history);								// 히스토리정리
				if ($history_con == '') {
					$history_con = "";
				}
				$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
				if ($reg_Nname == '') {
					$reg_Nname = "";
				}
				$member_Reg_Img = memImgInfo($reg_Id);											// 회원이미지
				if ($member_Reg_Img == '') {
					$member_Reg_Img = "";
				}
				$mem_Nname = memNickInfo($mem_Id);				// 회원닉네임
				if ($reg_Nname == '') {
					$reg_Nname = "";
				}
				$member_Img = memImgInfo($mem_Id);											// 회원이미지
				if ($member_Img == '') {
					$member_Img = "";
				}
				$reg_Date = $row['reg_Date'];															// 히스토리등록일

				$result = ["idx" => $idx, "mem_Id" => $mem_Id, "mem_Name" => $mem_Nname, "member_Img" => $member_Img, "con_Idx" => $con_Idx, "con_Lv" => $con_Lv, "con_Name" => $con_Name, "memo" => $memo, "subs_bit" => $subs_bit, "place_Idx" => $place_Idx, "place_Name" => $place_Name, "history" => $history_con, "history_Type" => (string)$history, "reg_Id" => $reg_Id, "reg_Name" => $reg_Nname, "member_Reg_Img" => $member_Reg_Img, "reg_Date" => $reg_Date];
				array_push($data, $result);
			}
			$chkData = [];
			$chkData["result"] = "success";
			$chkData["like_cnt"] = (string)$like_cnt;
			$chkData["history"] = $data;
			$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
			echo urldecode($output);
		}
		dbClose($DB_con);
		$stmt = null;
	}
} else { //빈값일 경우
	$result = array("result" => "error");
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
