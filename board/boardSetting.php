<?
	include "../lib/alertLib.php";

	// 회원이이디 체크
	if ( $du_udev['lv']  != "" ) {
		$mem_Id = $du_udev['id'];
	} else {
		$mem_Id = $mem_Id;
	}

	//회원레벨 체크
	if ( $du_udev['lv']  != "" ) {
		$memLv = $du_udev['lv'];
	} else {
		$memLv = $memLv;
	}



	//게시판 환경설정
	$bquery = "";
	$bquery = "SELECT  b_Upload, b_Type, b_Width, b_CateName, b_CateChk, b_TitCnt, b_PageCnt, b_NewIcon, b_ItemChk, b_UploadCnt, b_PwdChk, b_RepChk, b_EmailChk, b_CommentChk, b_Disply, b_ListLv, b_ViewLv, b_WriteLv, b_RepLv,  b_ComentLv, b_Header FROM  TB_BOARD_SET  WHERE b_Idx = :board_id LIMIT 1";

	$bqStmt = $DB_con->prepare($bquery);
	$bqStmt->bindparam(":board_id",$board_id);
	$bqStmt->execute();
	$bqNum = $bqStmt->rowCount();

	if($bqNum < 1)  { //아닐경우
		$message = "etc";
		$preUrl = "/board/boardList.php?board_id=1";
		proc_msg($message, $preUrl);
	} else {
		while($brow=$bqStmt->fetch(PDO::FETCH_ASSOC)) {
				$b_Upload = trim($brow['b_Upload']);
				$b_Type = trim($brow['b_Type']);
				$b_Width = trim($brow['b_Width']);
				$b_CateName = trim($brow['b_CateName']);
				$b_CateChk = trim($brow['b_CateChk']);
				$b_TitCnt = trim($brow['b_TitCnt']);
				$b_PageCnt = trim($brow['b_PageCnt']);
				$b_NewIcon = trim($brow['b_NewIcon']);
				$b_ItemChk = trim($brow['b_ItemChk']);
				$b_UploadCnt = trim($brow['b_UploadCnt']);
				$b_PwdChk = trim($brow['b_PwdChk']);
				$b_EmailChk = trim($brow['b_EmailChk']);
				$b_RepChk = trim($brow['b_RepChk']);
				$b_CommentChk = trim($brow['b_CommentChk']);
				$b_Disply = trim($brow['b_Disply']);
				$b_ListLv = trim($brow['b_ListLv']);
				$b_ViewLv = trim($brow['b_ViewLv']);
				$b_WriteLv = trim($brow['b_WriteLv']);
				$b_RepLv = trim($brow['b_RepLv']);
				$b_ComentLv = trim($brow['b_ComentLv']);
				$b_Header = trim($brow['b_Header']);
		}
	}


	 if ( $memLv == "") {  //레벨이 없을 경우 비회원 권한 부여함.
		$memLv = "15";
	 }

	 $altMessage = "게시판 권한이 없습니다. 로그인 후 이용해 주세요!";

?>