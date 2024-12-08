	<?
	include "../lib/common.php";

	$board_id = trim($board_id);  //게시판 ID
	//echo $board_id."<BR>";
	$mem_Id  = trim($memId);		
	$memLv = $memLv;


	if ($board_id == "1") {  //공지사항
		$titNm = "공지사항";
	} else 	if ($board_id == "2") {  //문의하기
		$titNm = "문의하기";
	} else 	if ($board_id == "3") {  //이용가이드
		$titNm = "이용가이드";
	}

	$idx = trim($idx);   //고유번호
	$mode = trim($mode);   //구분

	$DB_con = db1();

	include "boardSetting.php";  //게시판 환경설정

	if ($mode == "M") {  //수정일경우 
				
	} else if ($mode == "R") {  //수정일경우
		if ($b_RepLv < $du_udev[lv]) {  //게시판 답변 권한 
		   $message = $altMessage;
		   proc_msg2($message);
		}
	} else {  //등록일 경우
		if ($b_WriteLv < $du_udev[lv]) {  //게시판 글쓰기권한 
		   $message = $altMessage;
		   proc_msg2($message);
		}
	}

	if ($mode == "M" || $mode == "R") {  //수정일경우
		$query = "";
		$query = " SELECT b_NIdx, b_Cate, b_MemId, b_Title, b_Name, b_Content, b_Rcontent, b_ReadCnt, b_Ref, b_RefStep, b_RefOrd, b_Not, b_Hide, reg_Date ";
		$query .= "  , ( SELECT COUNT(b_Idx) FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = '$board_id' AND TB_BOARD_FILE.b_NIdx = '$idx' ) AS fileCnt  ";
		$query .= "  FROM  TB_BOARD  WHERE b_Idx = :board_id and b_NIdx = :idx LIMIT 1 ";
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":board_id",$board_id);
		$stmt->bindparam(":idx",$idx);
		$stmt->execute();
		$num = $stmt->rowCount();


		if($num < 1)	{
				$message = "잘못된 접근 방식입니다.";
				proc_msg3($message);
		} else {


			if ($mode == "M" ) {  //수정일경우
				$mode = "mod";
				$titNm = "수정";
				$btnNm   = "수정";
				$chkType = "M";

			} else if ( $mode == "R") {  //수정일경우
				$mode = "rep";
				$titNm = "답변";
				$btnNm   = "답변";
				$chkType = "R";
			}

			$v = $stmt->fetch(PDO::FETCH_ASSOC);

			$b_Idx = $board_id;
			$idx = trim($v['b_NIdx']);
			$b_Cate = trim($v['b_Cate']);
			$b_Title = trim($v['b_Title']);
			$b_Content = htmlspecialchars_decode(trim($v['b_Content']));

			if ($mode == "rep" ) {  //답변일경우
				  $b_Title = "Re:".$b_Title;
				  $b_MemId = $du_udev[id];
				  $b_Name =  $du_udev[nickNm];
				 $b_Content = $b_Content . "<br>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><BR>";
				 $b_Ref = trim($v['b_Ref']);
				 $b_Ref = trim($v['b_Ref']);
				 $b_RefStep = trim($v['b_RefStep']);
				 $b_RefOrd  = trim($v['b_RefOrd']);
	  

			} else if ( $mode == "mod") {  //수정일경우

				$b_MemId = trim($v['b_MemId']);
				$b_Title = trim($v['b_Title']);
				$b_Name = trim($v['b_Name']);
				$b_Rcontent = htmlspecialchars_decode(trim($v['b_Rcontent']));
				$b_Not = trim($v['b_Not']);
				$b_Hide = trim($v['b_Hide']);
				$b_Upload = trim($b_Upload); 
				$readCnt  = trim($v['b_ReadCnt']);    //조회수
				$fileCnt  = trim($v['fileCnt']);    //첨부파일 갯수

			}


		}
	
	} else { //등록일경우

		  $mode = "reg";
		  $mtit    = "등록";
		  $btnNm   = "등록";
		  $chkType = "R";

		  $b_MemId = $du_udev[id];
		  $b_Name =   $du_udev[nickNm];
		  $b_Not = "";
		  $b_Hide = "";

	}

	if ( isset($_GET["searchType"]) || isset($_GET["searchValue"]) ) {
		$b_Cate = base64_decode($searchType);
		$searchFlag = base64_decode($searchKey);
		$searchWord = base64_decode($searchValue);
	} else {

		$b_Cate  = $b_Cate;  //구분값
		$searchFlag = $searchFlag;
		$searchWord = str_replace("'","`",$searchWord);	
		$searchWord = $searchWord;

		$searchType = base64_encode($b_Cate);
		$searchKey = base64_encode($searchFlag);
		$searchValue = base64_encode($searchWord);
	}

	$pageParam = "page=".urlencode($page)."&amp;searchType=".urlencode($searchType)."&amp;searchKey=".urlencode($searchKey)."&amp;searchValue=".urlencode($searchValue);


	include "boardHead.php";  //게시판 헤더

