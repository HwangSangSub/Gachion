	<?
	include "../lib/common.php";
	header('Content-Type: text/html; charset=utf-8');

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

	$idx = trim($idx);   //고유번호
	$mode = trim($mode);   //구분

	$DB_con = db1();

	include "boardSetting.php";  //게시판 환경설정

	if ($mode == "M") {  //수정일경우 
				
	} else if ($mode == "R") {  //수정일경우
		if ($b_RepLv < $memLv) {  //게시판 답변 권한 
		   $message = $altMessage;
		   proc_amsg($message);
		}
	} else {  //등록일 경우

		if ($b_WriteLv < $memLv) {  //게시판 글쓰기권한 
		   $message = $altMessage;
		   proc_amsg($message);
		}
	}

	if ($mode == "M" || $mode == "R") {  //수정일경우
		$query = "";
		$query = " SELECT b_NIdx, b_Cate, b_SMemId, b_MemId, b_Title, b_Name, b_Content, b_Rcontent, b_ReadCnt, b_Ref, b_RefStep, b_RefOrd, b_Not, b_Hide, b_Chk, reg_Date ";
		$query .= "  , ( SELECT COUNT(b_Idx) FROM TB_BOARD_FILE WHERE TB_BOARD_FILE.b_Idx = :board_id AND TB_BOARD_FILE.b_NIdx = :idx ) AS fileCnt  ";
		$query .= "  FROM TB_BOARD  WHERE b_Idx = :board_id AND b_NIdx = :idx LIMIT 1 ";
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
				$btnNm   = "수정";
				$chkType = "M";

			} else if ( $mode == "R") {  //수정일경우
				$mode = "rep";
				$btnNm   = "답변";
				$chkType = "R";
			}

			$v = $stmt->fetch(PDO::FETCH_ASSOC);

			$b_Idx = $board_id;
			$idx = trim($v['b_NIdx']);
			$b_Cate = trim($v['b_Cate']);
			$b_Title = trim($v['b_Title']);
			$b_Chk = trim($v['b_Chk']);
			$b_Content = htmlspecialchars_decode(trim($v['b_Content']));

			if ($mode == "rep" ) {  //답변일경우
				 $b_Title = "Re:".$b_Title;
				 $b_MemId = $du_udev['id'];
				 $b_SMemId = $du_udev['msid']; //회원고유아이디
				 $b_Name =  $du_udev['nickNm'];
				 $b_Content = $b_Content . "<br>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><BR>";
				 $b_Ref = trim($v['b_Ref']);
				 $b_RefStep = trim($v['b_RefStep']);
				 $b_RefOrd  = trim($v['b_RefOrd']);
	  

			} else if ( $mode == "mod") {  //수정일경우

				$b_MemId = trim($v['b_MemId']);
				$b_SMemId = trim($v['b_SMemId']);
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

		  $b_MemId = $du_udev['id'];
		  $b_SMemId = $du_udev['msid']; //회원고유아이디
		  $b_Name =   $du_udev['nickNm'];
		  $b_Chk = "N";
		  $b_Not = "";
		  $b_Hide = "";

	}


	$qstr = "board_id=".urlencode($board_id)."&amp;b_Part=".urlencode($b_Part);
	
	include "boardHead.php";  //게시판 헤더

?>

