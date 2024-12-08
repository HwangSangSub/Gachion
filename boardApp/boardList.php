<?
	include "../lib/common.php";



	$board_id = trim($board_id);  //게시판 ID
	$mem_Id  = trim($memId);
	$memLv = $memLv;

	if ($board_id == "1") {  //공지사항
		$titNm = "공지사항";
	} else 	if ($board_id == "2") {  //문의하기
		$titNm = "문의하기";
	} else 	if ($board_id == "3") {  //이용가이드
		$titNm = "이용가이드";
	}

	$DB_con = db1();



	include "boardSetting.php";  //게시판 환경설정



	if ($board_id == "4")	{ //기타접근제어
	    $result = array("result" => "error", "errorMsg" => "잘못된 접근입니다. 해당하는 게시판이 없습니다." );
	} else {
	    $result = array("result" => "error", "errorMsg" => "게시판 권한이 없습니다. 로그인 후 이용해 주세요!" );
    }

	########## 기본 설정 시작 ##########
	//	$url_etc = "?page=";
	//$base_url = $PHP_SELF."?board_id=".$board_id;
	$base_url = $PHP_SELF;

	if($b_Part != "")  {
	    $sql_search = " AND b_Cate = :b_Cate";
	}


	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_BOARD WHERE b_Idx = :board_id AND b_Not = '' AND b_Disply = 'Y' {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
	$cntStmt->bindparam(":board_id",$board_id);

	if($b_Part != "")  {
	    $cntStmt->bindparam(":b_Cate",$b_Part);
	}

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];
	$totalCnt = (int)$totalCnt;

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함

	$page = (int)$page;

	if ($b_Type == "1" || $b_Type == "5") {  //일반 게시물
		## 공지글
		$nquery = "";
		$nquery = " SELECT idx, b_Idx, b_NIdx, b_Cate, b_Title, b_Name, reg_Date, b_Not, b_RefStep, b_Hide, b_ReadCnt, b_Content, b_Chk ";
		$nquery .= "  , ( SELECT COUNT(b_Idx) FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx ) AS fileNCnt  ";
		$nquery .= "  FROM TB_BOARD WHERE  b_Not = 'Y' AND b_Idx = :board_id ";
		$nquery .= " AND  b_Disply = 'Y'" ;    //사용여부 체크
		$nquery .= " {$sql_search} ORDER BY b_Not DESC, idx DESC";
		$nqStmt = $DB_con->prepare($nquery);
		$nqStmt->bindparam(":board_id",$board_id);

		if($b_Part != "")  {
		    $nqStmt->bindparam(":b_Cate",$b_Part);
		}

		$nqStmt->execute();
		$Ncounts = $nqStmt->rowCount();

	}

	## 공지글이 아닌 게시물
	$query = "";
	$query = " SELECT idx, b_Idx, b_NIdx, b_Cate, b_Title, b_Name, reg_Date, b_Not, b_RefStep, b_Hide, b_ReadCnt, b_Content, b_Chk  ";

	if ($b_Type == "1") {  //일반게시판
		$query .= "  , ( SELECT COUNT(b_Idx) FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx ) AS fileCnt  ";
	}

	if ($b_Type == "2") {  //갤러리게시판
		$query .= "  , ( SELECT b_FName FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx  ORDER BY TB_BOARD_FILE.idx ASC  limit 1 ) AS fileNm  ";
		$query .= "  , ( SELECT b_FIdx FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx  ORDER BY TB_BOARD_FILE.idx ASC  limit 1 ) AS fileIdx ";
		$query .= "  , ( SELECT COUNT(b_Idx) FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx ) AS fileCnt  ";
	} else if ($b_Type == "3" || $b_Type == "8" ) { //웹진게시판, 이벤트게시판
		$query .= "  , ( SELECT b_FName FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx  ORDER BY TB_BOARD_FILE.idx ASC  limit 1 ) AS fileNm  ";
		$query .= "  , ( SELECT b_FIdx FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = TB_BOARD.b_Idx AND TB_BOARD_FILE.b_NIdx = TB_BOARD.b_NIdx  ORDER BY TB_BOARD_FILE.idx ASC  limit 1 ) AS fileIdx ";
	}

	if ($b_Type == "4") {  //FAQ게시판
		$query .= "  , b_Content ";
	}

	$query .= "  FROM TB_BOARD WHERE b_Idx = :board_id ";
	$query .= " AND  b_Not = '' AND  b_Disply = 'Y'";    //사용여부 체크
	$query .= "  {$sql_search} ORDER BY b_Ref DESC, b_RefOrd ASC, b_RefStep ASC, b_NIdx DESC limit  {$from_record}, {$rows}";

	$qStmt = $DB_con->prepare($query);
	$qStmt->bindparam(":board_id",$board_id);

	if($b_Part != "")  {
	    $qStmt->bindparam(":b_Cate",$b_Part);
	}

	$qStmt->execute();
	$counts = $qStmt->rowCount();



	if ($board_id == "2") {  //문의하기
	 if ($b_CateChk == "Y") {  //카테고리 사용여부

	   $bcate  = [];
	   $chk = explode("&",$b_CateName);
	   foreach($chk as $k=>$v):
		$k = $k +1;

		$mCate = array("b_part"=> $k, "cateNm" => $v);
		array_push($bcate, $mCate);

	   endforeach;


	 }
   }



	if ($b_Type == "1" || $b_Type == "5") {  //일반게시판, 기타게시판
		include "boardNomalList.php";
	} elseif ($b_Type == "2") {  //갤러리게시판
		include "boardPhoto.php";
	} elseif ($b_Type == "4") {  //FAQ게시판
		include "boardFaq.php";
	}


	dbClose($DB_con);
	$nqStmt = null;
	$cntStmt = null;
	$qStmt = null;

?>
