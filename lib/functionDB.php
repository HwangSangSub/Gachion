<?
/*======================================================================================================================

* 프로그램			: DB 내용 불러올 함수
* 페이지 설명		: DB 내용 불러올 함수

========================================================================================================================*/


/*회원 여부확인 */
function memChk($mem_Id) {
    
    $fDB_con = db1();
    
    $memChkQuery = "SELECT * FROM TB_MEMBERS WHERE mem_id = :mem_Id AND b_Disply = 'N' ORDER BY idx DESC LIMIT 1" ;
    $memChkStmt = $fDB_con->prepare($memChkQuery);
    $memChkStmt->bindparam(":mem_Id",$mem_Id);
    $memChkStmt->execute();
    $memChkNum = $memChkStmt->rowCount();
    
    if($memChkNum < 1)  {				//없을 경우
		$member_Chk_Bit = "0";
    } else {									//등록된 회원이 있을 경우
		$member_Chk_Bit = "1";
    }
    return $member_Chk_Bit;
    
    dbClose($fDB_con);
    $memChkStmt = null;
}

/*회원 고유번호 가져오기 */
function memIdxInfo($mem_Id) {
    
    $fDB_con = db1();
    
    $memTQuery = "SELECT idx FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' LIMIT 1" ;
    $memTStmt = $fDB_con->prepare($memTQuery);
    $memTStmt->bindparam(":mem_Id",$mem_Id);
    $memTStmt->execute();
    $memTNum = $memTStmt->rowCount();
    
    if($memTNum < 1)  { //주 ID가 없을 경우 회원가입 시작
			$idx = "";
    } else {  //등록된 회원이 있을 경우
        while($memTRow = $memTStmt->fetch(PDO::FETCH_ASSOC)) {
            $idx = $memTRow['idx'];	       //체크 랜덤아이디
        }
        return $idx;
    }
    
    dbClose($fDB_con);
    $memTStmt = null;
}

/*회원 닉네임 가져오기 */
function memNickInfo($mem_Id) {
    
    $fDB_con = db1();
    
    $memNmQuery = "SELECT mem_NickNm FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' LIMIT 1" ;
    $memNmStmt = $fDB_con->prepare($memNmQuery);
    $memNmStmt->bindparam(":mem_Id",$mem_Id);
    $memNmStmt->execute();
    $memNmNum = $memNmStmt->rowCount();
    
    if($memNmNum < 1)  { 
    } else {  //등록된 회원이 있을 경우
        while($memNmRow = $memNmStmt->fetch(PDO::FETCH_ASSOC)) {
            $mem_NickNm = $memNmRow['mem_NickNm'];	       //체크 랜덤아이디
        }
        return $mem_NickNm;
    }
    
    dbClose($fDB_con);
    $memNmStmt = null;
}

