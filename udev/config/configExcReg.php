<?
	$menu = "1";
	$smenu = "7";

	include "../common/inc/inc_header.php";  //헤더 

	$titNm = "캐시환전 환경 설정";

	$DB_con = db1();
	
	$query = "";
	$query = "SELECT idx, con_Price1,  con_Price2, con_Price3, con_Tax  FROM TB_CONFIG_EXC LIMIT 1" ;
	$stmt = $DB_con->prepare($query);
	$stmt->execute();

	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$idx = trim($row['idx']);
	$con_Price1 =  trim($row['con_Price1']);
	$con_Price2 = trim($row['con_Price2']);
	$con_Price3 = trim($row['con_Price3']);
	$con_Tax = trim($row['con_Tax']);

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
		<form name="fmember" id="fmember" action="configExcProc.php" onsubmit="return f_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="conPrice1">1단계 금액</label></th>
				<td colspan="3"><input type="text" name="conPrice1" id="conPrice1" class="frm_input" size="30" maxlength="20" value="<?=number_format($con_Price1)?>"> 원</td>
			</tr>
			<tr>
				<th scope="row"><label for="conPrice2">2단계 금액</label></th>
				<td colspan="3"><input type="text" name="conPrice2" id="conPrice2" class="frm_input" size="30" maxlength="20" value="<?=number_format($con_Price2)?>"> 원</td>
			</tr>
			<tr>
				<th scope="row"><label for="conPrice3">3단계 금액</label></th>
				<td colspan="3"><input type="text" name="conPrice3" id="conPrice3" class="frm_input" size="30" maxlength="20" value="<?=number_format($con_Price3)?>"> 원</td>
			</tr>
			<tr>
				<th scope="row"><label for="conTax">환전 수수료</label></th>
				<td colspan="3"><input type="text" name="conTax" id="conTax" class="frm_input" size="30" maxlength="20" value="<?=number_format($con_Tax)?>"> 원</td>
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
