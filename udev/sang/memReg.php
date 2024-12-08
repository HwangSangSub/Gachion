<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 
	$loginId =  $_COOKIE['du_udev']['id'];

	$titNm = "회원등록";
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 
	$DB_con = db1();
	//회원등급
	/*$mquery = "";
	$mquery = "SELECT memLv, memLv_Name FROM TB_MEMBER_LEVEL WHERE 1 = 1 ORDER BY memLv ASC" ;
	$mstmt = $DB_con->prepare($mquery);
	$mstmt->execute();*/
?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
		
        <div class="container_wr">
		<form name="fmember" id="fmember" action="memRegProc.php" onsubmit="return f_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="login_Id" id="login_Id"  value="<?=$loginId?>"/>
		<span style="font-weight:bold;">관리자 등급으로 설정 시 관리자사이트에 로그인이 가능합니다.</span><br>
		<span>계정 사용 가능일을 선택하지 않을 경우 기본 30일간 로그인 가능입니다.</span><br>
		<br>
						
		<div class="tbl_frm01 tbl_wrap">
			<table>
			<caption><?=$titNm?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>	
				<tr>
					<th scope="row"><label for="mem_Id">회원아이디</label></th>
					<td><input type="text" name="mem_Id" id="mem_Id" class="frm_input" size="15" maxlength="20"></td>
					<th scope="row"><label for="memPwd">회원비밀번호</label></th>
					<td><input type="password" name="memPwd" id="memPwd" class="frm_input" size="50" maxlength="20"></td>
				</tr>
				<tr>
					<th scope="row"><label for="mem_Nm">회원이름</label></th>
					<td><input type="text" name="mem_Nm" id="mem_Nm" class="frm_input" size="15" maxlength="20"></td>
					<th scope="row"><label for="mem_Tel">연락처</label></th>
					<td><input type="text" name="mem_Tel" id="mem_Tel" class="frm_input" size="15" maxlength="20"></td>
				</tr>
				<tr>
					<th scope="row"><label for="use_Date">계정 사용 가능일</label></th>
					<td>
						<select id="use_Date" name="use_Date">
							<option value="30">30일</option>
							<option value="60">60일</option>
							<option value="90">90일</option>
						</select>
					</td>
					<th scope="row"><label for="mem_Lv">회원 권한</label></th>
					<td>
						<input type="hidden" name="oldlev"  id="oldlev" value="<?=$mem_Lv?>">
						<select id="mem_Lv" name="mem_Lv">
							<option value="">회원등급선택</option>
							<option value="0">관리자</option>
							<option value="1">일반회원</option>
						</select>
					</td>
				</tr>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>


		<script>

		function f_submit(f) 	{
			return true;
		}
		</script>

	</div>    

<? 
	dbClose($DB_con);
	$mstmt = null;
	include "../common/inc/inc_footer.php";  //푸터
?>
