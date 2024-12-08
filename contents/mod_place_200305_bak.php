<?
/*
* 프로그램				: 지점정보를 수정하는 기능
* 페이지 설명			: 지점위치, 지점대표아이콘, 지점명, 지점설명을 변경할 수 있다.
* 파일명					: mod_place.php
* 관련DB					: TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$place_Idx = trim($placeIdx);						//지점고유번호
$place_Name = trim($placeName);				//지점명
if($place_Name == ""){
	$place_Name = "";
}
$place_Icon = trim($placeIcon);					//지점대표아이콘
if($place_Icon == ""){
	$place_Icon = "";
}
$memo = trim($memo);								//상세설명
if($memo == ""){
	$memo = "";
}
$lng = trim($lng);										//경도
if($lng == ""){
	$lng = "";
}
$lat = trim($lat);										//위도
if($lat == ""){
	$lat = "";
}
$mode = trim($mode);
if($mode == ""){
	$mode = "";
}
$now_time = time();										// 추후 파일 디렉토리가 될 예정
//좌표 지정
/*
$lng = "126.921804310000000";

$lat = "37.554260160000000";
*/
$DB_con = db1();

// 지점고유번호를 확인한다.
$chk_query = "
		SELECT count(idx) as cnt
		FROM TB_PLACE
		WHERE idx = :place_Idx 
			AND delete_Bit = '0';
	";
