<?
	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	header('Content-Type: text/html; charset=utf-8');

	$mode = trim($mode);		//구분
	$idx = trim($idx);		//고유번호
	$check = trim($chk); //선택값
	$b_Title = trim($b_Title);		//게시판명
	$b_Upload  = trim($b_Upload);	// 폴더명
	$b_Type = trim($b_Type);			// 게시판타입
	$b_Width = trim($b_Width);			// 게시판타입
	$b_CateChk = trim($b_CateChk);			// 카테고리 사용 유무
	$b_CateName = trim($b_CateName);			// 말머리기능
	$b_TitCnt = trim($b_TitCnt);			// 게시판글자수
	$b_PageCnt = trim($b_PageCnt);			// 페이지수
	$b_ItemChk = trim($cut1AltVal);			// 리스트 항목 체크
	$b_NewIcon = trim($b_NewIcon);			// 게시물new 아이콘 표시시간
	$b_UploadCnt = trim($b_UploadCnt);			// 업로드파일갯수
	$b_PwdChk = trim($b_PwdChk);			// 비밀글사용여부
	$b_EmailChk = trim($b_EmailChk);			// 이메일사용여부
	$b_RepChk = trim($b_RepChk);			// 답변글사용여부
	$b_CommentChk = trim($b_CommentChk);			// 코멘트사용여부
	$b_EditChk = trim($b_EditChk);		// 에디터 사용여부
	$b_ListLv = trim($b_ListLv);			// 목록 권한
	$b_ViewLv = trim($b_ViewLv);		// 내용 권한
	$b_WriteLv = trim($b_WriteLv);			// 글쓰기 권한
	$b_RepLv = trim($b_RepLv);			// 답변쓰기 권한
	$b_ComentLv = trim($b_ComentLv);			// 코멘트 권한
	$b_WriteP = trim($b_WriteP);			// 글쓰기 포인트
	$b_ComWriteP = trim($b_ComWriteP);	// 코멘트 포인트
	$b_Disply = trim($b_Disply);			// 게시판사용여부