/*히스토리내용*/
/*
	$idx => 히스토르의 고유번호
	$login_Id => 히스토리를 조회하는 아이디
	$reg_Id => 히스토리를 등록한 아이디
	$mem_Id => 히스토리를 등록되어 있는 아이디
	$nickname => 나의 닉네임
	$memnickname => 히스토리 닉네임
	$history_reg_nickname => 히스토리등록 닉네임
	$history => 히스토리유형

*/
function historyInfo($idx, $login_Id, $reg_Id, $history) {

    $fDB_con = db1();
    $hisQuery = "SELECT mem_Id, reg_Id, place_Idx, memo FROM TB_HISTORY WHERE idx = :idx LIMIT 1" ;
    $hisStmt = $fDB_con->prepare($hisQuery);
    $hisStmt->bindparam(":idx",$idx);
    $hisStmt->execute();
	while($hisRow = $hisStmt->fetch(PDO::FETCH_ASSOC)) {
		$history_Id = $hisRow['mem_Id'];			// 히스토리아이디
		$history_regId = $hisRow['reg_Id'];	    // 히스토리등록아이디
		$place_Idx = $hisRow['place_Idx'];		// 히스토리지점고유번호
		$memo = $hisRow['memo'];		// 히스토리지점고유번호
	}
	/*
	로그인계정
	히스토리 등록계정
	히스토리 계정
	로그인 계정 = 히스토리 등록계정 >> 본인
	로그인 계정 <> 히스토리 등록계정 >> 다른사람
	로그인 계정 = 히스토리 계정 >> 다른사람이 나에 대한 히스토리
	*/
	$nickname = memNickInfo($login_Id);								// 로그인 닉네임
	$history_nickname = memNickInfo($history_Id);					// 히스토리 닉네임
	$history_reg_nickname = memNickInfo($history_regId);			// 히스토리등록 닉네임
    
	if($history == "지도생성"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "신규 지도를 생성하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 신규 지도를 생성하였습니다.";
		}
	}else if($history == "지점등록"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "신규 지점을 등록하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 신규 지점을 등록하였습니다.";
		}
	}else if($history == "사진업데이트"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "사진을 업데이트하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 사진을 업데이트하였습니다.";
		}
	}else if($history == "댓글등록"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "댓글을 등록하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
			$history_con = $history_reg_nickname." 님이 나의 지점에 댓글을 등록하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 ".$history_nickname." 님의 지점에 댓글을 등록하였습니다.";
		}
	}else if($history == "닉네임변경"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "닉네임을 변경하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $memo." 님이 닉네임을 변경하였습니다.";
		}
	}else if($history == "프로필사진변경"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "프로필 사진을 변경하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 프로필 사진을 변경하였습니다.";
		}
	}else if($history == "프로필소개변경"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "프로필 소개를 변경하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 프로필 소개를 변경하였습니다.";
		}
	}else if($history == "좋아요"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "좋아요를 하셨습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
			$history_con = $history_reg_nickname." 님이 좋아요를 하셨습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 ".$history_nickname." 님의 지점을 좋아요 하셨습니다.";
		}
	}else if($history == "관심등록"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "관심 유저를 추가하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
			$history_con = $history_reg_nickname." 님이 나를 관심 유저로 추가하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 ".$history_nickname." 님을 관심 유저로 추가하셨습니다.";
		}
	}else if($history == "구독"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "지도를 구독하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
			$history_con = $history_reg_nickname." 님이 내 지도를 구독하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 ".$history_nickname." 님의 지도를 구독하셨습니다.";
		}
	}else if($history == "즐겨찾기"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "지도를 즐겨찾기하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
			$history_con = $history_reg_nickname." 님이 내 지도를 즐겨찾기하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 ".$history_nickname." 님의 지도를 즐겨찾기하셨습니다.";
		}
	}else if($history == "담기"){
		$place_Id = placeIdInfo($place_Idx);
		if($login_Id == $history_regId){	// 본인
			$history_con = "지점을 담기하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
			$history_con = $history_reg_nickname." 님이 내 지점을 담아가셨습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id && $login_Id == $place_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 내 지점을 담아가셨습니다.";
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 ".$history_nickname." 님의 지점을 담았습니다.";
		}
	}else if($history == "신고"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "지점을 신고하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
		}
	}else if($history == "지점공개여부"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "지점의 공개여부가 변경하였습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
		}
	}else if($history == "지도삭제"){
		if($login_Id == $history_regId){	// 본인
			$history_con = "지도를 삭제하셨습니다.";
		}else if($login_Id <> $history_regId && $login_Id == $history_Id){	// 다른사람이 나에 대한 히스토리
		}else if($login_Id <> $history_regId && $login_Id <> $history_Id){	// 관심유저
			$history_con = $history_reg_nickname." 님이 지도를 삭제하셨습니다.";
		}
	}
	return $history_con;
    dbClose($fDB_con);
    $hisStmt = null;
}
/*
function historyInfo($idx, $reg_Id, $history) {

    $fDB_con = db1();
    $hisQuery = "SELECT mem_Id, reg_Id FROM TB_HISTORY WHERE idx = :idx LIMIT 1" ;
    $hisStmt = $fDB_con->prepare($hisQuery);
    $hisStmt->bindparam(":idx",$idx);
    $hisStmt->execute();
	while($hisRow = $hisStmt->fetch(PDO::FETCH_ASSOC)) {
		$history_Id = $hisRow['mem_Id'];	       //히스토리아이디
		$history_regId = $hisRow['reg_Id'];	       //히스토리등록아이디
	}
	$nickname = memNickInfo($reg_Id);									// 회원닉네임
	$history_nickname = memNickInfo($history_Id);						// 회원닉네임
	$history_reg_nickname = memNickInfo($history_regId);			// 히스토리등록아이디
    
	if($history == "지도생성"){
		if($reg_Id == $history_Id){
			$history_con = "신규 지도를 생성하였습니다.";
		}else{
			$history_con = $memnickname."님이 신규 지도를 생성하였습니다.";
		}
	}else if($history == "지점등록"){
		if($reg_Id == $history_Id){
			$history_con = "신규 지점을 등록하였습니다.";
		}else{
			$history_con = $memnickname."님이 신규 지점을 등록하였습니다.";
		}
	}else if($history == "사진업데이트"){
		if($reg_Id == $history_Id){
			$history_con = "사진을 업데이트하였습니다.";
		}else{
			$history_con = $memnickname."님이 사진을 업데이트하였습니다.";
		}
	}else if($history == "댓글등록"){
		if($reg_Id == $history_Id){
			$history_con = "댓글을 등록하였습니다.";
		}else{
			$history_con = $memnickname."님이 댓글을 등록하였습니다.";
		}
	}else if($history == "닉네임변경"){
		if($reg_Id == $history_Id){
			$history_con = "신규 지점을 등록하였습니다.";
		}else{
			$history_con = $memnickname."님이 신규 지점을 등록하였습니다.";
		}
	}else if($history == "프로필사진변경"){
		if($reg_Id == $history_Id){
			$history_con = "프로필 사진을 변경하였습니다.";
		}else{
			$history_con = $memnickname."님이 프로필 사진을 변경하였습니다.";
		}
	}else if($history == "프로필소개변경"){
		if($reg_Id == $history_Id){
			$history_con = "프로필 소개를 변경하였습니다.";
		}else{
			$history_con = $memnickname."님이 프로필 소개를 변경하였습니다.";
		}
	}else if($history == "좋아요"){
		if($reg_Id == $history_Id){
			$history_con = "좋아요를 하셨습니다.";
		}else{
			$history_con = $memnickname."님이 좋아요를 하셨습니다.";
		}
	}else if($history == "관심등록"){
		if($reg_Id != $history_Id){
			$history_con = "관심 유저를 추가하였습니다.";
		}else{
			$history_con = $history_nickname."님이 나를 관심 유저로 추가하였습니다.";
		}
	}else if($history == "구독"){
		if($reg_Id != $history_Id){
			$history_con = "지도를 구독하였습니다.";
		}else{
			$history_con = $history_reg_nickname."님이 내 지도를 구독하였습니다.";
		}
	}else if($history == "즐겨찾기"){
		if($reg_Id != $history_Id){
			$history_con = "지도를 즐겨찾기하였습니다.";
		}else{
			$history_con = $history_reg_nickname."님이 내 지도를 즐겨찾기하였습니다.";
		}
	}else if($history == "신고"){
		if($reg_Id != $history_Id){
			$history_con = "지점을 신고하였습니다.";
		}else{
			$history_con = $history_reg_nickname."님이 내 지도를 즐겨찾기하였습니다.";
		}
	}
	return $history_con;
    dbClose($fDB_con);
    $hisStmt = null;
}
*/
/*지도명 가져오기 */
function contentsNameInfo($con_Idx) {
    
    $fDB_con = db1();
    
    $conQuery = "SELECT con_Name FROM TB_CONTENTS WHERE idx = :idx AND delete_Bit = '0' LIMIT 1" ;
    $conStmt = $fDB_con->prepare($conQuery);
    $conStmt->bindparam(":idx",$con_Idx);
    $conStmt->execute();
    $conNum = $conStmt->rowCount();
    
    if($conNum < 1)  { //없을 경우
		$conName = '';
    } else {  
        while($conRow = $conStmt->fetch(PDO::FETCH_ASSOC)) {
            $conName = $conRow['con_Name'];	  
        }
        return $conName;
    }
    
    dbClose($fDB_con);
    $conStmt = null;
}

