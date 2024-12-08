<?
	include "../lib/common.php";
	header('Content-Type: text/html; charset=utf-8');
	$board_id = trim($board_id);  //게시판 ID
	$idx = trim($idx);  //게시판 Idx
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
	
	if ($b_ViewLv < $memLv) {  //게시판 글보기권한 
	   $message = $altMessage;
	   proc_amsg($message);
	}

	
	//조회수 업데이트
	$bquery = "";
	$bquery = "SELECT b_NIdx, b_Cate, b_MemId,  b_IP, b_ReadCnt  FROM TB_BOARD WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
	$bStmt = $DB_con->prepare($bquery);
	$bStmt->bindparam(":b_Idx",$board_id);
	$bStmt->bindparam(":b_NIdx",$idx);
	$bStmt->execute();
	$bNum = $bStmt->rowCount();

	if($bNum < 1)  { //아닐경우
		$message = "잘못된 접근 방식입니다.";
		proc_msg3($message);
	} else {
		while($bsRow=$bStmt->fetch(PDO::FETCH_ASSOC)) {
		    $b_Idx = $board_id;
			$bidx = trim($bsRow['b_NIdx']);			
			$b_MemId = trim($bsRow['b_MemId']);			
			$b_IP = trim($bsRow['b_IP']);		
			$b_ReadCnt = trim($bsRow['b_ReadCnt']);
		}
	}

	$chkMemID = $mem_Id;  //세션아이디
	$b_Ip = $_SERVER["REMOTE_ADDR"];  //아이피

	$wMemID = $b_MemId;  //작성자ID
	$wIp = $b_IP;   //작성자 IP

	//조회수 업데이트
	if ($chkMemID != $wMemID || $wMemID == "") {
	    
	    //echo "DD";
	   // exit;
	   if ($b_Ip != $wIp) {  
			$upQuery = "UPDATE TB_BOARD SET b_ReadCnt = $bReadCnt + 1 WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
			$upStmt = $DB_con->prepare($upQuery);
			$upStmt->bindparam(":b_Idx",$b_Idx);
			$upStmt->bindparam(":b_NIdx",$bidx);
			$upStmt->execute();
	   } 
	}
	//조회수 업데이트 끝


	$query = "";
	$query = " SELECT b_NIdx, b_Cate, b_Chk, b_MemId,  b_Title, b_Name, b_Content, b_Rcontent, b_IP, b_ReadCnt, b_Ref, b_RefStep, b_RefOrd, b_Not, b_Hide, reg_Date ";
	$query .= "  FROM  TB_BOARD  WHERE b_Idx= :b_Idx AND b_NIdx = :b_NIdx LIMIT 1 ";
	$qStmt = $DB_con->prepare($query);
	$qStmt->bindparam(":b_Idx",$board_id);
	$qStmt->bindparam(":b_NIdx",$idx);
	$qStmt->execute();
	$bNum = $qStmt->rowCount();

	if($bNum < 1)  { //아닐경우
		$message = "잘못된 접근 방식입니다.";
		proc_amsg($message);
	} else {
		$mode = "mod";
	    $chkType = "M";

		while($v=$qStmt->fetch(PDO::FETCH_ASSOC)) {
			$b_Idx = $board_id;
			$idx = trim($v['b_NIdx']);
			$b_Cate = trim($v['b_Cate']);
			$b_Chk = trim($v['b_Chk']);
			$b_MemId = trim($v['b_MemId']);
			$b_Title = trim($v['b_Title']);
			$b_Name = trim($v['b_Name']);
			$b_Content = nl2br(trim($v['b_Content']));
			$b_Rcontent = htmlspecialchars_decode(trim($v['b_Rcontent']));
			$b_Not = trim($v['b_Not']);
			$reg_Date = trim($v['reg_Date']);
			$cur_time = date("Y.m.d H:i:s",time());
			$b_Upload = trim($b_Upload); 
			$readCnt  = trim($v['b_ReadCnt']);    //조회수
		}

		$bFileUpload = $b_UploadCnt;

	    if ($bFileUpload > 0 ) {
		   # 파일첨부  조회
		   $bFileQuery = "";
		   $bFileQuery = " SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize FROM TB_BOARD_FILE  WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx ";
		   $bFileQuery .= " ORDER BY b_FIdx DESC";
		   $bFileStmt = $DB_con->prepare($bFileQuery);
		   $bFileStmt->bindparam(":b_Idx",$b_Idx);
		   $bFileStmt->bindparam(":b_NIdx",$idx);
		   $bFileStmt->execute();
		   $bFileNum = $bFileStmt->rowCount();
		}

	}
	
	

	$qstr = "board_id=".urlencode($board_id)."&amp;b_Part=".urlencode($b_Part);
	
	include "boardHead.php";  //게시판 헤더
?>

<script>
	function chkDel(b_Idx, idx){

			//삭제시작
		  if(!confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?"))   {
			   return;                                      
		  }  else {
				var action = "/board/boardProc.php";
				$.ajax({
					type: "POST",
					url: action,
					data: 'mode=allDel&board_id='+b_Idx+'&chk='+idx,
					success: function(response) {
						if($.trim(response) == 'success') {
							alert("삭제되었습니다.");
							location.replace('/board/boardList.php?board_id='+b_Idx);
						} else {
							alert("에러입니다. 관리자에게 문의해 주세요.");
						}
					}
				});
		  }
		 //삭제끝
	}
	
	<? if ($b_Chk == "Y") {  ?>  
		function FormCheck() {
		   var message, chk;	

			if ($.trim($('#b_Content').val()) == '') {
				alert("문의 하실 내용을 입력해 주세요!");
			   $('#b_Content').focus();
			   return;
			 }
			$("#theForm").submit();

		}
	<? } ?>
