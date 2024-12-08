<?
/*
* 프로그램				: 등록된 지점 목록을 보여줌
* 페이지 설명			: 등록된 지점 목록을 보여줌(지도에 표시하기 위함)
* 파일명					: view_place.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$con_Idx = trim($conIdx);						// 지도고유번호 
$con_chk_Bit = contentsChk($con_Idx);		// 지도고유번호 확인 
$con_reg_Id = contentsIdInfo($con_Idx);	// 지도등록아이디 확인
$mem_Id = trim($memId);						// 회원아이디
/*
echo "con_reg_Id : ".$con_reg_Id."\n";
echo "mem_Id : ".$mem_Id."\n";
*/
if($con_reg_Id != $mem_Id){
	$con_open_Bit =contentsOpenBit($con_Idx);
}
$locatLng = trim($lng);							// 경도
$locatLat = trim($lat);							// 위도
$mode =  trim($mode);							// 조회타입(1: 거리순, 2: 지역순)
$si = trim($si);										// 조회타입이 2(지역순) 인 경우 해당 시구분명칭
if($mode == ""){									// 빈값일 경우 1: 거리순 처리
	$mode = "1";
}
$DB_con = db1();
if($con_chk_Bit == "1") {						// 지점이 정상적으로 조회되는 경우에만 api 실행
	if($mode == "2") {								// 지역순으로 조회할 경우
		if($con_reg_Id != $mem_Id){
			$chk_con_query = "
				SELECT count(*) as cnt
				FROM TB_CONTENTS 
				WHERE idx = :idx
					AND open_Bit = '0'
				;
			";
			$chk_con_stmt = $DB_con->prepare($chk_con_query);
			$chk_con_stmt->bindParam(":idx", $con_Idx);
			$chk_con_stmt->execute();
			$chk_con_row=$chk_con_stmt->fetch(PDO::FETCH_ASSOC);
			$con_Cnt = $chk_con_row['cnt'];
			if($con_Cnt != "0"){
				// 지점수 확인
				$cquery = "
					SELECT count(idx) as cnt
					FROM TB_PLACE 
					WHERE (con_Idx = :con_Idx OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y'))
						AND open_Bit = '0'
						AND delete_Bit = '0'
						AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '') = :si
						;
					";
				$cstmt = $DB_con->prepare($cquery);
				$cstmt->bindParam(":con_Idx", $con_Idx);
				$cstmt->bindParam(":si", $si);
				$cstmt->execute();
				$crow = $cstmt->fetch(PDO::FETCH_ASSOC);
				$p_Cnt = $crow['cnt'];
				
				if ($p_Cnt == "") {
					$p_Cnt = 0;
				} else {
					$p_Cnt =  $p_Cnt ;
				}
				
				$rows = 10;  //페이지 갯수
				$total_page  = ceil($p_Cnt / $rows);  // 전체 페이지 계산
				if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
				$from_record = ($page - 1) * $rows; // 시작 열을 구함
				// 지점정보
				$query = "
					SELECT idx, 
						place_Name, 
						lng, 
						lat, 
						category, 
						share_Cnt, 
						REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '')
						as si, 
						addr
					FROM TB_PLACE 
					WHERE (con_Idx = :con_Idx OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y'))
						AND open_Bit = '0'
						AND delete_Bit = '0'
						AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '') = :si
					ORDER BY case 
						when si like '%서울%' OR si like '%서울특별시%' then 1
						when si like '%부산%' OR si like '%부산광역시%' then 2
						when si like '%대구%' OR si like '%대구광역시%' then 3
						when si like '%인천%' OR si like '%인천광역시%' then 4
						when si like '%광주%' OR si like '%광주광역시%' then 5
						when si like '%대전%' OR si like '%대전광역시%' then 6
						when si like '%울산%' OR si like '%울산광역시%' then 7
						when si like '%세종%' OR si like '%세종특별자치시%' then 8
						when si like '%경기%' OR si like '%경기도%' then 9
						when si like '%강원%' OR si like '%강원도%' then 10
						when si like '%충청북도%' OR si like '%충북%' then 11
						when si like '%충청남도%' OR si like '%충남%' then 12
						when si like '%전라북도%' OR si like '%전북%' then 13
						when si like '%전라남도%' OR si like '%전남%' then 14
						when si like '%경상북도%' OR si like '%경북%' then 15
						when si like '%경상남도%' OR si like '%경남%' then 16
						when si like '%제주특별자치도%' OR si like '%제주%' then 17
						else 99 end, 
						REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', 3), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '')
					LIMIT {$from_record}, {$rows}
						;
				";
				$stmt = $DB_con->prepare($query);
				$stmt->bindParam(":con_Idx", $con_Idx);
				$stmt->bindParam(":si", $si);
				$stmt->execute();
				$data = [];
				if($p_Cnt == 1){
					$chk_idx = 0;
					$chkidx = 0;
					$row=$stmt->fetch(PDO::FETCH_ASSOC);
					$idx = $row['idx'];											// 지점고유번호
					$place_Name = $row['place_Name'];					// 지점명
					$lng = $row['lng'];										// 경도
					$lat = $row['lat'];											// 위도
					$category = $row['category'];							// 카테고리
					$cate_query = "
						SELECT code_Name
						FROM TB_CONFIG_CODE 
						WHERE code = :code;
						";
					$cate_stmt = $DB_con->prepare($cate_query);
					$cate_stmt->bindParam(":code", $category);
					$cate_stmt->execute();
					$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
					$category_Name = $cate_row['code_Name'];		//카테고리명
					$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
					$si = $row['si'];											// 시
					$addr = $row['addr'];										// 주소
					$chk_addr =explode(" ", $addr);						// 주소를 잘라와서 부분으로 가져오기
					if(strpos($si, "경기도") !== false){
						$address = $chk_addr['2']." ".$chk_addr['3'];
					}else{
						$address = $chk_addr['1']." ".$chk_addr['2'];
					}
					$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "si" => $si, "addr" => $address];
					array_push($data, $mresult);

					$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
					$chkData = [];
					$chkData["result"] = "success";
					$chkData["lists_info"] = $listInfoResult;
					$chkData["lists"] = $data;
					$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);
				}else if($p_Cnt > 1){
					$chk_idx = 0;
					$chkidx = 0;
					while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
						$idx = $row['idx'];											// 지점고유번호
						$place_Name = $row['place_Name'];					// 지점명
						$lng = $row['lng'];										// 경도
						$lat = $row['lat'];											// 위도
						$category = $row['category'];							// 카테고리
						$cate_query = "
							SELECT code_Name
							FROM TB_CONFIG_CODE 
							WHERE code = :code;
							";
						$cate_stmt = $DB_con->prepare($cate_query);
						$cate_stmt->bindParam(":code", $category);
						$cate_stmt->execute();
						$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
						$category_Name = $cate_row['code_Name'];		// 카테고리명
						$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
						$si = $row['si'];											// 시
						$addr = $row['addr'];										// 주소
						$chk_addr =explode(' ' , $addr);						// 주소를 잘라와서 부분으로 가져오기
						if(strpos($si, "경기도") !== false){
							$address = $chk_addr['2']." ".$chk_addr['3'];
						}else{
							$address = $chk_addr['1']." ".$chk_addr['2'];
						}
						$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "si" => $si, "addr" => $address];
						array_push($data, $mresult);
					}
					$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
					$chkData = [];
					$chkData["result"] = "success";
					$chkData["lists_info"] = $listInfoResult;
					$chkData["lists"] = $data;
					$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);
				}else{
					$result = array("result" => "success", "pin_Cnt" => "0", "lists" =>["place_Idx" => "", "category" => "", "category_Name" => "", "place_Name" => "", "share_Cnt" => "", "lng" => "", "lat" => "", "si" => "", "addr" => ""]);
					echo json_encode($result, JSON_UNESCAPED_UNICODE); 
				}
			}else{
				$result = array("result" => "success", "errorMsg" => "비공개 지도입니다.");
				echo json_encode($result, JSON_UNESCAPED_UNICODE); 
			}
		}else{
			// 지점수 확인
			$cquery = "
				SELECT count(idx) as cnt
				FROM TB_PLACE 
				WHERE (con_Idx = :con_Idx OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y'))
					AND delete_Bit = '0'
					AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '') = :si
					;
				";
			$cstmt = $DB_con->prepare($cquery);
			$cstmt->bindParam(":con_Idx", $con_Idx);
			$cstmt->bindParam(":si", $si);
			$cstmt->execute();
			$crow = $cstmt->fetch(PDO::FETCH_ASSOC);
			$p_Cnt = $crow['cnt'];
			
			if ($p_Cnt == "") {
				$p_Cnt = 0;
			} else {
				$p_Cnt =  $p_Cnt ;
			}
			
			$rows = 10;  //페이지 갯수
			$total_page  = ceil($p_Cnt / $rows);  // 전체 페이지 계산
			if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
			$from_record = ($page - 1) * $rows; // 시작 열을 구함
			// 지점정보
			$query = "
				SELECT idx, 
					place_Name, 
					lng, 
					lat, 
					category, 
					share_Cnt, 
					REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '')
					as si, 
					addr
				FROM TB_PLACE 
				WHERE (con_Idx = :con_Idx OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y'))
					AND delete_Bit = '0'
					AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '') = :si
				ORDER BY case 
					when si like '%서울%' OR si like '%서울특별시%' then 1
					when si like '%부산%' OR si like '%부산광역시%' then 2
					when si like '%대구%' OR si like '%대구광역시%' then 3
					when si like '%인천%' OR si like '%인천광역시%' then 4
					when si like '%광주%' OR si like '%광주광역시%' then 5
					when si like '%대전%' OR si like '%대전광역시%' then 6
					when si like '%울산%' OR si like '%울산광역시%' then 7
					when si like '%세종%' OR si like '%세종특별자치시%' then 8
					when si like '%경기%' OR si like '%경기도%' then 9
					when si like '%강원%' OR si like '%강원도%' then 10
					when si like '%충청북도%' OR si like '%충북%' then 11
					when si like '%충청남도%' OR si like '%충남%' then 12
					when si like '%전라북도%' OR si like '%전북%' then 13
					when si like '%전라남도%' OR si like '%전남%' then 14
					when si like '%경상북도%' OR si like '%경북%' then 15
					when si like '%경상남도%' OR si like '%경남%' then 16
					when si like '%제주특별자치도%' OR si like '%제주%' then 17
					else 99 end, 
					REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', 3), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '')
				LIMIT {$from_record}, {$rows}
					;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->bindParam(":si", $si);
			$stmt->execute();
			$data = [];
			if($p_Cnt == 1){
				$chk_idx = 0;
				$chkidx = 0;
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				$idx = $row['idx'];											// 지점고유번호
				$place_Name = $row['place_Name'];					// 지점명
				$lng = $row['lng'];										// 경도
				$lat = $row['lat'];											// 위도
				$category = $row['category'];							// 카테고리
				$cate_query = "
					SELECT code_Name
					FROM TB_CONFIG_CODE 
					WHERE code = :code;
					";
				$cate_stmt = $DB_con->prepare($cate_query);
				$cate_stmt->bindParam(":code", $category);
				$cate_stmt->execute();
				$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
				$category_Name = $cate_row['code_Name'];		//카테고리명
				$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
				$si = $row['si'];											// 시
				$addr = $row['addr'];										// 주소
				$chk_addr =explode(" ", $addr);						// 주소를 잘라와서 부분으로 가져오기
				if(strpos($si, "경기도") !== false){
					$address = $chk_addr['2']." ".$chk_addr['3'];
				}else{
					$address = $chk_addr['1']." ".$chk_addr['2'];
				}
				$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "si" => $si, "addr" => $address];
				array_push($data, $mresult);

				$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
				$chkData = [];
				$chkData["result"] = "success";
				$chkData["lists_info"] = $listInfoResult;
				$chkData["lists"] = $data;
				$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
				echo  urldecode($output);
			}else if($p_Cnt > 1){
				$chk_idx = 0;
				$chkidx = 0;
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
					$idx = $row['idx'];											// 지점고유번호
					$place_Name = $row['place_Name'];					// 지점명
					$lng = $row['lng'];										// 경도
					$lat = $row['lat'];											// 위도
					$category = $row['category'];							// 카테고리
					$cate_query = "
						SELECT code_Name
						FROM TB_CONFIG_CODE 
						WHERE code = :code;
						";
					$cate_stmt = $DB_con->prepare($cate_query);
					$cate_stmt->bindParam(":code", $category);
					$cate_stmt->execute();
					$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
					$category_Name = $cate_row['code_Name'];		// 카테고리명
					$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
					$si = $row['si'];											// 시
					$addr = $row['addr'];										// 주소
					$chk_addr =explode(' ' , $addr);						// 주소를 잘라와서 부분으로 가져오기
					if(strpos($si, "경기도") !== false){
						$address = $chk_addr['2']." ".$chk_addr['3'];
					}else{
						$address = $chk_addr['1']." ".$chk_addr['2'];
					}
					$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "si" => $si, "addr" => $address];
					array_push($data, $mresult);
				}
				$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
				$chkData = [];
				$chkData["result"] = "success";
				$chkData["lists_info"] = $listInfoResult;
				$chkData["lists"] = $data;
				$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
				echo  urldecode($output);
			}else{
				$result = array("result" => "success", "pin_Cnt" => "0", "lists" =>["place_Idx" => "", "category" => "", "category_Name" => "", "place_Name" => "", "share_Cnt" => "", "lng" => "", "lat" => "", "si" => "", "addr" => ""]);
				echo json_encode($result, JSON_UNESCAPED_UNICODE); 
			}
		}
	}else{	
		if($con_reg_Id != $mem_Id){
			$chk_con_query = "
				SELECT count(*) as cnt
				FROM TB_CONTENTS 
				WHERE idx = :idx
					AND open_Bit = '0'
				;
			";
			$chk_con_stmt = $DB_con->prepare($chk_con_query);
			$chk_con_stmt->bindParam(":idx", $con_Idx);
			$chk_con_stmt->execute();
			$chk_con_row=$chk_con_stmt->fetch(PDO::FETCH_ASSOC);
			$con_Cnt = $chk_con_row['cnt'];
			if($con_Cnt != "0"){
				// 지점수 확인
				$cquery = "
					SELECT count(idx) as cnt
					FROM TB_PLACE 
					WHERE con_Idx = :con_Idx
						AND open_Bit = '0'
						AND delete_Bit = '0'
						OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y');
				";
				$cstmt = $DB_con->prepare($cquery);
				$cstmt->bindParam(":con_Idx", $con_Idx);
				$cstmt->execute();
				$crow = $cstmt->fetch(PDO::FETCH_ASSOC);
				$p_Cnt = $crow['cnt'];
				
				if ($p_Cnt == "") {
					$p_Cnt = 0;
				} else {
					$p_Cnt =  $p_Cnt ;
				}
				
				$rows = 15;  //페이지 갯수
				$total_page  = ceil($p_Cnt / $rows);  // 전체 페이지 계산
				if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
				$from_record = ($page - 1) * $rows; // 시작 열을 구함
				// 지점정보
				$query = "
					SELECT idx, place_Name, lng, lat, category, share_Cnt, ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) ) AS  distance
					FROM TB_PLACE 
					WHERE con_Idx = :con_Idx
						AND open_Bit = '0'
						AND delete_Bit = '0'
						OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
					ORDER BY ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) )
					LIMIT {$from_record}, {$rows};
					";
				$stmt = $DB_con->prepare($query);
				$stmt->bindParam(":con_Idx", $con_Idx);
				$stmt->bindParam(":locatLng", $locatLng);
				$stmt->bindParam(":locatLat", $locatLat);
				$stmt->execute();
				if($p_Cnt == 1){
					$data = [];
					$row=$stmt->fetch(PDO::FETCH_ASSOC);
					$idx = $row['idx'];											// 지점고유번호
					$place_Name = $row['place_Name'];					// 지점명
					$lng = $row['lng'];										// 경도
					$lat = $row['lat'];											// 위도
					$category = $row['category'];							// 카테고리
					$cate_query = "
						SELECT code_Name
						FROM TB_CONFIG_CODE 
						WHERE code = :code;
						";
					$cate_stmt = $DB_con->prepare($cate_query);
					$cate_stmt->bindParam(":code", $category);
					$cate_stmt->execute();
					$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
					$category_Name = $cate_row['code_Name'];		//카테고리명
					$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
					$distance = $row['distance'];							// 거리
					$distance = round($distance, 1);						// 거리 소수첫째 자리까지 반올림
					$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "distance" => (string)$distance];
					 array_push($data, $mresult);

					$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
					$chkData = [];
					$chkData["result"] = "success";
					$chkData["lists_info"] = $listInfoResult;
					$chkData["lists"] = $data;
					$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);
				}else if($p_Cnt > 1){
					$data = [];
					while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
						$idx = $row['idx'];											// 지점고유번호
						$place_Name = $row['place_Name'];					// 지점명
						$lng = $row['lng'];										// 경도
						$lat = $row['lat'];											// 위도
						$category = $row['category'];							// 카테고리
						$cate_query = "
							SELECT code_Name
							FROM TB_CONFIG_CODE 
							WHERE code = :code;
							";
						$cate_stmt = $DB_con->prepare($cate_query);
						$cate_stmt->bindParam(":code", $category);
						$cate_stmt->execute();
						$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
						$category_Name = $cate_row['code_Name'];		// 카테고리명
						$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
						$distance = $row['distance'];							// 거리
						$distance = round($distance, 1);						// 거리 소수첫째 자리까지 반올림
						$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "distance" => (string)$distance];
						 array_push($data, $mresult);
					}
					$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
					$chkData = [];
					$chkData["result"] = "success";
					$chkData["lists_info"] = $listInfoResult;
					$chkData["lists"] = $data;
					$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);
				}else{
					$result = array("result" => "success", "pin_Cnt" => ["totCnt" => "0", "page" => "0"], "lists" =>["place_Idx" => "", "category" => "", "category_Name" => "", "place_Name" => "", "share_Cnt" => "", "lng" => "", "lat" => "", "distance" => "0"]);
					echo json_encode($result, JSON_UNESCAPED_UNICODE); 
				}
			}else{
				$result = array("result" => "success", "errorMsg" => "비공개 지도입니다.");
				echo json_encode($result, JSON_UNESCAPED_UNICODE); 
			}
		}else{
			// 지점수 확인
			$cquery = "
				SELECT count(idx) as cnt
				FROM TB_PLACE 
				WHERE con_Idx = :con_Idx
					AND delete_Bit = '0'
					OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y');
				";
			$cstmt = $DB_con->prepare($cquery);
			$cstmt->bindParam(":con_Idx", $con_Idx);
			$cstmt->execute();
			$crow = $cstmt->fetch(PDO::FETCH_ASSOC);
			$p_Cnt = $crow['cnt'];
			
			if ($p_Cnt == "") {
				$p_Cnt = 0;
			} else {
				$p_Cnt =  $p_Cnt ;
			}
			
			$rows = 15;  //페이지 갯수
			$total_page  = ceil($p_Cnt / $rows);  // 전체 페이지 계산
			if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
			$from_record = ($page - 1) * $rows; // 시작 열을 구함
			if($con_reg_Id != $mem_Id){
				// 지점정보
				$query = "
					SELECT idx, place_Name, lng, lat, category, share_Cnt, ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) ) AS  distance
					FROM TB_PLACE 
					WHERE con_Idx = :con_Idx
						AND open_Bit = '0'
						AND delete_Bit = '0'
						OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
					ORDER BY ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) )
					LIMIT {$from_record}, {$rows};
					";
			}else{
				// 지점정보
				$query = "
					SELECT idx, place_Name, lng, lat, category, share_Cnt, ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) ) AS  distance
					FROM TB_PLACE 
					WHERE con_Idx = :con_Idx
						AND delete_Bit = '0'
						OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
					ORDER BY ( 6371 * acos( cos( radians(:locatLat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:locatLng) ) + sin( radians(:locatLat) ) * sin( radians( lat ) ) ) )
					LIMIT {$from_record}, {$rows};
					";
			}
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->bindParam(":locatLng", $locatLng);
			$stmt->bindParam(":locatLat", $locatLat);
			$stmt->execute();
			if($p_Cnt == 1){
				$data = [];
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				$idx = $row['idx'];											// 지점고유번호
				$place_Name = $row['place_Name'];					// 지점명
				$lng = $row['lng'];										// 경도
				$lat = $row['lat'];											// 위도
				$category = $row['category'];							// 카테고리
				$cate_query = "
					SELECT code_Name
					FROM TB_CONFIG_CODE 
					WHERE code = :code;
					";
				$cate_stmt = $DB_con->prepare($cate_query);
				$cate_stmt->bindParam(":code", $category);
				$cate_stmt->execute();
				$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
				$category_Name = $cate_row['code_Name'];		//카테고리명
				$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
				$distance = $row['distance'];							// 거리
				$distance = round($distance, 1);						// 거리 소수첫째 자리까지 반올림
				$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "distance" => (string)$distance];
				 array_push($data, $mresult);

				$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
				$chkData = [];
				$chkData["result"] = "success";
				$chkData["lists_info"] = $listInfoResult;
				$chkData["lists"] = $data;
				$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
				echo  urldecode($output);
			}else if($p_Cnt > 1){
				$data = [];
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
					$idx = $row['idx'];											// 지점고유번호
					$place_Name = $row['place_Name'];					// 지점명
					$lng = $row['lng'];										// 경도
					$lat = $row['lat'];											// 위도
					$category = $row['category'];							// 카테고리
					$cate_query = "
						SELECT code_Name
						FROM TB_CONFIG_CODE 
						WHERE code = :code;
						";
					$cate_stmt = $DB_con->prepare($cate_query);
					$cate_stmt->bindParam(":code", $category);
					$cate_stmt->execute();
					$cate_row=$cate_stmt->fetch(PDO::FETCH_ASSOC);
					$category_Name = $cate_row['code_Name'];		// 카테고리명
					$share_Cnt = $row['share_Cnt'];						// 퍼가기 수
					$distance = $row['distance'];							// 거리
					$distance = round($distance, 1);						// 거리 소수첫째 자리까지 반올림
					$mresult = ["place_Idx" => $idx, "category" => $category, "category_Name" => $category_Name, "place_Name" => $place_Name, "share_Cnt" => (string)$share_Cnt, "lng" => $lng, "lat" => $lat, "distance" => (string)$distance];
					 array_push($data, $mresult);
				}
				$listInfoResult = array("totCnt" => (string)$p_Cnt, "page" => (string)$page);
				$chkData = [];
				$chkData["result"] = "success";
				$chkData["lists_info"] = $listInfoResult;
				$chkData["lists"] = $data;
				$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
				echo  urldecode($output);
			}else{
				$result = array("result" => "success", "pin_Cnt" => ["totCnt" => "0", "page" => "0"], "lists" =>["place_Idx" => "", "category" => "", "category_Name" => "", "place_Name" => "", "share_Cnt" => "", "lng" => "", "lat" => "", "distance" => "0"]);
				echo json_encode($result, JSON_UNESCAPED_UNICODE); 
			}
		}
	}
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "등록된 지도가 없음");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