/*지점의 지도고유번호 가져오기 */
function conIdxInfo($place_Idx) {
    
    $fDB_con = db1();
    
    $conIdxQuery = "SELECT con_Idx FROM TB_PLACE WHERE idx = :idx AND delete_Bit = '0' LIMIT 1" ;
    $conIdxStmt = $fDB_con->prepare($conIdxQuery);
    $conIdxStmt->bindparam(":idx",$place_Idx);
    $conIdxStmt->execute();
    $conIdxNum = $conIdxStmt->rowCount();
    
    if($conIdxNum < 1)  { //없을 경우
		$con_Idx = '';
    } else {  
        while($conIdxRow = $conIdxStmt->fetch(PDO::FETCH_ASSOC)) {
            $con_Idx = $conIdxRow['con_Idx'];	  
        }
        return $con_Idx;
    }
    
    dbClose($fDB_con);
    $conStmt = null;
}

/*지도 등록자아이디 가져오기 */
function contentsIdInfo($con_Idx) {
    
    $fDB_con = db1();
    
    $conQuery = "SELECT reg_Id FROM TB_CONTENTS WHERE idx = :idx AND delete_Bit = '0' LIMIT 1" ;
    $conStmt = $fDB_con->prepare($conQuery);
    $conStmt->bindparam(":idx",$con_Idx);
    $conStmt->execute();
    $conNum = $conStmt->rowCount();
    
    if($conNum < 1)  { //없을 경우
		$con_Id = '';
    } else {  
        while($conRow = $conStmt->fetch(PDO::FETCH_ASSOC)) {
            $con_Id = $conRow['reg_Id'];	  
        }
        return $con_Id;
    }
    
    dbClose($fDB_con);
    $conStmt = null;
}
/*지도 등록자아이디 가져오기 */
function contentsOpenBit($con_Idx) {
    
    $fDB_con = db1();
    
    $conQuery = "SELECT open_Bit FROM TB_CONTENTS WHERE idx = :idx AND delete_Bit = '0' LIMIT 1" ;
    $conStmt = $fDB_con->prepare($conQuery);
    $conStmt->bindparam(":idx",$con_Idx);
    $conStmt->execute();
    $conNum = $conStmt->rowCount();
    
    if($conNum < 1)  { //없을 경우
		$opeb_Bit = '';
    } else {  
        while($conRow = $conStmt->fetch(PDO::FETCH_ASSOC)) {
            $opeb_Bit = $conRow['opeb_Bit'];	  
        }
        return $opeb_Bit;
    }
    
    dbClose($fDB_con);
    $conStmt = null;
}

