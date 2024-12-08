<?
	$menu = "0";
	$smenu = "3";

	include "../common/inc/inc_header.php";  //헤더 

	$titNm = "기타 환경 설정";

	$DB_con = db1();
	
	$query = "";
	$query = "SELECT idx, con_ImgUp,  con_TxtFilter, con_Agree, con_Privacy  FROM TB_CONFIG_ETC LIMIT 1" ;
	$stmt = $DB_con->prepare($query);
	$stmt->execute();

	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$idx = trim($row['idx']);
	$con_ImgUp =  trim($row['con_ImgUp']);
	$con_TxtFilter = trim($row['con_TxtFilter']);
	$con_Agree = trim($row['con_Agree']);
	$con_Privacy = trim($row['con_Privacy']);

	if ($idx == "") {
		$mode = "reg";
	} else {
		$mode = "mod";
	}

	dbClose($DB_con);
	$stmt = null;
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="configEtcProc.php" onsubmit="return f_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">

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
				<th scope="row"><label for="conImgUp">이미지 업로드 확장자</label></th>
				<td colspan="3">	<input type="text" name="conImgUp" id="conImgUp" class="frm_input" size="50" maxlength="20" value="<?=$con_ImgUp?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="conTxtFilter">단어 필터링</label></th>
				<td colspan="3"><textarea name="conTxtFilter" id="conTxtFilter"><?=$con_TxtFilter?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label for="conAgree">회원가입약관</label></th>
				<td colspan="3"><textarea name="conAgree" id="conAgree"><?=$con_Agree?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label for="conPrivacy">개인정보취급방침</label></th>
				<td colspan="3"><textarea name="conPrivacy" id="conPrivacy"><?=$con_Privacy?></textarea></td>
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

<? include "../common/inc/inc_footer.php";  //푸터 ?>