</script>

    <content>
        <div class="contents">		
		
		<div>
			<ul class="title_h2">
				<li class="float_l">
					<h2><?=$titNm?></h2>			
				</li>
			</ul>
		</div>
    	<?
    	if ($board_id == "2") {  //문의하기
    	?>
    	<!-- 카테고리 -->
    	<div class="clear category">
    		<ul class="nav">
    		
    		<? if ($b_CateChk == "Y") {  //카테고리 사용여부?> 		
    		<li>
    			<!-- 카테고리 옵션 -->
    			<div class="float_l">
    			<li <? if ( $b_Part == "" ) { ?>class="on"<?}?>>
    				<a href="/board/boardList.php?board_id=<?=$board_id?>">전체</a>
    			</li>	
    			<li>
    				<span class="line_gray">|</span>
    			</li>			
    			<? 
    			
    				$chk = explode("&",$b_CateName);
    				foreach($chk as $k=>$v):
    				$k = $k +1;
    			?>
            		<li <? if ( $k == $b_Part ) { ?>class="on"<?}?>>
            			<a href="/board/boardList.php?board_id=<?=$board_id?>&amp;b_Part=<?=$k;?>"><?=$v?></a>
            		</li>			
    				<li>
    					<span class="line_gray">|</span>
    				</li>				
    			<? 
    			    endforeach;
    		     } 
    		   ?>				
    		
    		</ul>
    	</div>
     <? } ?>	
    			
			<div class="du01">
				
				<ul class="view_contents">
					<li class="title">
						<p>
						<span class="title"><?=$b_Title?></span>
						</p>
						
						<div class="admin_l">
						<p>
						<span class="date"><?= DateHard($reg_Date,1) ?></span>
						</p>
						</div>
						
						<!-- 관리자일 경우만 보임 -->

						<? if ($du_udev['lv'] == '0' || $du_udev['lv'] == '1') {   //버튼 관리자 권한 ?>
							<div class="admin_r">
								<p>
<? if($_COOKIE['du_udev']['id'] != 'admin2'){ ?>
									<span class="btn blue" onclick="location.href='/board/boardReg.php?<?=$qstr?>&amp;idx=<?=$idx?>&amp;mode=M'">수정</span>
									<span class="btn red" onclick="chkDel(<?=$board_id?>,<?=$idx?>)";>삭제</span>
<? } ?>
								</p>
							</div>

					   <? } ?>
					</li>
					
					<li class="m_content">
						<p>
						<? 
							if($bFileNum < 1)  { //아닐경우
							} else {
								$imgUrl = "/data/".$b_Upload."/";

								while($i = $bFileStmt->fetch(PDO::FETCH_ASSOC)) {
									$bFName = trim($i['b_FName']);

									$fname = explode(".", $i['b_FName']);
									$fileExt = strtolower($fname[count($fname)-1]);   //확장자 구하는것

									If ($fileExt == "gif" || $fileExt == "jpeg" || $fileExt == "jpg" || $fileExt == "png" || $fileExt == "bmp") {  //확장자 이미지 체크
						?>
									<img src="<?=$imgUrl?><?=$i['b_FName']?>" class="thumb"></br></br>
						<?
									}
								}								
							}
						?>
						</p>
						<p><?=$b_Content?></p>

					</li>
    				<? if ($b_Chk == "Y") {  //카테고리 사용여부?> 		
    				<p>&nbsp;</p>
    				
           			<form name="theForm" id="theForm" action="boardMProc.php" method="post" enctype="multipart/form-data" autocomplete="off">
        			<input type="hidden" name="b_MemId" id="b_MemId" value="<?=$b_MemId?>">	
        			<input type="hidden" name="b_Name" id="b_Name" value="<?=$b_Name?>">	
        			<input type="hidden" name="board_id" id="board_id" value="<?=$board_id?>">	
    				
    				<ul class="write_contents">
    					<li class="m_content">					
    						<textarea id="b_Content" name="b_Content" placeholder="문의 하실 내용을 입력하세요."></textarea>
    					</li>
    				</ul>	
    				</form>
    				<? } ?>
    				
					<li class="bottom">
						<div class="admin_r">
						<p>
						<? if ($b_Chk == "Y") {  //카테고리 사용여부?> 		
							<span class="btn gray" onclick="location.href='/board/boardList.php?<?=$qstr?>'"> 목록 </span>
							<span class="btn blue" onclick="FormCheck();">상담하기</span>
						<? } else { ?>
							<span class="btn blue" onclick="location.href='/board/boardList.php?<?=$qstr?>'"> 목록 </span>						
						<? } ?>
						</p>
						</div>
					</li>
				</ul>
			</div>
			
			
			
			
        </div>
    </content>

</body>
</html>

<?
    dbClose($DB_con);
    $bStmt = null;
    $upStmt = null;
    $qStmt = null;
    $bFileStmt = null;
?>