<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더
	include "../common/inc/inc_mir.php";		//미르페이  
	
	$DB_con = db1();
	
	if($mode == "mod") {
		$titNm = "회원 수정";

		$query = "";
		$query = "
			SELECT idx,
				mem_Id,
				mem_Nm,
				mem_NickNm,
				mem_Tel,
				mem_Email,
				login_Date,
				reg_Date
			FROM 
				TB_MEMBERS 
			WHERE mem_id = :id" ;	
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
    		$mem_Nm = trim($row['mem_Nm']);
    		$mem_NickNm = trim($row['mem_NickNm']);
    		$mem_Tel = trim($row['mem_Tel']);
    		$memEmail = trim($row['mem_Email']);
			if($memEmail){
				$mem_Email = '등록필요';
			}else{
				$mem_Email = $memEmail;
			}
    		$loginDate = trim($row['login_Date']);
			if($loginDate == ''){
				$login_Date = '-';
			}else{
				$login_Date = $loginDate;
			}
    		$reg_Date =  trim($row['reg_Date']);
    		$use_Date =  trim($row['use_Date']);
    		$b_Disply = $row['b_Disply'];
    		
		  }
	   }

	} else {
	    $titNm = "회원 등록";
	}


	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="memberProc.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
					<input type="text" name="id" value="" id="id" required class="frm_input required" size="50"  maxlength="20">
				</td>
				<th scope="row"><label for="memPwd">비밀번호<strong class="sound_only">필수</strong></label></th>
				<td><input type="password" name="memPwd" id="memPwd" required class="frm_input required" size="50" maxlength="20"></td>
			<? } ?>
			</tr>
			<tr>
				<th scope="row"><label for="mem_Nm">회원닉네임<strong class="sound_only">필수</strong></label></th>
				<td><?=$mem_NickNm?></td>
				<th scope="row"><label for="login_Date">최근접속일</label></th>
				<td><?=$login_Date?></td>
			</tr>
			<tr>
				<th scope="row"><label for="mem_Tel">휴대폰번호<strong class="sound_only">필수</strong></label></th>
				<td><input type="text" name="mem_Tel" value="<?=$mem_Tel?>" id="mem_Tel" required class="required frm_input" size="50"  maxlength="20"></td>
				<th scope="row"><label for="mem_Email">이메일</label></th>
				<td><input type="text" name="mem_Email" value="<?=$mem_Email?>" id="mem_Email" required class="required frm_input" size="50"  maxlength="20"></td>
			</tr>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="memberList.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
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