/*지점명 가져오기 */
function placeNameInfo($place_Idx) {
    
    $fDB_con = db1();
    
    $placeQuery = "SELECT place_Name FROM TB_PLACE WHERE idx = :idx LIMIT 1" ;
    $placeStmt = $fDB_con->prepare($placeQuery);
    $placeStmt->bindparam(":idx",$place_Idx);
    $placeStmt->execute();
    $placeNum = $placeStmt->rowCount();
    
    if($placeNum < 1)  { //없을 경우
		$placeName = '';
    } else {  
        while($placeRow = $placeStmt->fetch(PDO::FETCH_ASSOC)) {
            $placeName = $placeRow['place_Name'];
        }
        return $placeName;
    }
    
    dbClose($fDB_con);
    $placeStmt = null;
}

/*지점 등록자아이디 가져오기 */
function placeIdInfo($place_Idx) {
    
    $fDB_con = db1();
    
    $placeQuery = "SELECT reg_Id FROM TB_PLACE WHERE idx = :idx LIMIT 1" ;
    $placeStmt = $fDB_con->prepare($placeQuery);
    $placeStmt->bindparam(":idx",$place_Idx);
    $placeStmt->execute();
    $placeNum = $placeStmt->rowCount();
    
    if($placeNum < 1)  { //없을 경우
		$place_Id = '';
    } else {  
        while($placeRow = $placeStmt->fetch(PDO::FETCH_ASSOC)) {
            $place_Id = $placeRow['reg_Id'];
        }
        return $place_Id;
    }
    
    dbClose($fDB_con);
    $placeStmt = null;
}
/*회원 디바이스 아이디 가져오기 */
function memDeviceIdInfo($mem_Id) {
    
    $fDB_con = db1();
    
    $memDeQuery = "SELECT mem_DeviceId FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' LIMIT 1" ;
    $memDeStmt = $fDB_con->prepare($memDeQuery);
    $memDeStmt->bindparam(":mem_Id",$mem_Id);
    $memDeStmt->execute();
    $memDeNum = $memDeStmt->rowCount();
    
    if($memDeNum < 1)  { //없을 경우
    } else {  //등록된 회원이 있을 경우
        while($memDeRow = $memDeStmt->fetch(PDO::FETCH_ASSOC)) {
            $memDeviceId = $memDeRow['mem_DeviceId'];	       //체크 랜덤아이디
        }
        return $memDeviceId;
    }
    
    dbClose($fDB_con);
    $memDeStmt = null;
}