/*
	echo "mode=".$mode."<BR>";
	echo "idx=".$idx."<BR>";
	echo "b_Title=".$b_Title."<BR>";
	echo  "b_Upload=".$b_Upload."<BR>";
	echo  "b_Type=".$b_Type."<BR>";
	echo  "b_Width=".$b_Width."<BR>";
	echo  "b_CateChk=".$b_CateChk."<BR>";
	echo  "b_CateName=".$b_CateName."<BR>";
	echo  "b_TitCnt=".$b_TitCnt."<BR>";
	echo  "b_PageCnt=".$b_PageCnt."<BR>";
	echo  "b_ItemChk=".$b_ItemChk."<BR>";
	echo  "b_NewIcon=".$b_NewIcon."<BR>";
	echo  "b_UploadCnt=".$b_UploadCnt."<BR>";
	echo  "b_PwdChk=".$b_PwdChk."<BR>";
	echo  "b_EmailChk=".$b_EmailChk."<BR>";
	echo  "b_RepChk=".$b_RepChk."<BR>";
	echo  "b_CommentChk=".$b_CommentChk."<BR>";
	echo "b_Disply=".$b_Disply."<BR>";
	echo  "b_ListLv=".$b_ListLv."<BR>";
	echo  "b_ViewLv=".$b_ViewLv."<BR>";
	echo  "b_WriteLv=".$b_WriteLv."<BR>";
	echo  "b_RepLv=".$b_RepLv."<BR>";
	echo  "b_ComentLv=".$b_ComentLv."<BR>";
*/	

	$b_UploadFChk = "jpg,jpeg,gif,bmp,png,psd,swf,tar,gz,tgz,alz,zip,rar,ace,arj,tif,doc,docx,xls,xlsx,hwp,ppt,pptx,pdf,dwg";		 //업로드파일체크

	$DB_con = db1();

	if ($mode == "reg") {  //등록일 경우

			//게시판 고유건 조회
			$cntQuery = "SELECT MAX(b_Idx) AS num from TB_BOARD_SET LIMIT 1" ;
			$cntStmt = $DB_con->prepare($cntQuery);
			$cntStmt->execute();
			$cntRow = $cntStmt->fetch(PDO::FETCH_ASSOC);
			$cntNum = $cntRow['num'];
			$b_Idx = $cntNum + 1;  //게시판 고유값

			$reg_Date = DU_TIME_YMDHIS;		   //등록일

			$insQuery = "INSERT INTO TB_BOARD_SET ( b_Idx, b_Title, b_Upload, b_Type, b_Width, b_CateName, b_CateChk, b_TitCnt, b_PageCnt, b_NewIcon, b_ItemChk, b_UploadCnt, b_UploadFChk, b_PwdChk, b_EmailChk, b_RepChk, b_CommentChk, b_EditChk, b_ListLv, b_ViewLv, b_WriteLv, b_RepLv, b_ComentLv, b_WriteP, b_ComWriteP, b_Disply, reg_date ) VALUES ( :b_Idx, :b_Title, :b_Upload, :b_Type, :b_Width, :b_CateName, :b_CateChk, :b_TitCnt, :b_PageCnt, :b_NewIcon, :b_ItemChk, :b_UploadCnt, :b_UploadFChk, :b_PwdChk, :b_EmailChk, :b_RepChk, :b_CommentChk, :b_EditChk, :b_ListLv, :b_ViewLv, :b_WriteLv, :b_RepLv, :b_ComentLv, :b_WriteP, :b_ComWriteP, :b_Disply, :reg_date )";
			$stmt = $DB_con->prepare($insQuery);
			$stmt->bindParam(":b_Idx", $b_Idx);
			$stmt->bindParam(":b_Title", $b_Title);
			$stmt->bindParam(":b_Upload", $b_Upload);
			$stmt->bindParam(":b_Type", $b_Type);
			$stmt->bindParam(":b_Width", $b_Width);
			$stmt->bindParam(":b_CateName", $b_CateName);
			$stmt->bindParam(":b_CateChk", $b_CateChk);
			$stmt->bindParam(":b_TitCnt", $b_TitCnt);
			$stmt->bindParam(":b_PageCnt", $b_PageCnt);
			$stmt->bindParam(":b_NewIcon", $b_NewIcon);
			$stmt->bindParam(":b_ItemChk", $b_ItemChk);
			$stmt->bindParam(":b_UploadCnt", $b_UploadCnt);
			$stmt->bindParam(":b_UploadFChk", $b_UploadFChk);
			$stmt->bindParam(":b_PwdChk", $b_PwdChk);
			$stmt->bindParam(":b_EmailChk", $b_EmailChk);
			$stmt->bindParam(":b_RepChk", $b_RepChk);
			$stmt->bindParam(":b_CommentChk", $b_CommentChk);
			$stmt->bindParam(":b_EditChk", $b_EditChk);
			$stmt->bindParam(":b_ListLv", $b_ListLv);
			$stmt->bindParam(":b_ViewLv", $b_ViewLv);
			$stmt->bindParam(":b_WriteLv", $b_WriteLv);
			$stmt->bindParam(":b_RepLv", $b_RepLv);
			$stmt->bindParam(":b_ComentLv", $b_ComentLv);
			$stmt->bindParam(":b_WriteP", $b_WriteP);
			$stmt->bindParam(":b_ComWriteP", $b_ComWriteP);
			$stmt->bindParam(":b_Disply", $b_Disply);
			$stmt->bindParam(":reg_date", $reg_Date);

			$stmt->execute();
			$DB_con->lastInsertId();

			$preUrl = "boardManagerList.php?page=$page&$qstr";
			$message = "reg";
			proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

			$upQquery = "UPDATE TB_BOARD_SET SET b_Title = :b_Title, b_Upload = :b_Upload, b_Type = :b_Type, b_Width = :b_Width, b_CateName = :b_CateName, b_CateChk = :b_CateChk, b_TitCnt = :b_TitCnt, b_PageCnt = :b_PageCnt, b_NewIcon = :b_NewIcon, b_ItemChk = :b_ItemChk, b_UploadCnt = :b_UploadCnt, b_PwdChk = :b_PwdChk, b_EmailChk = :b_EmailChk, b_RepChk = :b_RepChk, b_CommentChk = :b_CommentChk, b_EditChk = :b_EditChk, b_ListLv = :b_ListLv, b_ViewLv = :b_ViewLv, b_WriteLv = :b_WriteLv, b_RepLv = :b_RepLv, b_ComentLv = :b_ComentLv, b_WriteP = :b_WriteP, b_ComWriteP = :b_ComWriteP, b_Disply = :b_Disply WHERE b_Idx =  :b_Idx LIMIT 1";

			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindParam("b_Title", $b_Title);
			$upStmt->bindParam("b_Upload", $b_Upload);
			$upStmt->bindParam("b_Type", $b_Type);
			$upStmt->bindParam("b_Width", $b_Width);
			$upStmt->bindParam("b_CateName", $b_CateName);
			$upStmt->bindParam("b_CateChk", $b_CateChk);
			$upStmt->bindParam("b_TitCnt", $b_TitCnt);
			$upStmt->bindParam("b_PageCnt", $b_PageCnt);
			$upStmt->bindParam("b_NewIcon", $b_NewIcon);
			$upStmt->bindParam("b_ItemChk", $b_ItemChk);
			$upStmt->bindParam("b_UploadCnt", $b_UploadCnt);
			$upStmt->bindParam("b_PwdChk", $b_PwdChk);
			$upStmt->bindParam("b_EmailChk", $b_EmailChk);
			$upStmt->bindParam("b_RepChk", $b_RepChk);
			$upStmt->bindParam("b_CommentChk", $b_CommentChk);
			$upStmt->bindParam("b_EditChk", $b_EditChk);
			$upStmt->bindParam("b_ListLv", $b_ListLv);
			$upStmt->bindParam("b_ViewLv", $b_ViewLv);
			$upStmt->bindParam("b_WriteLv", $b_WriteLv);
			$upStmt->bindParam("b_RepLv", $b_RepLv);
			$upStmt->bindParam("b_ComentLv", $b_ComentLv);
			$upStmt->bindParam("b_WriteP", $b_WriteP);
			$upStmt->bindParam("b_ComWriteP", $b_ComWriteP);
			$upStmt->bindParam("b_Disply", $b_Disply);
			$upStmt->bindparam(":b_Idx",$idx);
			$upStmt->execute();

			$preUrl = "boardManagerList.php?page=$page&$qstr";
			$message = "mod";
			proc_msg($message, $preUrl);


	} else if ($mode == "copy") { //복사일 경우

			$array = explode('/', $check);

			foreach($array as $k=>$v) {
				$idx = $v;

				$viewQuery = "";
				$viewQuery = "SELECT b_Title, b_Upload, b_Type, b_Width, b_CateName, b_CateChk, b_TitCnt, b_PageCnt, b_NewIcon, b_ItemChk, b_UploadCnt, b_PwdChk, b_EmailChk, b_RepChk, b_CommentChk, ";  
				$viewQuery .= "  b_Disply, b_ListLv, b_ViewLv, b_WriteLv, b_RepLv, b_ComentLv FROM TB_BOARD_SET WHERE b_Idx = :idx LIMIT 1  ";
				$viewStmt = $DB_con->prepare($viewQuery);
				$viewStmt->bindparam(":idx",$idx);
				$viewStmt->execute();
				$num = $viewStmt->rowCount();

				if($num < 1)  { //아닐경우
					$result = array("result" => "error");
				} else {

				   //게시판 고유건 조회
				  $cntQuery = "SELECT MAX(b_Idx) AS num from TB_BOARD_SET LIMIT 1" ;
				  $cntStmt = $DB_con->prepare($cntQuery);
				  $cntStmt->execute();
				  $cntRow = $cntStmt->fetch(PDO::FETCH_ASSOC);
				  $cntNum = $cntRow['num'];
				  $b_Idx = $cntNum + 1;  //게시판 고유값

				  while($row=$viewStmt->fetch(PDO::FETCH_ASSOC)) {
						$reg_Date = DU_TIME_YMDHIS;		   //등록일

						$insQuery = "INSERT INTO TB_BOARD_SET ( b_Idx, b_Title, b_Upload, b_Type, b_Width, b_CateName, b_CateChk, b_TitCnt, b_PageCnt, b_NewIcon, b_ItemChk, b_UploadCnt, b_UploadFChk, b_PwdChk, b_EmailChk, b_RepChk, b_CommentChk, b_Disply, b_ListLv, b_ViewLv, b_WriteLv, b_RepLv, b_ComentLv, reg_date ) VALUES ( :b_Idx, :b_Title, :b_Upload, :b_Type, :b_Width, :b_CateName, :b_CateChk, :b_TitCnt, :b_PageCnt, :b_NewIcon, :b_ItemChk, :b_UploadCnt, :b_UploadFChk, :b_PwdChk, :b_EmailChk, :b_RepChk, :b_CommentChk, :b_Disply, :b_ListLv, :b_ViewLv, :b_WriteLv, :b_RepLv, :b_ComentLv, :reg_date )";
						$stmt = $DB_con->prepare($insQuery);
						$stmt->bindParam(":b_Idx", $b_Idx);
						$stmt->bindParam(":b_Title", trim($row['b_Title']));
						$stmt->bindParam(":b_Upload", trim($row['b_Upload']));
						$stmt->bindParam(":b_Type", trim($row['b_Type']));
						$stmt->bindParam(":b_Width", trim($row['b_Width']));
						$stmt->bindParam(":b_CateName", trim($row['b_CateName']));
						$stmt->bindParam(":b_CateChk", trim($row['b_CateChk']));
						$stmt->bindParam(":b_TitCnt", trim($row['b_TitCnt']));
						$stmt->bindParam(":b_PageCnt", trim($row['b_PageCnt']));
						$stmt->bindParam(":b_NewIcon", trim($row['b_NewIcon']));
						$stmt->bindParam(":b_ItemChk", trim($row['b_ItemChk']));
						$stmt->bindParam(":b_UploadCnt", trim($row['b_UploadCnt']));
						$stmt->bindParam(":b_UploadFChk", trim($row['b_UploadFChk']));
						$stmt->bindParam(":b_PwdChk", trim($row['b_PwdChk']));
						$stmt->bindParam(":b_EmailChk", trim($row['b_EmailChk']));
						$stmt->bindParam(":b_RepChk", trim($row['b_RepChk']));
						$stmt->bindParam(":b_CommentChk", trim($row['b_CommentChk']));
						$stmt->bindParam(":b_Disply", trim($row['b_Disply']));
						$stmt->bindParam(":b_ListLv", trim($row['b_ListLv']));
						$stmt->bindParam(":b_ViewLv", trim($row['b_ViewLv']));
						$stmt->bindParam(":b_WriteLv", trim($row['b_WriteLv']));
						$stmt->bindParam(":b_RepLv", trim($row['b_RepLv']));
						$stmt->bindParam(":b_ComentLv", trim($row['b_ComentLv']));
						$stmt->bindParam(":reg_date", $reg_Date);

						$stmt->execute();
						$DB_con->lastInsertId();

				  }

				 echo "success";

				}

			}


   	} else {  //삭제일경우

			$array = explode('/', $check);

			foreach($array as $k=>$v) {
				$idx = $v;

				# 게시물여부 확인 
				$chkCntQuery = "SELECT count(b_Idx) AS num from TB_BOARD WHERE b_Idx = :idx LIMIT 1" ;
				$chkStmt = $DB_con->prepare($chkCntQuery);
				$chkStmt->bindparam(":idx",$idx);
				$chkStmt->execute();
				$chkRow = $chkStmt->fetch(PDO::FETCH_ASSOC);
				$num = $chkRow['num'];

				if($num == 0) { //게시물이 없을 경우
					$delQquery = "DELETE FROM TB_BOARD_SET WHERE idx =  :idx LIMIT 1";
					$delStmt = $DB_con->prepare($delQquery);
					$delStmt->bindParam(":idx", $idx);
					$delStmt->execute();
				} else {  
					$result = "1";
				}


			}

			# 데이터가 없으면 | 있으면
			if ($result == FALSE) {
				echo "error";
			} else {
				echo "success";
			}

	}


	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;
	$viewStmt = null;
	$chkStmt = null;
	$delStmt = null;


?>