<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$DB_con = db1();
	
	if($mode=="mod") {
		$titNm = "신고코드관리";

		$query = "";
		$query = "SELECT idx, code_Div, code_Sub_Div, code, code_Name, use_Bit, use_guest_Bit, reg_Date FROM TB_CONFIG_CODE WHERE idx = :idx AND code_Div = 'report'" ;
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":idx",$idx);
		//$idx = trim($idx);
		$stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $idx =  trim($row['idx']);
        $code_Div = trim($row['code_Div']);
		$code_Sub_Div = trim($row['code_Sub_Div']);
        $code = trim($row['code']);
        $code_Name = trim($row['code_Name']);
        $use_Bit = trim($row['use_Bit']);
        $reg_Date = trim($row['reg_Date']);

	} else {
		$mode = "reg";
		$titNm = "신고코드관리";

	}

	
	dbClose($DB_con);
	$stmt = null;
	$mstmt = null;

	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="report_proc.php" onsubmit="return fubmit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="qstr" id="qstr"  value="<?=$qstr?>">
		<input type="hidden" name="page"  id="page"  value="<?=$page?>">
		<input type="hidden" name="use_guest_Bit"  id="use_guest_Bit"  value="0">

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
				<th scope="row"><label for="code_Name">신고사유</label></th>
				<td><input type="text" name="code_Name" id="code_Name" class="frm_input" size="50" maxlength="100" value="<?=$code_Name?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="use_Bit">사용여부</label></th>
				<td>
					<input type="radio" name="use_Bit" value="0" id="use_Bit" <?=($use_Bit == "0" )?"checked":"";?> required class="required" checked/>
					<label for="use_Bit">사용</label>
					<input type="radio" name="use_Bit" value="1" id="use_Bit" <?=($use_Bit == "1")?"checked":"";?> required class="required" />
					<label for="use_Bit">사용안함</label>		
				</td>
			</tr>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="code_list.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>


		<script>
		function fubmit(f) {
			if (!f.mb_img.value.match(/\.(gif|jpe?g|png)$/i) && f.mb_img.value) {
			  alert('회원등급이미지는 이미지 파일만 가능합니다.');
			  return false;
			}
			
			return true;
		}
		</script>

	</div>    


<?

	include "../common/inc/inc_footer.php";  //푸터 
	 
?>