$chk_stmt = $DB_con->prepare($chk_query);
$chk_stmt->bindParam(":place_Idx", $place_Idx);
$chk_stmt->execute();
$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
$chk_Cnt = $chk_row['cnt'];
if($chk_Cnt > 0){					//지점이 있는 경우
	if($mode == "0"){				//삭제처리
		$p_query = "
			SELECT idx, con_Idx, member_Idx, img, reg_Id
			FROM TB_PLACE
			WHERE idx = :place_Idx
				AND delete_Bit = '0';
		";
		$p_stmt = $DB_con->prepare($p_query);
		$p_stmt->bindParam(":place_Idx", $place_Idx);
		$p_stmt->execute();
		$p_row = $p_stmt->fetch(PDO::FETCH_ASSOC);
		$placeIdx = $p_row['idx'];						// 지점고유번호
		$conIdx = $p_row['con_Idx'];					// 지도고유번호
		$memberIdx = $p_row['member_Idx'];		// 회원고유번호
		$placeImg = $p_row['img'];						// 지점사진폴더
		$reg_Id = $p_row['reg_Id'];						// 지점사진폴더
		// 삭제처리 => 좋아요, 통합좋아요, 담기, 댓글, 지점이미지폴더삭제, 히스토리(좋아요, 담기, 댓글, 사진업데이트, 지점등록)
		// 6. 히스토리 내역 삭제
		$history_del_query = "
			SELECT idx
			FROM TB_HISTORY
			WHERE place_Idx = :place_Idx
				AND history IN ('지점등록', '사진업데이트', '지점공개여부')
				OR idx IN (member_Idx = :member_Idx AND place_Idx = :place_Idx AND history = '댓글등록');
		";
		$history_del_stmt = $DB_con->prepare($history_del_query);
		$history_del_stmt->bindParam(":place_Idx", $placeIdx);
		$history_del_stmt->bindParam(":member_Idx", $memberIdx);
		$history_del_stmt->execute();
		$history_del_num = $history_del_stmt->rowCount();
		if($history_del_num > 0){
			while($history_del_row = $history_del_stmt->fetch(PDO::FETCH_ASSOC)){
				$idx = $history_del_row['idx'];						// 지점고유번호
				$del_history_query = "
					DELETE FROM TB_HISTORY
					WHERE idx = :idx
					LIMIT 1;
				";
				$del_history_stmt = $DB_con->prepare($del_history_query);
				$del_history_stmt->bindParam(":idx", $idx);
				$del_history_stmt->execute();
			}
		}
		// 7. 지점삭제
		$share_chk_query = "
			SELECT count(idx) as cnt
			FROM TB_MEMBERS_SHARE
			WHERE place_Idx = :place_Idx
				AND use_Bit = 'Y';
		";
		$share_chk_stmt = $DB_con->prepare($share_chk_query);
		$share_chk_stmt->bindParam(":place_Idx", $placeIdx);
		$share_chk_stmt->execute();
		$share_chk_row = $share_chk_stmt->fetch(PDO::FETCH_ASSOC);
		$share_chk_num = $share_chk_row['cnt'];						// 담긴수
		if($share_chk_num > 0){		// 다른사람이 담기를 한 경우는 본인 댓글, 지점관련히스토리, 지점DB삭제가아닌 BIT값 수정
			$del_place_query = "
				UPDATE TB_PLACE
				SET delete_Bit = '1'
				WHERE idx = :idx
					AND member_Idx = :member_Idx
				LIMIT 1;
			";
			$del_place_stmt = $DB_con->prepare($del_place_query);
			$del_place_stmt->bindParam(":idx", $placeIdx);
			$del_place_stmt->bindParam(":member_Idx", $memberIdx);
			$del_place_stmt->execute();
			// 4. 댓글 내역 삭제
			$comment_del_query = "
				SELECT idx
				FROM TB_MEMBERS_COMMENT
				WHERE con_Idx = :con_Idx
					AND place_Idx = :place_Idx
					AND member_Idx = :member_Idx;
			";
			$comment_del_stmt = $DB_con->prepare($comment_del_query);
			$comment_del_stmt->bindParam(":con_Idx", $conIdx);
			$comment_del_stmt->bindParam(":place_Idx", $placeIdx);
			$comment_del_stmt->bindParam(":member_Idx", $memberIdx);
			$comment_del_stmt->execute();
			$comment_del_num = $comment_del_stmt->rowCount();
			if($comment_del_num > 0){
				while($comment_del_row = $comment_del_stmt->fetch(PDO::FETCH_ASSOC)){
					$idx = $comment_del_row['idx'];						// 지점고유번호
					$del_comment_query = "
						DELETE FROM TB_MEMBERS_COMMENT
						WHERE idx = :idx
						LIMIT 1;
					";
					$del_comment_stmt = $DB_con->prepare($del_comment_query);
					$del_comment_stmt->bindParam(":idx", $idx);
					$del_comment_stmt->execute();
				}
			}

			$history = "지점삭제";
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $memberIdx);
			$his_stmt->bindParam(":mem_Id", $reg_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":con_Idx", $conIdx);
			$his_stmt->bindParam(":place_Idx", $placeIdx);
			$his_stmt->bindParam(":reg_Id", $reg_Id);
			$his_stmt->execute();
		}else{		// 다른사람이 담기를 안한 경우는 전부 삭제
			// 1. 좋아요 내역 삭제
			$like_del_query = "
				SELECT idx
				FROM TB_MEMBERS_LIKE
				WHERE con_Idx = :con_Idx
					AND place_Idx = :place_Idx;
			";
			$like_del_stmt = $DB_con->prepare($like_del_query);
			$like_del_stmt->bindParam(":con_Idx", $conIdx);
			$like_del_stmt->bindParam(":place_Idx", $placeIdx);
			$like_del_stmt->execute();
			$like_del_num = $like_del_stmt->rowCount();
			if($like_del_num > 0){
				while($like_del_row = $like_del_stmt->fetch(PDO::FETCH_ASSOC)){
					$idx = $like_del_row['idx'];						// 좋아요 고유번호
					$del_like_query = "
						DELETE FROM TB_MEMBERS_LIKE
						WHERE idx = :idx
						LIMIT 1;
					";
					$del_like_stmt = $DB_con->prepare($del_like_query);
					$del_like_stmt->bindParam(":idx", $idx);
					$del_like_stmt->execute();
				}
			}
			// 2. 통합좋아요 내역 삭제
			$tlike_del_query = "
				SELECT idx
				FROM TB_TOTAL_LIKE
				WHERE con_Idx = :con_Idx
					AND place_Idx = :place_Idx;
			";
			$tlike_del_stmt = $DB_con->prepare($tlike_del_query);
			$tlike_del_stmt->bindParam(":con_Idx", $conIdx);
			$tlike_del_stmt->bindParam(":place_Idx", $placeIdx);
			$tlike_del_stmt->execute();
			$tlike_del_num = $tlike_del_stmt->rowCount();
			if($tlike_del_num > 0){
				while($tlike_del_row = $tlike_del_stmt->fetch(PDO::FETCH_ASSOC)){
					$idx = $tlike_del_row['idx'];						// 통합좋아요 고유번호
					$del_tlike_query = "
						DELETE FROM TB_TOTAL_LIKE
						WHERE idx = :idx
						LIMIT 1;
					";
					$del_tlike_stmt = $DB_con->prepare($del_tlike_query);
					$del_tlike_stmt->bindParam(":idx", $idx);
					$del_tlike_stmt->execute();
				}
			}
			// 4. 댓글 내역 삭제
			$comment_del_query = "
				SELECT idx
				FROM TB_MEMBERS_COMMENT
				WHERE con_Idx = :con_Idx
					AND place_Idx = :place_Idx;
			";
			$comment_del_stmt = $DB_con->prepare($comment_del_query);
			$comment_del_stmt->bindParam(":con_Idx", $conIdx);
			$comment_del_stmt->bindParam(":place_Idx", $placeIdx);
			$comment_del_stmt->execute();
			$comment_del_num = $comment_del_stmt->rowCount();
			if($comment_del_num > 0){
				while($comment_del_row = $comment_del_stmt->fetch(PDO::FETCH_ASSOC)){
					$idx = $comment_del_row['idx'];						// 지점고유번호
					$del_comment_query = "
						DELETE FROM TB_MEMBERS_COMMENT
						WHERE idx = :idx
						LIMIT 1;
					";
					$del_comment_stmt = $DB_con->prepare($del_comment_query);
					$del_comment_stmt->bindParam(":idx", $idx);
					$del_comment_stmt->execute();
				}
			}
			$del_place_query = "
				DELETE FROM TB_PLACE
				WHERE idx = :idx
					AND member_Idx = :member_Idx
				LIMIT 1;
			";
			$del_place_stmt = $DB_con->prepare($del_place_query);
			$del_place_stmt->bindParam(":idx", $placeIdx);
			$del_place_stmt->bindParam(":member_Idx", $memberIdx);
			$del_place_stmt->execute();
			// 5. 지점이미지폴더삭제
			if($placeImg != ""){
				$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$placeImg;
				if(is_dir($file_dir)){
					function delete_all($file_dir) {
						$d = @dir($file_dir);
						while ($entry = $d->read()) {
							if ($entry == "." || $entry == "..") continue;
							if (is_dir($entry)) delete_all($entry);
							else unlink($file_dir."/".$entry);
						}
					 
						// 해당디렉토리도 삭제할 경우에는 아래 주석처리를 해제합니다.
						rmdir($file_dir);
					}
					delete_all($file_dir);
				}
			}
			$history = "지점삭제";
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $memberIdx);
			$his_stmt->bindParam(":mem_Id", $reg_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":con_Idx", $conIdx);
			$his_stmt->bindParam(":place_Idx", $placeIdx);
			$his_stmt->bindParam(":reg_Id", $reg_Id);
			$his_stmt->execute();
		}
	}else if($mode == "1"){		//공개처리
		$chk_p_query = "
			SELECT count(idx) as cnt
			FROM TB_PLACE
			WHERE idx = :place_Idx
		";
		$chk_p_stmt = $DB_con->prepare($chk_p_query);
		$chk_p_stmt->bindParam(":place_Idx", $place_Idx);
		$chk_p_stmt->execute();
		$chk_p_row = $chk_p_stmt->fetch(PDO::FETCH_ASSOC);
		$chk_p_Cnt = $chk_p_row['cnt'];
		if($chk_p_Cnt > 0){					//지점이 있는 경우
			$p_query = "
				SELECT idx, con_Idx, member_Idx, reg_Id
				FROM TB_PLACE
				WHERE idx = :place_Idx
			";
			$p_stmt = $DB_con->prepare($p_query);
			$p_stmt->bindParam(":place_Idx", $place_Idx);
			$p_stmt->execute();
			$p_row = $p_stmt->fetch(PDO::FETCH_ASSOC);
			$placeIdx = $p_row['idx'];						// 지점고유번호
			$conIdx = $p_row['con_Idx'];					// 지도고유번호
			$memberIdx = $p_row['member_Idx'];		// 회원고유번호
			$reg_Id = $p_row['reg_Id'];		// 회원고유번호
			// 지점 공개처리 
			$open_place_query = "
				UPDATE TB_PLACE
				SET open_Bit = '0'
				WHERE idx = :idx
					AND member_Idx = :member_Idx
				LIMIT 1;
			";
			$open_place_stmt = $DB_con->prepare($open_place_query);
			$open_place_stmt->bindParam(":idx", $placeIdx);
			$open_place_stmt->bindParam(":member_Idx", $memberIdx);
			$open_place_stmt->execute();

			$his_Query = "SELECT idx FROM TB_HISTORY WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND history = '지점공개여부' AND reg_Id = :reg_Id; " ;
			$his_stmt = $DB_con->prepare($his_Query);
			$his_stmt->bindparam(":con_Idx",$conIdx);
			$his_stmt->bindparam(":place_Idx",$placeIdx);
			$his_stmt->bindparam(":reg_Id",$reg_Id);
			$his_stmt->execute();
			$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
			$his_idx = $his_row['idx'];
			$his_num = $his_stmt->rowCount();
			if($his_num > 0){
				$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
				$his_del_stmt = $DB_con->prepare($his_del_query);
				$his_del_stmt->bindParam(":idx", $his_idx);
				$his_del_stmt->execute();
			}
			$history = "지점공개여부";
			$con_Id = contentsIdInfo($conIdx);	//지도를 등록한 아이디
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $memberIdx);
			$his_stmt->bindParam(":mem_Id", $reg_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":con_Idx", $conIdx);
			$his_stmt->bindParam(":place_Idx", $placeIdx);
			$his_stmt->bindParam(":reg_Id", $reg_Id);
			$his_stmt->execute();
		}
	}else if($mode == "2"){		//비공개처리
		$chk_p_query = "
			SELECT count(idx) as cnt
			FROM TB_PLACE
			WHERE idx = :place_Idx
		";
		$chk_p_stmt = $DB_con->prepare($chk_p_query);
		$chk_p_stmt->bindParam(":place_Idx", $place_Idx);
		$chk_p_stmt->execute();
		$chk_p_row = $chk_p_stmt->fetch(PDO::FETCH_ASSOC);
		$chk_p_Cnt = $chk_p_row['cnt'];
		if($chk_p_Cnt > 0){					//지점이 있는 경우
			$p_query = "
				SELECT idx, con_Idx, member_Idx, reg_Id
				FROM TB_PLACE
				WHERE idx = :place_Idx
			";
			$p_stmt = $DB_con->prepare($p_query);
			$p_stmt->bindParam(":place_Idx", $place_Idx);
			$p_stmt->execute();
			$p_row = $p_stmt->fetch(PDO::FETCH_ASSOC);
			$placeIdx = $p_row['idx'];						// 지점고유번호
			$conIdx = $p_row['con_Idx'];					// 지도고유번호
			$memberIdx = $p_row['member_Idx'];		// 회원고유번호
			$reg_Id = $p_row['reg_Id'];		// 회원고유번호
			// 지점 비공개처리 
			$close_place_query = "
				UPDATE TB_PLACE
				SET open_Bit = '1'
				WHERE idx = :idx
					AND member_Idx = :member_Idx
				LIMIT 1;
			";
			$close_place_stmt = $DB_con->prepare($close_place_query);
			$close_place_stmt->bindParam(":idx", $placeIdx);
			$close_place_stmt->bindParam(":member_Idx", $memberIdx);
			$close_place_stmt->execute();

			$his_Query = "SELECT idx FROM TB_HISTORY WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND history = '지점공개여부' AND reg_Id = :reg_Id; " ;
			$his_stmt = $DB_con->prepare($his_Query);
			$his_stmt->bindparam(":con_Idx",$conIdx);
			$his_stmt->bindparam(":place_Idx",$placeIdx);
			$his_stmt->bindparam(":reg_Id",$reg_Id);
			$his_stmt->execute();
			$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
			$his_idx = $his_row['idx'];
			$his_num = $his_stmt->rowCount();
			if($his_num > 0){
				$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
				$his_del_stmt = $DB_con->prepare($his_del_query);
				$his_del_stmt->bindParam(":idx", $his_idx);
				$his_del_stmt->execute();
			}
			$history = "지점공개여부";
			$con_Id = contentsIdInfo($conIdx);	//지도를 등록한 아이디
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $memberIdx);
			$his_stmt->bindParam(":mem_Id", $reg_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":con_Idx", $conIdx);
			$his_stmt->bindParam(":place_Idx", $placeIdx);
			$his_stmt->bindParam(":reg_Id", $reg_Id);
			$his_stmt->execute();
		}
	}else{								//지점정보수정
		function search_Addr($url, $param=array()){
			$url = $url.'?'.http_build_query($param, '', '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$contents = curl_exec($ch); 
			$contents_json = json_decode($contents, true); // 결과값을 파싱
			curl_close($ch);
			return $contents_json;
		}
		function search_Addr2($url, $param=array()){
			$url = $url.'?'.http_build_query($param, '', '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$contents = curl_exec($ch); 
			$contents_json = json_decode($contents, true); // 결과값을 파싱
			curl_close($ch);
			return $contents_json;
		}
		$res = search_Addr('https://apis.openapi.sk.com/tmap/geo/reversegeocoding',array("version" => "1", "format" => "json", "callback" => "result", "coordType" => "WGS84GEO", "lon" => $lng,"lat" => $lat, "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
		$address = $res['addressInfo']['fullAddress'];
		if($address != ""){
			//$address = urlencode($address);
			//echo "-----------------------------------------------------";
					//주소검색 방법
					//NtoO : 새주소 -> 구주소 변환 검색
					//OtoN : 구주소(법정동) -> 새주소 변환 검색
			$res2 = search_Addr2('https://apis.openapi.sk.com/tmap/geo/convertAddress',array("version" => "1", "format" => "json", "callback" => "result", "searchTypCd" => "OtoN", "reqAdd" => $address, "resCoordType" => "WGS84GEO", "reqMulti" => "M", "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
			//print_r($res2);
			//echo $res2['ConvertAdd']['upperDistName'];
			$upperDistName = $res2['ConvertAdd']['upperDistName'];											// 시
			$middleDistName = $res2['ConvertAdd']['middleDistName'];										// 구
			$roadName = $res2['ConvertAdd']['newAddressList']['newAddress']['0']['roadName'];		// 도로명
			$bldNo1 = $res2['ConvertAdd']['newAddressList']['newAddress']['0']['bldNo1'];				// 번지
			$addr = $upperDistName." ".$middleDistName." ".$roadName." ".$bldNo1;
			if($upperDistName == ""){
				$addr = $address;
			}   
		}
		$query = "
			SELECT con_Idx, member_Idx, place_Name, place_Icon, memo, addr, lng, lat
			FROM TB_PLACE
			WHERE idx = :place_Idx
		";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":place_Idx", $place_Idx);
		$stmt->execute();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$con_Idx = $row['con_Idx'];
			$member_Idx = $row['member_Idx'];
			$org_place_Name = $row['place_Name'];
			if($place_Name == ""){
				$place_Name = $org_place_Name;
			}else{
				$place_Name = $place_Name;
			}
			$org_place_Icon = $row['place_Icon'];
			if($place_Icon == ""){
				$place_Icon = $org_place_Icon;
			}else{
				$place_Icon = $place_Icon;
			}
			$category_query = "
				SELECT code_Sub_Div
				FROM TB_CONFIG_CODE
				WHERE code = :code
					AND code_Div = 'placeicon'
			";
			$category_stmt = $DB_con->prepare($category_query);
			$category_stmt->bindParam(":code", $place_Icon);
			$category_stmt->execute();
			$category_row=$category_stmt->fetch(PDO::FETCH_ASSOC);
			$category = $category_row['code_Sub_Div'];

			$org_memo = $row['memo'];
			if($memo == ""){
				$memo = $org_memo;
			}else{
				$memo = $memo;
			}
			$org_lng = $row['lng'];
			if($lng == ""){
				$lng = $org_lng;
			}else{
				$lng = $lng;
			}
			$org_lat = $row['lat'];
			if($lat == ""){
				$lat = $org_lat;
			}else{
				$lat = $lat;
			}
			$org_addr = $row['addr'];
			if($addr == ""){
				$addr = $org_addr;
			}else{
				$addr = $addr;
			}
			$mod_query = "
				UPDATE TB_PLACE
				SET category = :category, 
					place_Name = :place_Name,
					place_Icon = :place_Icon,
					memo = :memo,
					lng = :lng,
					lat = :lat,
					addr = :addr,
					mod_Date = NOW()
				WHERE member_Idx = :member_Idx
					AND idx = :place_Idx
				LIMIT 1;
			";
			$mod_stmt = $DB_con->prepare($mod_query);
			$mod_stmt->bindParam(":category", $category);
			$mod_stmt->bindParam(":place_Name", $place_Name);
			$mod_stmt->bindParam(":place_Icon", $place_Icon);
			$mod_stmt->bindParam(":memo", $memo);
			$mod_stmt->bindParam(":lng", $lng);
			$mod_stmt->bindParam(":lat", $lat);
			$mod_stmt->bindParam(":addr", $addr);
			$mod_stmt->bindParam(":member_Idx", $member_Idx);
			$mod_stmt->bindParam(":place_Idx", $place_Idx);
			$mod_stmt->execute();
			
			if($con_Idx != ""){
				$mod_query2 = "
					UPDATE TB_CONTENTS
					SET mod_Date = NOW()
					WHERE idx = :con_Idx
						AND member_Idx = :member_Idx
				";
				$mod_stmt2 = $DB_con->prepare($mod_query2);
				$mod_stmt2->bindParam(":member_Idx", $member_Idx);
				$mod_stmt2->bindParam(":con_Idx", $con_Idx);
				$mod_stmt2->execute();
			}
		}
	}

    dbClose($DB_con);
	$chk_stmt = null;
    $stmt = null;
	$mod_stmt = null;
	$mod_stmt2 = null;
	$result = array("result" => "success");
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점고유번호오류");
}

echo json_encode($result);