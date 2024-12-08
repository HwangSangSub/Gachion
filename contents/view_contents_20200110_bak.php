<?
/*
* 프로그램				: 지도리스트
* 페이지 설명			: 지도리스트를 조회할 수 있다.
* 파일명					: view_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$category = trim($category);					// 검색어
$search = trim($search);							// 검색어
$mem_Id = trim($memId);						// 로그인한 회원 아이디
if($mem_Id == ''){
	$mem_Id = 'GUEST';							// 비회원 인 경우 GUEST
}
//$reg_Date = DU_TIME_YMDHIS;		//등록일


if ($category != "") {
    
    $DB_con = db1();
	$data = [];
	if($search != ""){	//검색어가 있을 경우 검색값 추가
		//지도 기본테이블 조회
		$query = "
			SELECT idx, con_Name, con_Lv, category, img, tag, like_Cnt, kml_File, memo, reg_Id, reg_Date, end_Date
			FROM TB_CONTENTS
			WHERE category in (:category)
				AND con_Name like '%".$search."%'
			ORDER BY reg_Date DESC;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":category", $category);
		$stmt->execute();
	}else{
		//지도 기본테이블 조회
		$query = "
			SELECT idx, con_Name, con_Lv, category, img, tag, like_Cnt, kml_File, memo, reg_Id, reg_Date, end_Date
			FROM TB_CONTENTS
			WHERE category in (:category)
			ORDER BY reg_Date DESC;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":category", $category);
		$stmt->execute();
	}

	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$idx = $row['idx'];											// 지도고유번호
		// 지점수 확인
		$cnt_query = "
			SELECT count(idx) as cnt
			FROM TB_PLACE
			WHERE con_Idx = :con_Idx
			ORDER BY reg_Date DESC;
			";
		$cnt_stmt = $DB_con->prepare($cnt_query);
		$cnt_stmt->bindParam(":con_Idx", $idx);
		$cnt_stmt->execute();
		$cnt_row=$cnt_stmt->fetch(PDO::FETCH_ASSOC);
		$pin_Cnt = $cnt_row['cnt'];								// 지도내 지점 갯수
		if($mem_Id != 'GUEST'){
			$subs_query = "
				SELECT *
				FROM TB_MEMBERS_SUBSCRIBE
				WHERE mem_Id = :mem_Id
					AND con_Idx = :con_Idx
				ORDER BY reg_Date DESC;
				";
			$subs_stmt = $DB_con->prepare($subs_query);
			$subs_stmt->bindParam(":mem_Id", $mem_Id);
			$subs_stmt->bindParam(":con_Idx", $idx);
			$subs_stmt->execute();
			$subs_num = $subs_stmt->rowCount();
			if($subs_num < 1){
				$subs_bit = 'N';
			}else{
				$subs_bit = 'Y';
			}
		}else{
			$subs_bit = 'N';
		}
		$con_Name = $row['con_Name'];						// 지도명
		$con_Lv = $row['con_Lv'];								// 지도등급 (0: 본사, 1: 일반회원, 2: 제휴회원)
		$category = $row['category'];							// 카테고리
		$img_num = 0;
		// 이미지확인
		$img_query = "
			SELECT img as pimg
			FROM TB_PLACE
			WHERE con_Idx = :con_Idx
			ORDER BY reg_Date DESC
			LIMIT 4;
			";
		$img_stmt = $DB_con->prepare($img_query);
		$img_stmt->bindParam(":con_Idx", $idx);
		$img_stmt->execute();
		$img_num = $img_stmt->rowCount();
		$p_Img = [];
		while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)) {
			$pimg = $img_row['pimg'];							// 지점에 등록된 이미지
			if($pimg == ''){
				continue;
			}
			$p_locat_Img = "https://places.gachita.kr/contents/place_img/".$pimg;
			 array_push($p_Img, $p_locat_Img);
		}
		if($img_num > 1){
			$img = $p_Img;
			$img_Bit = $img_num;										// 이미지타입(1: 썸네일이미지, 2: 지도이미지, 3: 사진4개이상)
		}else{
			$img_Name = $row['img'];							// 이미지
			$img = "https://places.gachita.kr/contents/place_img/".$img_Name;
			if($img == ""){											// 이미지가 없을 경우 
				$img = "";					
				$img_Bit = "0";										// 이미지타입 빈값
			}else{
				if($con_Lv == "1"){								// 일반회원 인 경우 사진 3개 이하면 지도이미지를 표시
					$img_Bit = $img_num;		
					$img = "";							
				}else{
					$img_Bit = $img_num;	
				}
			}
		}
		
		$tag = $row['tag'];										// 지점 태그
		if($tag == ''){
			$tag = '';
		}else{
			$tag = "#".str_replace(","," #",$tag);
		}
		$like_Cnt = $row['like_Cnt'];							// 좋아요 수
		$kml_File = $row['kml_File'];							// kml 영역파일 유무
		if($kml_File == ''){
			$kml_File = '';
		}
		$memo = $row['memo'];								// 한줄메모
		if($memo == ''){
			$memo = '';
		}
		$reg_Id = $row['reg_Id'];									// 등록자
		$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
		$member_Img = memImgInfo($reg_Id);				// 회원이미지
		if($member_Img == ''){
			$member_Img = "";
		}
		if($reg_Nname == ''){
			$reg_Nname = "";
		}
		$reg_Date = $row['reg_Date'];							// 등록일
		$regDate = substr($reg_Date, 0, 10);
		$end_Date = $row['end_Date'];							// 등록기간종료일
		if($con_Lv == "1"){
			$endDate = ""; 
		}else{
			$endDate = substr($end_Date, 0, 10); 
		}

		$mresult = ["con_Idx" => $idx, "con_Name" => $con_Name, "con_Lv" => $con_Lv, "category" => $category,  "img_Bit" => (string)$img_Bit, "img" => $img, "tag" => $tag, "like_Cnt" => $like_Cnt, "kml_File" => $kml_File, "memo" => $memo, "pin_Cnt" => $pin_Cnt, "subs_bit" => $subs_bit, "member_Img" => $member_Img, "reg_Id" => $reg_Id, "nick_Name" =>$reg_Nname, "reg_Date" => $regDate, "end_Date" => $endDate];
		 array_push($data, $mresult);

	}
	$chkData = [];
	$chkData["result"] = "success";
	$chkData['lists'] = $data;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);
    dbClose($DB_con);
    $stmt = null;
	//$result = array("result" => "success", "Msg" => "지도등록성공", "mIdx" => $mIdx);
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "카테고리값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