/* 회원이미지가져오기 */
function memImgInfo($mem_Id) {
    
    $fDB_con = db1();
    
    $memImgQuery = "SELECT mem_ImgFile FROM TB_MEMBERS WHERE mem_id = :mem_Id AND b_Disply = 'N' LIMIT 1" ;
    $memImgStmt = $fDB_con->prepare($memImgQuery);
    $memImgStmt->bindparam(":mem_Id",$mem_Id);
    $memImgStmt->execute();
    $memImgNum = $memImgStmt->rowCount();
    
    if($memImgNum < 1)  { //없을 경우
    } else {  //등록된 회원이 있을 경우
        while($memImgRow = $memImgStmt->fetch(PDO::FETCH_ASSOC)) {
			$mem_ImgFile = $memImgRow['mem_ImgFile'];
			if($mem_ImgFile == ""){
				$member_Img = "";
			}else{
				// $member_Img = "https://places.gachita.kr/member/member_img/photo.php?id=".$mem_Id;
				$member_Img = "https://places.gachita.kr/member/member_img/".$mem_ImgFile;
			}
        }
        return $member_Img;
    }
    
    dbClose($fDB_con);
    $memImgStmt = null;
}
/* 썸네일이미지가져오기 */
function conImgInfo($con_Idx) {
    
    $fDB_con = db1();
    
    $conImgQuery = "SELECT img FROM TB_CONTENTS WHERE idx = :idx AND delete_Bit = '0' LIMIT 1" ;
    $conImgStmt = $fDB_con->prepare($conImgQuery);
    $conImgStmt->bindparam(":idx",$con_Idx);
    $conImgStmt->execute();
    $conImgNum = $conImgStmt->rowCount();
    
    if($conImgNum < 1)  { //없을 경우
    } else {  //등록된 지도가 있을 경우
        while($conImgRow = $conImgStmt->fetch(PDO::FETCH_ASSOC)) {
			$con_ImgFile = $conImgRow['img'];
			if($con_ImgFile == ""){
				$con_Img = "";
			}else{
				$con_Img = "https://places.gachita.kr/contents/img/photo.php?id=".$con_ImgFile;
			}
        }
        return $con_Img;
    }
    
    dbClose($fDB_con);
    $conImgStmt = null;
}
function kmlpoi($kml_File){
    $fDB_con = db1();
	$xml = file_get_contents("../contents/kmlfile/".$kml_File);
	$result_xml = simplexml_load_string($xml);

	$Placemark = $result_xml->Document->Placemark;
	$pm_cnt = count($Placemark);
	$name = [];
	for($pm = 0; $pm < $pm_cnt; $pm++){
		$name_chk = $result_xml->Document->Placemark[$pm]->name;
		array_push($name, $name_chk);
	}
	$name_cnt = count($name);
	for($nm = 0; $nm < $name_cnt; $nm++){
		$kmlpoi[$nm] = [];
		$locat = $result_xml->Document->Placemark[$nm]->Polygon->outerBoundaryIs->LinearRing->coordinates; 
		$areaName = $area[$nm];
		$locat_poi = explode( ',', $locat);
		$poi_cnt = count($locat_poi);
		$lat = [];  //위도
		$lng = []; //경도
		for($i = 0; $i < $poi_cnt; $i++){
			if($i % 2 != 0){
				//위도
				array_push($lat, (double)$locat_poi[$i]);
			}else{
				//경도
				array_push($lng, (double)str_replace(" ","",str_replace("0 ", "", $locat_poi[$i])));
			}
		}
		$lng_chk = array_pop($lng); 
		$lat_min = min($lat);
		$lat_max = max($lat);
		$lng_min = min($lng);
		$lng_max = max($lng);
		$kmlpoi[$nm] = ["lat_min" => $lat_min, "lat_max" => $lat_max, "lng_min" => $lng_min, "lng_max" => $lng_max];
		//array_push($kmlpoi[$nm], $chkkmlpoi);
		//$kmlpoi = $chk_kmlpoi;
	}
    return $kmlpoi;
    dbClose($fDB_con);
}

