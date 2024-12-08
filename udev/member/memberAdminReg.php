<?
	$menu = "1";
	$smenu = "6";

	include "../common/inc/inc_header.php";  //헤더 
	
	$DB_con = db1();
	
	if($mode == "mod") {
		$titNm = "관리자정보 수정";

		$query = "";
		// 회원정보 가져오기_2019.02.18
		$query = "
			SELECT 
				member.idx, 
				member.mem_Id, 
				member.mem_Pwd, 
				member.mem_Lv, 
				member.mem_NickNm, 
				member.mem_Tel, 
				member.mem_Birth,
				member.mem_ImgFile, 
				member.mem_Code, 
				member.b_Disply,
				member.mem_NPush,
				member.mem_Email
			FROM 
				TB_MEMBERS as member 
			WHERE member.mem_id = :id" ;	
		//echo $query."<BR>";
		//exit;
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		$num = $stmt->rowCount();
		
		if($num < 1)  { //아닐경우
		} else {
		    
		  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    		$idx = trim($row['idx']);
    		$mem_Id =  trim($row['mem_Id']);
    		$mem_Pwd = $row['mem_Pwd'];
    		$mem_Lv = $row['mem_Lv'];
			if($mem_Lv == "0"){
				$mem_Lv_Name = "관리자";
			}
    		$mem_NickNm = trim($row['mem_NickNm']);
    		$mem_Tel = trim($row['mem_Tel']);
    		$mem_ImgFile = $row['mem_ImgFile'];
    		$mem_Code =  trim($row['mem_Code']);
    		$b_Disply = $row['b_Disply'];
			$mem_NPush = $row['mem_NPush'];
			$memEmail = $row['mem_Email'];
		  }
	   }

	} else {
	    $titNm = "관리자정보 등록";
	}

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="memberAdminProc.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="mem_Id" id="mem_Id" value="<?=$mem_Id?>">
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

			<? if($mode=="mod") { ?>
				<th scope="row"><label for="id">아이디</label></th>
				<td>
					<?=$mem_Id?>
					<input type="hidden" name="mem_Id"  id="mem_Id" value="<?=$mem_Id?>">
				</td>
				<th scope="row"><label for="memPwd">비밀번호</label></th>
				<td>
					<input type="password" name="memPwd" id="memPwd" class="frm_input" size="50" maxlength="20">
					<input type="hidden" name="mem_Pwd"  id="mem_Pwd" value="<?=$mem_Pwd?>">

				</td>
			<? } else if($mode=="reg") { ?>
				<th scope="row"><label for="id">아이디<strong class="sound_only">필수</strong></label></th>
				<td>
					<input type="text" name="mem_Id" value="" id="mem_Id" required class="frm_input required" size="50"  maxlength="20">
				</td>
				<th scope="row"><label for="memPwd">비밀번호<strong class="sound_only">필수</strong></label></th>
				<td><input type="password" name="memPwd" id="memPwd" required class="frm_input required" size="50" maxlength="20"></td>
			<? } ?>
			</tr>
			<tr>
				<th scope="row"><label for="mem_NickNm">닉네임<strong class="sound_only">필수</strong></label></th>
				<td><input type="text" name="mem_NickNm" value="<?=$mem_NickNm?>" id="mem_NickNm" required class="required frm_input" size="50"  maxlength="20"></td>
				<th scope="row"><label for="mem_Lv">회원 권한</label></th>
				<td>
					<input type="hidden" name="oldlev"  id="oldlev" value="<?=$mem_Lv?>">
					<?=$mem_Lv_Name?>
				</td>

			</tr>
			<tr>
				<th scope="row"><label for="mem_Birth">생년월일</label></th>
				<td><input type="text" name="mem_Birth" value="<?=$mem_Birth?>" id="mem_Birth" class="frm_input" size="50"  maxlength="20"></td>
				<th scope="row"><label for="mem_Tel">휴대폰번호<strong class="sound_only">필수</strong></label></th>
				<td><input type="text" name="mem_Tel" value="<?=$mem_Tel?>" id="mem_Tel" required class="required frm_input" size="50"  maxlength="20"></td>
			</tr>
			<tr>
				<th scope="row"><label for="mem_Email">이메일</label></th>
				<td scope="row"><input type="text" name="mem_Email" value="<?=$memEmail?>" id="mem_Email" required class="required frm_input" size="50"  maxlength="200"></td>
				<th scope="row"><label for="mem_SnsChk">알림유무</label></th>
				<td scope="row">
					<input type="radio" name="mem_NPush" value="0" id="mem_NPush" <?=($mem_NPush == "0" || !$mem_NPush)?"checked":"";?> >
					<label for="mem_Seat">알림받기</label>
					<input type="radio" name="mem_NPush" value="1" id="mem_NPush" <?=($mem_NPush == "1")?"checked":"";?>>
					<label for="mem_Seat">알림받지 않기</label>		
					</td>		
			</tr>	
			<tr>
				<th scope="row"><label for="mem_Memo">메모</label></th>
				<td colspan="3"><textarea name="mem_Memo" id="mem_Memo"><?=stripslashes($view['mem_Memo']);?></textarea></td>
			</tr>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="memberAdminList.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>


		<script>
		function fmember_submit(f)
		{
			if (!f.mb_img.value.match(/\.(gif|jpe?g|png)$/i) && f.mb_img.value) {
				alert('회원이미지는 이미지 파일만 가능합니다.');
				return false;
			}

			return true;
		}
		</script>

	</div>    

<?
	dbClose($DB_con);
	$stmt = null;
	$meInfoStmt = null;
	$mEtcStmt = null;
	$mstmt = null;

	include "../common/inc/inc_footer.php";  //푸터 
	 
?>