?>

<script type="text/javascript">
	$(document).ready(function(e) {
		//primary
		$('a#FormCheck').click(function() {

		  var message, chk;	

		<? if ($b_CateChk == "Y") {  ?>  
		  if ($.trim($('#b_Cate option:selected').val()) == '0') {
			  alert("분류를 선택해 주세요!");
			  $('#b_Cate').focus();
		   }
		<? } ?>

		   if ($.trim($('#b_Title').val()) == '') {
			  alert("제목을 입력해 주세요!");
			  $('#b_Title').focus();
		   }

		   if ($.trim($('#b_Memo').val()) == '') {
			  alert("간략설명을 입력해 주세요!");
			  $('#b_Memo').focus();
		   }

	
		$("#theForm").submit();

		});
	});

</script>


        <h1><?=$titNm?></h1>

    <content>
        <div class="contents">			
			
			<div class="du01">
				
			<form name="theForm" id="theForm" action="boardProc.php" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
			<input type="hidden" name="qstr" id="qstr"  value="<?=$qstr?>">

			<? if ($mode == "mod" || $mode == "rep") { ?>
				<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
				<input type="hidden" name="preUrl" value="<?=urlencode($_SERVER["REQUEST_URI"])?>" />
			<? } ?> 
			
			<? if ($mode == "rep") { ?>
				<input type="hidden" name="b_Ref" value="<?=$b_Ref?>">		
				<input type="hidden" name="b_RefStep" value="<?=$b_RefStep?>">		
				<input type="hidden" name="b_RefOrd" value="<?=$b_RefOrd?>">
			<? } ?> 	


				<ul class="write_contents">
					<li class="title">
						<input type="text" id="" maxlength="50" placeholder="제목을 입력해주세요." value="" />						
					</li>
					
					<li class="m_content">					
					<textarea id="contents" placeholder="내용을 입력하세요."></textarea>
					</li>
					<!-- 파일 등록 -->
					<li class="file" style="display:none;">
						<input type="file" id="" name="" />						
					</li>
					<!-- 등록 된 파일 삭제 -->
					<li class="file">					
						<div class="file_l">
						<p>
						<span class="file">csd31oafd14ab0asdasfadfdafasdsadsadsadsadsadsacsacadcdacsacascsacsacsacsacsa_sacsacsasa.jpg</span>
						</p>
						</div>
						
						<div class="file_r">
						<p>
						<span class="btn red">X</span>
						</p>
						</div>
					</li>
					
					<li class="bottom">
						<div class="center">
						<p>
						<span class="btn gray" onclick="goBack()">취소</span>
						<!-- 						
						<span class="btn blue" onclick="location.href='list2.html'">글 쓰기</span>
						-->
						<span class="btn blue" onclick="location.href='view1.html'">수정하기</span>
						</p>
						</div>
					</li>
				</ul>
			</div>
			
			
			
			
        </div>
    </content>

</body>
</html>