/* 콘텐츠 삭제여부확인 */
function contentsChk($con_Idx) {
    
    $fDB_con = db1();
    
    $conChkQuery = "SELECT COUNT(*) as cnt FROM TB_CONTENTS WHERE idx = :con_Idx AND delete_Bit = '0' ORDER BY idx DESC LIMIT 1" ;
    $conChkStmt = $fDB_con->prepare($conChkQuery);
    $conChkStmt->bindparam(":con_Idx",$con_Idx);
    $conChkStmt->execute();
    $conRow = $conChkStmt->fetch(PDO::FETCH_ASSOC);
	$c_Cnt = $conRow['cnt'];
    if($c_Cnt < 1)  {						//없을 경우
		$con_Chk_Bit = "0";
    } else {									//등록된 지점이 있는 경우
		$con_Chk_Bit = "1";
    }
    return $con_Chk_Bit;
    
    dbClose($fDB_con);
    $conChkStmt = null;
}

/* 지도내 이미지 수 */
function conimgCChk($con_Idx) {
    
    $fDB_con = db1();

	// 이미지확인
	$img_query = "
		SELECT img as pimg
		FROM TB_PLACE
		WHERE con_Idx = :con_Idx
			AND img <> ''
			OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
		ORDER BY reg_Date DESC;
		";
	$img_stmt = $fDB_con->prepare($img_query);
	$img_stmt->bindParam(":con_Idx", $con_Idx);
	$img_stmt->execute();
	$p_Img = [];
	$img_File = [];
	$img_Cnt = 0;
	while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)){
		$pimg = $img_row['pimg'];
		array_push($p_Img, $pimg);
	}
	$p_Cnt = count($p_Img);
	for($i = 0; $i < $p_Cnt; $i++){
		$pimg = $p_Img[$i];
		$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$pimg;		
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
				$img_FileName = $pimg."/".$f;
				array_push($img_File, $img_FileName);
			}
			$img_Cnt = (int)$img_Cnt + count($files);
		}else{
			$img_Cnt = $img_Cnt;
		}
	}
    return $img_Cnt;
    
    dbClose($fDB_con);
    $img_stmt = null;
}
			
?>