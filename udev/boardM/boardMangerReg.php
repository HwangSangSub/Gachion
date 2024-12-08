
<?
	$menu = "6";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	if ( $du_udev[lv] == 0 ) { //최고권한관리자 
		$titNm = "게시판 환경 설정 관리";
	} else {
		$titNm = "게시판 관리";
	}
		

	if($mode=="mod") {
		$titNm = $titNm. " 수정";


		$DB_con = db1();

		$query = "";
		$query = "SELECT b_Title, b_Upload, b_Type, b_Width, b_CateName, b_CateChk, b_TitCnt, b_PageCnt, b_NewIcon, b_ItemChk, b_UploadCnt, b_PwdChk, b_EmailChk, b_RepChk, b_EmailChk, b_RepChk, b_CommentChk, b_Disply, b_ListLv, b_ViewLv, b_WriteLv, b_RepLv,  b_ComentLv, b_EditChk, b_WriteP, b_ComWriteP FROM  TB_BOARD_SET  WHERE b_Idx = :idx LIMIT 1";
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":idx",$idx);
		$stmt->execute();
		$bNum = $stmt->rowCount();

		if($bNum < 1)  { //아닐경우
			$message = "잘못된 접근 방식입니다.";
			proc_msg3($message);
		} else {
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$b_Title = trim($row['b_Title']);
				$b_Upload = trim($row['b_Upload']);
				$b_Type = trim($row['b_Type']);
				$b_Width = trim($row['b_Width']);
				$b_CateName = trim($row['b_CateName']);
				$b_CateChk = trim($row['b_CateChk']);
				$b_TitCnt = trim($row['b_TitCnt']);
				$b_PageCnt = trim($row['b_PageCnt']);
				$b_NewIcon = trim($row['b_NewIcon']);
				$b_ItemChk = trim($row['b_ItemChk']);
				$b_UploadCnt = trim($row['b_UploadCnt']);
				$b_PwdChk = trim($row['b_PwdChk']);
				$b_EmailChk = trim($row['b_EmailChk']);
				$b_RepChk = trim($row['b_RepChk']);
				$b_CommentChk = trim($row['b_CommentChk']);
				$b_Disply = trim($row['b_Disply']);
				$b_ListLv = trim($row['b_ListLv']);
				$b_ViewLv = trim($row['b_ViewLv']);
				$b_WriteLv = trim($row['b_WriteLv']);
				$b_RepLv = trim($row['b_RepLv']);
				$b_EditChk = trim($row['b_EditChk']);
				$b_WriteP = trim($row['b_WriteP']);
				$b_ComWriteP = trim($row['b_ComWriteP']);		
			}
		}


	} else {
		$mode = "reg";
		$titNm = $titNm. " 등록";
	}

	$qstr = "b_Type=".urlencode($b_Type)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="theForm" id="theForm" action="boardManagerProc.php" onsubmit="return fchk_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="qstr" id="qstr"  value="<?=$qstr?>">
		<input type="hidden" name="page"  id="page"  value="<?=$page?>">

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<caption><?=$titNm?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
			<tr>
				<th scope="row"><label for="b_Title">게시판명<strong class="sound_only">필수</strong></label></th>
				<td><input type="text" name="b_Title" value="<?=$b_Title?>" id="b_Title" required class="frm_input required" size="50"  maxlength="20"></td>
				<th scope="row"><label for="b_Upload">게시판폴더명<strong class="sound_only">필수</strong></label></th>
				<td>
					<span class="frm_info">  <strong> 폴더명은 영문명으로 적어주세요. ex) notice</strong></span>    
					<input type="text" name="b_Upload" value="<?=$b_Upload?>" id="id" required class="frm_input" size="50"  maxlength="20">
					<span class="frm_info">  <strong> 파일첨부가 필요하지 않은 게시판은 폴더명은 생략해도 됩니다.</strong></span>    
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="b_Type">게시판타입</label></th>
				<td>
					<? 
						$select_array = array(
							'1' => '일반게시판',
							'2' => '갤러리게시판',
							'3' => '웹진게시판',
							'4' => 'FAQ게시판',
							'5' => '기타게시판',
							'6' => '온라인상담게시판'
						);
					?>
					<select id="b_Type" name="b_Type" required class="required frm_input">
						<option value="">게시판타입선택</option>
						<? foreach($select_array as $k=>$v):?>
							<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $b_Type ) { ?>selected="selected"<? } }?>><? echo $v?></option>
						<? endforeach;?>
					</select>
				</td>
				<th scope="row"><label for="b_Width">게시판넓이</label></th>
				<td><input type="text" name="b_Width" value="<?=$b_Width?>" id="b_Width" required class="required frm_input" size="10"  maxlength="3"></td>
			</tr>

			
			<tr>
				<th scope="row"><label for="b_CateChk">카테고리사용</label></th>
				<td  colspan="3">
					<input type="radio" name="b_CateChk" value="Y" id="b_CateChk" <?=($b_CateChk == "Y" )?"checked":"";?> onclick="useSubSpeechChk(0);" />
					<label for="b_PwdChk">사용</label>
					<input type="radio" name="b_CateChk" value="N" id="b_CateChk" <?=($b_CateChk == "N")?"checked":"";?> onclick="useSubSpeechChk(1);" />
					<label for="mem_Sex">사용안함</label>			
				</td>
			</tr>
			<tr id="subSpeechWrite" <? if ( $b_CateChk  == "N") { ?> style="display:none"<? }?>>
				<th scope="row"><label for="b_CateChk">말머리기능</label></th>
				<td colspan="3">
					<textarea name="b_CateName" id="b_CateName"><?=$b_CateName?></textarea>
					<span class="frm_info">  <strong> &로 구분해서 등록해 주세요. - ex)카테고리선택&카테고리1&카테고리2</strong></span>    
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="b_TitCnt">제목 글자수</label></th>
				<td><input type="text" name="b_TitCnt" value="<?=$b_TitCnt?>" id="b_TitCnt" required class="required frm_input" size="10"  maxlength="3"></td>
				<th scope="row"><label for="b_PageCnt">페이지 수</label></th>
				<td><input type="text" name="b_PageCnt" value="<?=$b_PageCnt?>" id="b_PageCnt" required class="required frm_input" size="10"  maxlength="3"></td>
			</tr>

			<tr>
				<th scope="row">항목감추기</th>
				<td>
					<?
						 if ($mode == "mod") { 
							  $chk=explode("/",$b_ItemChk);
							  for ($i=0; $i < count($chk); $i++) {
								 switch ($chk[$i]) {
									case '1':
										$chked1 = "Y";break;
									 case '2':
										$chked2 = "Y";break;
									case '3':
										$chked3 = "Y";break;
									case '4':
										$chked4 = "Y";break;
									case '5':
										$chked5 = "Y";break;
									case '6':
										$chked6 = "Y";break;
									case '7':
										$chked7 = "Y";break;
								 }
							 }
						 }
					?>

					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="1" <? if (isset($chked1)) :?>checked="checked"<? endif; ?> /> <label for="use01">글번호</label>
					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="2" checked="checked" disabled="disabled" /> <label for="use02">제목</label>
					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="3" <? if (isset($chked3)) :?>checked="checked"<? endif; ?> /> <label for="use03">작성자</label>
					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="4" <? if (isset($chked4)) :?>checked="checked"<? endif; ?> /> <label for="use04">첨부파일</label>
					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="5" <? if (isset($chked5)) :?>checked="checked"<? endif; ?> /> <label for="use05">날짜</label>
					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="6" <? if (isset($chked6)) :?>checked="checked"<? endif; ?> /> <label for="use06">조회수</label>
					<input type="checkbox" name="b_ItemChk" id="b_ItemChk" value="7" <? if (isset($chked7)) :?>checked="checked"<? endif; ?> /> <label for="use07">상태</label>
				</td>

				<th scope="row"><label for="b_NewIcon">NEW 아이콘 시간</label></th>
				<td><input type="text" name="b_NewIcon" value="<?=$b_NewIcon?>" id="b_NewIcon" required class="required frm_input" size="10"  maxlength="3"> 시간</td>
			</tr>
			<tr>
				<th scope="row"><label for="b_UploadCnt">파일 업로드 수</label></th>
				<td><input type="text" name="b_UploadCnt" value="<?=$b_UploadCnt?>" id="b_UploadCnt" required class="frm_input" size="10"  maxlength="3"> 개</td>
				<th scope="row"><label for="b_PwdChk">비밀글 기능</label></th>
				<td>
					<input type="radio" name="b_PwdChk" value="Y" id="b_PwdChk" <?=($b_PwdChk == "Y" )?"checked":"";?> />
					<label for="b_PwdChk">사용</label>
					<input type="radio" name="b_PwdChk" value="N" id="b_PwdChk" <?=($b_PwdChk == "N")?"checked":"";?> />
					<label for="mem_Sex">사용안함</label>					
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="b_EmailChk">이메일 사용</label></th>
				<td scope="row">
					<input type="radio" name="b_EmailChk" value="Y" id="b_EmailChk" <?=($b_EmailChk == "Y" )?"checked":"";?> />
					<label for="b_EmailChk">사용</label>
					<input type="radio" name="b_EmailChk" value="N" id="b_EmailChk" <?=($b_EmailChk == "N")?"checked":"";?> />
					<label for="b_EmailChk">사용안함</label>			
				</td>
				<th scope="row"><label for="b_RepChk">답변글쓰기 사용</label></th>
				<td>
					<input type="radio" name="b_RepChk" value="Y" id="b_RepChk" <?=($b_RepChk == "Y" )?"checked":"";?> />
					<label for="b_RepChk">사용</label>
					<input type="radio" name="b_RepChk" value="N" id="b_RepChk" <?=($b_RepChk == "N")?"checked":"";?> />
					<label for="b_RepChk">사용안함</label>			
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="b_CommentChk">코멘트 사용</label></th>
				<td>
					<input type="radio" name="b_CommentChk" value="Y" id="b_CommentChk" <?=($b_CommentChk == "Y" )?"checked":"";?> />
					<label for="b_CommentChk">사용</label>
					<input type="radio" name="b_CommentChk" value="N" id="b_CommentChk" <?=($b_CommentChk == "N")?"checked":"";?> />
					<label for="b_CommentChk">사용안함</label>			
				</td>
				<th scope="row"><label for="b_EditChk">에디터 사용</label></th>
				<td scope="row">
					<input type="radio" name="b_EditChk" value="Y" id="b_EditChk" <?=($b_EditChk == "Y" )?"checked":"";?> />
					<label for="b_EditChk">사용</label>
					<input type="radio" name="b_EditChk" value="N" id="b_EditChk" <?=($b_EditChk == "N")?"checked":"";?> />
					<label for="b_EditChk">사용안함</label>			
				</td>
			</tr>


			<tr>
				<th scope="row"><label for="b_CateChk">권한설정</label></th>
				<td  colspan="3">
					<div class="tbl_frm01 tbl_wrap">
						<table>
						<caption><?=$titNm?></caption>
						<colgroup>
							<col class="grid_4">
							<col>
							<col class="grid_4">
							<col>
						</colgroup>
						<tbody>
								<tr>
									<th height="30px">* 리스트보기 권한</th>
									<th>글내용보기 권한</th>
									<th>글쓰기 권한</th>
									<th>글답변쓰기 권한</th>
									<th>글코멘트달기 권한</th>
								</tr>
								<tr>
									<td>
										<? 
											$select_array = array(
												'1' => '관리자',
												'2' => '2레벨',
												'3' => '3레벨',
												'4' => '4레벨',
												'5' => '5레벨',
												'6' => '6레벨',
												'7' => '7레벨',
												'8' => '8레벨',
												'9' => '9레벨',
												'10' => '10레벨',
												'11' => '비회원'
											);
										?>	  
										<select id="b_ListLv" name="b_ListLv" required class="required">
											<option value="">리스트보기 권한 선택</option>
											<? foreach($select_array as $k=>$v):?>
												<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $b_ListLv ) { ?>selected="selected"<? } }?>><? echo $v?></option>
											<? endforeach;?>
										</select>
									 </td>
									<td>
										<select id="b_ViewLv" name="b_ViewLv" class="selectBox">
											<option value="">글내용보기 권한 선택</option>
											<? foreach($select_array as $k=>$v):?>
												<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $b_ViewLv ) { ?>selected="selected"<? } }?>><? echo $v?></option>
											<? endforeach;?>
										</select>
									 </td>
									<td>
										<select id="b_WriteLv" name="b_WriteLv" class="selectBox">
											<option value="">글쓰기 권한 선택</option>
											<? foreach($select_array as $k=>$v):?>
												<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $b_WriteLv ) { ?>selected="selected"<? } }?>><? echo $v?></option>
											<? endforeach;?>
										</select>
									</td>
									<td>
										<select id="b_RepLv" name="b_RepLv" class="selectBox">
											<option value="">글답변쓰기 권한 선택</option>
											<? foreach($select_array as $k=>$v):?>
												<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $b_RepLv ) { ?>selected="selected"<? } }?>><? echo $v?></option>
											<? endforeach;?>
										</select>
									 </td>
									<td>
										<select id="b_ComentLv" name="b_ComentLv" class="selectBox">
											<option value="">글코멘트달기 권한 선택</option>
											<? foreach($select_array as $k=>$v):?>
												<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $b_ComentLv ) { ?>selected="selected"<? } }?>><? echo $v?></option>
											<? endforeach;?>
										</select>
									 </td>
								</tr>
							</tbody>
							</table>
						</div>

						</td>
					</tr>

			<tr>
				<th scope="row"><label for="b_WriteP">글쓰기 포인트</label></th>
				<td><input type="text" name="b_WriteP" value="<?=$b_WriteP?>" id="b_WriteP" required class="required frm_input" size="10"  maxlength="3"></td>
				<th scope="row"><label for="b_ComWriteP">코멘트 글쓰기 포인트</label></th>
				<td><input type="text" name="b_ComWriteP" value="<?=$b_ComWriteP?>" id="b_ComWriteP" required class="required frm_input" size="10"  maxlength="3"></td>
			</tr>

			<tr>
				<th scope="row"><label for="b_Disply">게시판 사용</label></th>
				<td colspan="3">
					<input type="radio" name="b_Disply" value="Y" id="b_Disply" <?=($b_Disply == "Y" )?"checked":"";?> />
					<label for="b_Disply">사용</label>
					<input type="radio" name="b_Disply" value="N" id="b_Disply" <?=($b_Disply == "N")?"checked":"";?> />
					<label for="b_Disply">사용안함</label>			
				</td>
			</tr>



			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="boardManagerList.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>


	<script>

		function fchk_submit(f)		{

			//항목값 체크 값 받기 위해서 추가
			var chk = $("input:checkbox[id='b_ItemChk']").is(":checked");
			if( chk == true ) {
				var idlist = [];
				var b_ItemChk ="";
				$('input[name=b_ItemChk]:checked').each(function(){idlist.push(this.value)});
			 
				$.each(idlist,function(index, item){
				b_ItemChk += item + '/';
				});
			}

			var cut1AltVal = $('<input type="hidden" id="cut1AltVal" name="cut1AltVal" value="' + b_ItemChk + '" />');
			cut1AltVal.appendTo($("#theForm"));



			return true;
		}

		$(document).ready(function() {
			<? if  ($b_CateChk == "Y" ) { ?>
				$("#subSpeechWrite").show();
			<? } else { ?>
				$("#subSpeechWrite").hide();
			<? } ?>

			// radio change 이벤트
			$("input[name=b_CateChk]").change(function() {
				var radioValue = $(this).val();


				if (radioValue == "Y") {
					$("#subSpeechWrite").show();
				} else if (radioValue == "N") {
					$("#subSpeechWrite").hide();
					$("#b_CateName").val('');         //값 초기화
				}
			});
		})


		</script>

	</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>



