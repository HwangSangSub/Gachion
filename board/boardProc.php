<?
	include "../lib/common.php";
	include "../lib/alertLib.php";
	header('Content-Type: text/html; charset=utf-8');

    //$bordChk = "bwrite";   //환경설정 인크루드 같이 사용하기 위해서..
 	//$idx = trim(strip_tags(mysql_real_escape_string($_POST['idx'])));						        //게시판Idx  ==>이거는 이렇게 해야값을 받음.

	$mode = trim($mode);  //구분
	$b_MemId = trim($b_MemId);  //아이디(회원)
	$b_SMemId = trim($b_SMemId);  //회원고유 아이디
	$b_Idx = trim($board_id);  //게시판ID
	$idx = trim($idx);  //게시판Idx
	$preUrl = urldecode($preUrl); //echo $preUrl;

	$DB_con = db1();

	if ($mode == "reg" || $mode == "rep") {  //등록, 답변

		$bchkQuery = "";
		$bchkQuery = "SELECT MAX(b_NIdx) AS b_NIdx FROM TB_BOARD WHERE b_Idx = :b_Idx LIMIT 1";

		$bchkSmt = $DB_con->prepare($bchkQuery);
		$bchkSmt->bindparam(":b_Idx",$b_Idx);
		$bchkSmt->execute();
		$bcRow = $bchkSmt->fetch(PDO::FETCH_ASSOC);
		$b_NIdx = $bcRow['b_NIdx'] + 1;

	}


	include "board_imgLib.php";			   //이미지 업로드 저장

	$b_Cate = trim($b_Cate);						//카테고리
	$b_Title = trim($b_Title);						//제목
	$b_Name = trim($b_Name);				//닉네임
	$b_Content =  trim($b_Content);		//내용
	$b_Content = str_replace("'","`",$b_Content);

	$b_Not = trim($b_Not);				//공지사항체크유무
	$b_Hide = trim($b_Hide);		   //비공개체크유무
	$b_Chk = trim($b_Chk);		   //문의하기 여부

	if ($b_Cate != "") {
		$b_Cate = $b_Cate;
	} else {
		$b_Cate = "";
	}

	if ($b_RContent != "") {
		$b_RContent = $b_RContent;
	} else {
		$b_RContent = "";
	}

	if ($b_Not != "") {
		$b_Not = $b_Not;
	} else {
		$b_Not = "";
	}

	if ($b_Hide != "") {
		$b_Hide = $b_Hide;
	} else {
		$b_Hide = "";
	}

	if ($b_Chk != "") {
		$b_Chk = $b_Chk;
	} else {
		$b_Chk = "";
	}


	if ($mode == "reg" || $mode == "rep") {  //등록, 답변

	        $regDate = DU_TIME_YMDHIS;																   //시간등록

		    //답변글이 있을경우
			if ( $b_RContent = "") {
				$b_RContent = "";
			} else {
				$b_RContent = $b_RContent;
				$b_RContent = str_replace("'","`",$b_RContent);
			}

			if ($b_NIdx) {

				if ($mode == "rep") {  // 답변

					$b_Ref = trim($b_Ref);						//뎁스
					$b_RefStep = trim($b_RefStep);  	//뎁스
					$b_RefOrd = trim($b_RefOrd);		   //순서
					//echo $b_Ref."<BR>";
					//echo $b_RefStep."<BR>";
					//echo $b_RefOrd."<BR>";
					//exit;

					// 답변뎁스 값 구하기 위해서
					$deQuery = "";
					$deQuery = "SELECT b_RefOrd FROM  TB_BOARD WHERE b_Idx= :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
					$deStmt = $DB_con->prepare($deQuery);
					$deStmt->bindparam(":b_Idx",$b_Idx);
					$deStmt->bindparam(":b_NIdx",$idx);
					$deStmt->execute();
					$deNum = $deStmt->rowCount();

					if($deNum < 1)  { //아닐경우
					} else {
						while($deRow=$deStmt->fetch(PDO::FETCH_ASSOC)) {
							$chkOrd = trim($row['depthOrd']);					//답변일 경우 순서값 가져옴.
						}
						$chkOrd = $chkOrd + 1;
					}

					$b_RefStep  = (int)$b_RefStep + 1;
					$b_RefOrd = (int)$b_RefOrd  + 1;
					//echo $b_RefStep."<BR>";
					//echo $b_RefOrd."<BR>";
					//exit;

					//게시글 순서 변경
					$ordQuery = "UPDATE TB_BOARD SET b_RefOrd = :b_RefOrd WHERE idx = :idx LIMIT 1";
					$ordStmt = $DB_con->prepare($ordQuery);
					$ordStmt->bindparam(":b_RefOrd",$chkOrd);
					$ordStmt->bindparam(":idx",$idx);
					$ordStmt->execute();
				}
			}

/*
echo "b_Idx=".$b_Idx."<BR>";
echo "b_NIdx=".$b_NIdx."<BR>";
echo "b_Cate=".$b_Cate."<BR>";
echo "b_MemId=".$b_MemId."<BR>";
echo "b_Name=".$b_Name."<BR>";
echo "b_Title=".$b_Title."<BR>";
echo "b_Content=".$b_Content."<BR>";
echo "b_RContent=".$b_RContent."<BR>";
echo "b_Ref=".$b_Ref."<BR>";
echo "b_RefStep=".$b_RefStep."<BR>";
echo "b_RefOrd=".$b_RefOrd."<BR>";
echo "b_Not=".$b_Not."<BR>";
echo "b_Hide=".$b_Hide."<BR>";
echo "reg_Date=".$regDate."<BR>";
*/


			if ($b_Ref != "") {
				$b_Ref = $b_Ref;
			} else {
				$b_Ref = 0;
			}

			if ($b_RefStep != "") {
				$b_RefStep = $b_RefStep;
			} else {
				$b_RefStep = 0;
			}

			if ($b_RefOrd != "") {
				$b_RefOrd = $b_RefOrd;
			} else {
				$b_RefOrd = 0;
			}


		   $b_Ip = escape_trim($_SERVER['REMOTE_ADDR']);

			
			//$insQuery = "INSERT INTO TB_BOARD (b_Idx, b_NIdx, b_Cate, b_MemId, b_Title, b_Name, b_Content, b_RContent, b_Ref, b_RefStep, b_RefOrd, b_Not, b_Hide, reg_Date) VALUES ($b_Idx, $b_NIdx, $b_Cate, $b_MemId, $b_Title, $b_Name, $b_Content, $b_RContent, $b_Ref, $b_RefStep, $b_RefOrd, $b_Not, $b_Hide, $regDate)";
			$insQuery = "INSERT INTO TB_BOARD (b_Idx, b_NIdx, b_Cate, b_SMemId, b_MemId, b_Title, b_Name, b_Content, b_RContent, b_Ref, b_RefStep, b_RefOrd, b_Not, b_Hide, b_Ip, b_Chk, reg_Date) VALUES (:b_Idx, :b_NIdx, :b_Cate, :b_SMemId, :b_MemId, :b_Title, :b_Name, :b_Content, :b_RContent, :b_Ref, :b_RefStep, :b_RefOrd, :b_Not, :b_Hide, :b_Ip, :b_Chk, :reg_Date)";
			//echo $insQuery."<BR>";
			//exit;
			$stmt = $DB_con->prepare($insQuery);
			$stmt->bindParam("b_Idx", $b_Idx);
			$stmt->bindParam("b_NIdx", $b_NIdx);
			$stmt->bindParam("b_Cate", $b_Cate);
			$stmt->bindParam("b_SMemId", $b_SMemId);	
			$stmt->bindParam("b_MemId", $b_MemId);
			$stmt->bindParam("b_Title", $b_Title);
			$stmt->bindParam("b_Name", $b_Name);
			$stmt->bindParam("b_Content", $b_Content);
			$stmt->bindParam("b_RContent", $b_RContent);
			$stmt->bindParam("b_Ref", $b_Ref);
			$stmt->bindParam("b_RefStep", $b_RefStep);
			$stmt->bindParam("b_RefOrd", $b_RefOrd);
			$stmt->bindParam("b_Not", $b_Not);
			$stmt->bindParam("b_Hide", $b_Hide);
			$stmt->bindParam("b_Ip", $b_Ip);
			$stmt->bindParam("b_Chk", $b_Chk);
			$stmt->bindParam("reg_Date", $regDate);
			$stmt->execute();
			$DB_con->lastInsertId();

			if($stmt->rowCount() > 0 ) { //삽입 성공
				$preUrl = "../board/boardList.php?board_id=".$b_Idx;
				$message = "reg";
				proc_msg($message, $preUrl);
			} else {
			   $msg = "정상적으로 처리 되지 못했습니다. 관리자에게 문의하세요.";
				proc_msg3($msg);
			}


	} else if ($mode == "mod") { //수정일경우

			$upQquery = "UPDATE TB_BOARD SET b_Cate = :b_Cate, b_Not = :b_Not, b_Title = :b_Title, b_Name = :b_Name, b_Content = :b_Content, b_Chk = :b_Chk WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
			//$upQuery = "UPDATE TB_BOARD SET b_Title = $b_Title, b_Content = $b_Content, b_Name = $b_Name WHERE b_Idx = $b_Idx AND b_NIdx = $idx LIMIT 1";
			//echo $upQquery."<BR>";
			//exit;
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":b_Cate",$b_Cate);
			$upStmt->bindparam(":b_Not",$b_Not);
			$upStmt->bindparam(":b_Title",$b_Title);
			$upStmt->bindparam(":b_Name",$b_Name);
			$upStmt->bindparam(":b_Content",$b_Content);
			$upStmt->bindParam(":b_Chk", $b_Chk);
			$upStmt->bindParam(":b_Idx", $b_Idx);
			$upStmt->bindParam(":b_NIdx", $idx);
			$upStmt->execute();

			if($upStmt ) { //삽입 성공
				$preUrl = "../board/boardList.php?board_id=".$b_Idx;
				$message = "mod";
				proc_msg($message, $preUrl);
			} else {
			   $msg = "정상적으로 처리 되지 못했습니다. 관리자에게 문의하세요.";
				proc_msg3($msg);
			}

	} else if ($mode == "fileDel") { //파일첨부 삭제

			$b_Idx = trim($board_id);				//게시판IDX
			$b_NIdx = trim($bNidx);				//게시판 ID
			$bFidx = trim($bFidx);					//첨부파일 idx

			//첨부파일폴더
			$foldQuery = "";
			$foldQuery = "SELECT b_Upload FROM TB_BOARD_SET WHERE b_Idx = :b_Idx LIMIT 1 ";
			$foldStmt = $DB_con->prepare($foldQuery);
			$foldStmt->bindparam(":b_Idx",$b_Idx);
			$foldStmt->execute();
			$foldNum = $foldStmt->rowCount();

			if($foldNum < 1)  { //아닐경우
			} else {
			    while($foldRow=$foldStmt->fetch(PDO::FETCH_ASSOC)) {
					$chkUpload = trim($foldRow['b_Upload']);					//첨부파일 폴더
				}
			}

			//첨부파일
			$bFileQuery = "";
			$bFileQuery = "  SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_FSize FROM TB_BOARD_FILE ";
			$bFileQuery .= "  WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx AND b_FIdx = :b_FIdx LIMIT 1 ";
			$bFileStmt = $DB_con->prepare($bFileQuery);
			$bFileStmt->bindparam(":b_Idx",$b_Idx);
			$bFileStmt->bindparam(":b_NIdx",$b_NIdx);
			$bFileStmt->bindparam(":b_FIdx",$bFidx);
			$bFileStmt->execute();
			$bFileNum = $bFileStmt->rowCount();

			if($bFileNum < 1)  { //아닐경우
				echo "";
			} else {
				while($bFileRow=$bFileStmt->fetch(PDO::FETCH_ASSOC)) {
					$b_FName = $bFileRow['b_FName'];
					$b_FIdx = $bFileRow['b_FIdx'];

					$dirRoot = $_SERVER["DOCUMENT_ROOT"]."/taxiKing";
					$uploadFolder =  trim($chkUpload);  //게시판 업로드폴더
					$upload_dir   =  $dirRoot."/data/".$uploadFolder."/";
					$fileName   =  $dirRoot."/data/".$uploadFolder."/".$b_FName;

					//파일 삭제
					if(file_exists($fileName)){
					   unlink($fileName);
					 }
				}

				$delFileQuery = "DELETE FROM TB_BOARD_FILE WHERE  b_idx = :b_Idx AND b_NIdx = :b_NIdx AND b_FIdx = :b_FIdx LIMIT 1";
				$delFileStmt = $DB_con->prepare($delFileQuery);
				$delFileStmt->bindParam("b_Idx", $b_Idx);
				$delFileStmt->bindParam(":b_NIdx", $b_NIdx);
				$delFileStmt->bindParam(":b_FIdx", $b_FIdx);
				$delFileStmt->execute();

				echo "success";

			}

	} else if ($mode == "allDel") {  //삭제일경우

			$b_Idx = trim($board_id);		//게시판IDX
			$check = trim($chk);				//게시판 ID
			$array = explode('/', $check);

			foreach($array as $k=>$v) {
				$chkIdx = $v;

				//첨부파일폴더
				$foldQuery = "";
				$foldQuery = "SELECT b_Upload FROM TB_BOARD_SET  WHERE b_Idx = :b_Idx LIMIT 1 ";
				$foldStmt = $DB_con->prepare($foldQuery);
				$foldStmt->bindparam(":b_Idx",$b_Idx);
				$foldStmt->execute();
				$foldNum = $foldStmt->rowCount();

				if($foldNum < 1)  { //아닐경우
				} else {
					while($foldRow=$foldStmt->fetch(PDO::FETCH_ASSOC)) {
						$chkUpload = trim($foldRow['b_Upload']);					//첨부파일 폴더
					}
				}

				//첨부파일
				$bFileQuery = "";
				$bFileQuery = "  SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_FSize FROM TB_BOARD_FILE ";
				$bFileQuery .= "  WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1 ";
				$bFileStmt = $DB_con->prepare($bFileQuery);
				$bFileStmt->bindparam(":b_Idx",$b_Idx);
				
				$bFileStmt->bindparam(":b_NIdx",$chkIdx);
				$bFileStmt->execute();
				$bFileNum = $bFileStmt->rowCount();

				if($bFileNum < 1)  { //아닐경우
				} else {
					while($bFileRow=$bFileStmt->fetch(PDO::FETCH_ASSOC)) {
						$b_FName = $bFileRow[b_FName];
						$b_FIdx = $bFileRow[b_FIdx];

						$dirRoot = $_SERVER["DOCUMENT_ROOT"]."/taxiKing";
						$uploadFolder =  trim($chkUpload);  //게시판 업로드폴더
						$upload_dir   =  $dirRoot."/data/".$uploadFolder."/";
						$fileName   =  $dirRoot."/data/".$uploadFolder."/".$b_FName;

						//파일 삭제
						if(file_exists($fileName)){
						   unlink($fileName);
						 }

						$delFileQuery = "DELETE FROM TB_BOARD_FILE WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
						$delFileStmt = $DB_con->prepare($delFileQuery);
						$delFileStmt->bindParam("b_Idx", $b_Idx);
						$delFileStmt->bindParam(":b_NIdx", $chkIdx);
						$delFileStmt->execute();

					}
			}

			 //댓글 삭제
			$delComQuery = "DELETE FROM TB_BOARD_COMMENT WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
			$delComStmt = $DB_con->prepare($delComQuery);
			$delComStmt->bindParam("b_Idx", $b_Idx);
			$delComStmt->bindParam(":b_NIdx", $chkIdx);
			$delComStmt->execute();


			 //게시물 삭제
			$delQuery = "DELETE FROM TB_BOARD WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
			$delStmt = $DB_con->prepare($delQuery);
			$delStmt->bindParam("b_Idx", $b_Idx);
			$delStmt->bindParam(":b_NIdx", $chkIdx);
			$delStmt->execute();

			echo "success";
		}
	}


	dbClose($DB_con);
	$bchkSmt = null;
	$deStmt = null;
	$ordStmt = null;
	$upStmt = null;
	$foldStmt = null;
	$bFileStmt = null;
	$delFileStmt = null;
	$delComStmt = null;
	$delStmt = null;
	$bStmt = null;
	$chkFStmt = null;
	$bNFileSmt = null;
	$fstmt = null;
	$bMFileSmt = null;
	$fMstmt = null;



?>