<script type="text/javascript">
	//primary
	function FormCheck() {
	   var message, chk;	

	<? if ($b_CateChk == "Y") {  ?>  
		  if($.trim($('#b_Cate option:selected').val()) == '0'){
			  alert("분류를 선택해 주세요!");
			  $('#b_Cate').focus();
			  return;
		  } 
	<? }  ?>

		if ($.trim($('#b_Title').val()) == '') {
		  alert("제목을 입력해 주세요!");
		  $('#b_Title').focus();
		  return;
		}

		if ($.trim($('#b_Content').val()) == '') {
			alert("내용을 입력해 주세요!");
		   $('#b_Content').focus();
		   return;
		 }

		$("#theForm").submit();

	}

	<? if ($du_udev['lv'] == '0' || $du_udev['lv'] == '1') {   //파일 삭제 ?>
	/********************************
	파일 삭제 
	********************************/
	function chkFDel(bNidx, fidx){
	  var boardId = $.trim($('#board_id').val());

	  //삭제시작
		  if(!confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?"))   {
			   return;                                      
		  }  else {
				var action = "/board/boardProc.php";
				$.ajax({
					type: "POST",
					url: action,
					data: 'mode=fileDel&board_id='+boardId+'&bNidx='+bNidx+'&bFidx='+fidx,
					success: function(response) {
						if(response = 'success') {
							alert("삭제되었습니다.");
							window.location.reload();
							//$('#file'+fidx).load('"/board/boardReg.php?board_id='+boardId+'&bNidx='+bNidx+'&bFidx='+fidx+'&mode=M #file'+fidx); 
						} else if(response == 'error') {
							alert("에러입니다. 관리자에게 문의해 주세요.");
						//	$("form:first").submit();
						}
					}
				});

		  }
		 //삭제끝
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
		
			<div class="du01">
				
			<form name="theForm" id="theForm" action="boardProc.php" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
			<input type="hidden" name="b_MemId" id="b_MemId" value="<?=$b_MemId?>">	
			<input type="hidden" name="b_SMemId" id="b_SMemId" value="<?=$b_SMemId?>">	
			<input type="hidden" name="b_Name" id="b_Name" value="<?=$b_Name?>">	
			<input type="hidden" name="board_id" id="board_id" value="<?=$board_id?>">	
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

					<? if ($b_CateChk == "Y") {  //카테고리 사용여부?> 		
					<li>
						<!-- 카테고리 옵션 -->
						<div class="float_l">
						<? 
							$b_CateName = "분류 선택&".$b_CateName;
							$chk = explode("&",$b_CateName);
						?>			
						<select name="b_Cate" id="b_Cate" title="카테고리">
							<!--<option value="">분류선택</option> -->
							<? foreach($chk as $k=>$v):?>
								<option value="<?=$k;?>" <? if ($mode == "mod" || $mode == "rep") { ?><? if ( $k == $b_Cate ) { ?>selected="selected"<? } }?>><?=$v?></option>
							<? endforeach;?>
						</select>	
					</div>
					<? } ?>		


					<?
					if ($mode != "rep") { //답변일 경우엔 공지사항 노출 제어함.
					   if ($b_Type == "1") {  //공지사항 사용?> 

							<li>
								<!-- 공지 체크 -->
								<div>
								<p>
								<label><input type="checkbox"  name="b_Not" id="b_Not" value="Y" <? if($b_Not == "Y") { ?>checked="checked"<? } ?>> 공지</label>
								</p>
								</div>
							</li>

					<? }
					 }
					?>

					<li class="title">
						<input type="text" id="b_Title" name="b_Title" maxlength="350" placeholder="제목을 입력해주세요." value="<?= $b_Title?>" />						
					</li>
					
					<li class="m_content">					
						<textarea id="b_Content" name="b_Content" placeholder="내용을 입력하세요."><?=$b_Content?></textarea>
					</li>
		
					
					<?
						 $file_count = $b_UploadCnt; 

						 if ($b_UploadCnt > "0") {  //이미지 사용
							 for ($i=0; $i<$file_count; $i++) { 
	 							$cimg = $i + 1;

					?>

						<li class="file" >					
							<div class="file_l">
								<input type="file" name="files[]" id="files_<?php echo $i+1 ?>" >
							</div>

					<?
								 
								 # 파일첨부  조회
								 $bFileQuery = "";
								 $bFileQuery = " SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize FROM TB_BOARD_FILE  WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx AND b_FIdx = :b_FIdx ";
								 $bFileQuery .= " ORDER BY b_FIdx ASC";
								 $bFileStmt = $DB_con->prepare($bFileQuery);
								 $bFileStmt->bindparam(":b_Idx",$b_Idx);
								 $bFileStmt->bindparam(":b_NIdx",$idx);
								 $bFileStmt->bindparam(":b_FIdx",$cimg);
								 $bFileStmt->execute();
								 $bFileNum = $bFileStmt->rowCount();

								if($bFileNum < 1)  { //아닐경우
								} else {
									while($f =$bFileStmt->fetch(PDO::FETCH_ASSOC)) {

										   if ($bFileNum % 3 > "1") {
											$chkBr = "";
										   } else {
											$chkBr = "<BR>";
										   }
										   
										   $b_FName = $f['b_FName'];
										   $b_OFName = $f['b_OFName'];
										   $b_FSize = $f['b_FSize'];
										   $b_FIdx = $f['b_FIdx'];
								?>
									<div class="file_r" id= "file<?=$cimg?>">
										<p>
											<span class="file"><?=$b_OFName?></span>
											<? if ($du_udev['lv'] == '0' || $du_udev['lv'] == '1') {   //파일 삭제 ?>
											<span class="btn red" onClick="javascript:chkFDel('<?=$idx?>','<?=$b_FIdx?>', 'file')">X</span>
											<? } ?>
										</p>
									</div>
								<? 
									}
								 }
								 
								 ?>
					</li>


					<? 
							} 
						 } 
					 ?>

					<? if ( $board_id == "2" ) { //문의하기 ?>
					<li>					
						<p class="center">
						<label for="b_Chk">문의 내용 여부</label>
						<input type="radio" name="b_Chk" value="Y" id="b_Chk" <?=($b_Chk == "Y" )?"checked":"";?> />
						<label for="b_Chk">사용</label>
						<input type="radio" name="b_Chk" value="N" id="b_Chk" <?=($b_Chk == "N")?"checked":"";?> />
						<label for="b_Chk">사용안함</label>	
						</p>	
					</li>
				  <? } ?>

					<li class="bottom">
						<div class="center">
						<p>
						<span class="btn gray" onclick="javascript:history.back();">취소</span>
						<span class="btn blue" onclick="FormCheck();"><?=$btnNm?></span>
						</p>
						</div>
					</li>
				</ul>
			</div>
			
			
			
			
        </div>
    </content>

</body>
</html>