<?
	$menu = "1";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$DB_con = db1();
	
	if($mode=="mod") {
		$titNm = "각종코드관리";

		$query = "";
		$query = "SELECT idx, code_Div, code_Sub_Div, code, code_Name, code_On_Img, code_Off_Img, code_And_Img, code_Ios_Img, code_Color, use_Bit, use_guest_Bit, reg_Date FROM TB_CONFIG_CODE WHERE idx = :idx AND code_Div <> 'codediv'" ;
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
		$code_On_Img = trim($row['code_On_Img']);
		$code_Off_Img = trim($row['code_Off_Img']);
		$code_And_Img = trim($row['code_And_Img']);
		$code_Ios_Img = trim($row['code_Ios_Img']);
		$code_Color = trim($row['code_Color']);
        $use_Bit = trim($row['use_Bit']);
		$use_guest_Bit = trim($row['use_guest_Bit']);
        $reg_Date = trim($row['reg_Date']);

	} else {
		$mode = "reg";
		$titNm = "각종코드관리";

	}

	
	dbClose($DB_con);
	$stmt = null;
	$mstmt = null;

	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script>
	function select_Div(){
		var code_Div = $('#code_Div').val();
		if(code_Div == 'minicategory' || code_Div == 'categorylist'){
			$('#code_Sub_Div_S').show();
			$('#code_Color').hide();
		}else if(code_Div == 'placeicon'){
			$('#code_Sub_Div_S').show();
			$('#code_Color').show();
		}else{
			$('#code_Sub_Div_S').hide();
			$('#code_Color').hide();
		}
	}
	function sel_Color(color){
		$('#code_sel_Color').val(color);
		$('#sel_Color').css( "background-color", color);
	}
</script>
<style type="text/css">
	#code_Color ul {
		list-style-type: none;
		margin: 0;
		padding: 0;
		width: 200px;
		background-color: #f1f1f1;
	}
	#code_Color li ul{
		list-style-type: none;
		margin: 0;
		padding: 0;
		width: 200px;
		display:none;
		z-index:200;
		left:0px;
		top:38px;
	}
	#code_Color li:hover ul{
		display:block;
	}
	#code_Color li a {
		display: block;
		color: #000;
		padding: 8px 16px;
		text-decoration: none;
		text-align: center;
	}
	#code_Color li a.active {
		background-color: #4CAF50;
		color: white;
	}
	#code_Color li:hover:not(.active) {
		display:block;
		color: white;
	}
	#code_Color li a:hover:not(.active) {
		/*background-color: #555;*/
		color: white;
	}
</style>
<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="code_proc.php" onsubmit="return fubmit(this);" method="post" enctype="multipart/form-data">
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
				<th scope="row"><label for="code_Div">코드구분</label></th>
				<td>
					<select id="code_Div" name="code_Div" onchange="select_Div()">
						<option value="">- 선택 -</option>
						<?
							$query = "SELECT idx, code_Div, code, code_Name FROM TB_CONFIG_CODE WHERE code_Div = 'codediv' AND use_Bit = 0" ;
							$stmt = $DB_con->prepare($query);
							$stmt->execute();
							while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
								$SELECT_LIST .= '<option value="'.$row["code"].'"';
								if($code_Div==$row["code"]){
									$SELECT_LIST .= 'selected';
								}
								$SELECT_LIST .= '>'.$row["code_Name"].'</option>';
							}
							echo $SELECT_LIST;
						?>
					</select>
					<span>코드구분 값을 선택 해주세요.</span>
				</td>
			</tr>
			<tr id="code_Sub_Div_S" style="<?if($code_Div == 'minicategory' || $code_Div == 'categorylist' || $code_Div == 'placeicon'){?><?}else{?>display:none;<?}?>">
				<th scope="row"><label for="code_Sub_Div">서브코드구분</label></th>
				<td>
					<select id="code_Sub_Div" name="code_Sub_Div" onchange="">
						<option value="">- 선택 -</option>
						<?
							$query = "SELECT idx, code_Div, code, code_Name FROM TB_CONFIG_CODE WHERE code_Div = 'category' AND use_Bit = 0" ;
							$stmt = $DB_con->prepare($query);
							$stmt->execute();
							while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
								$SELECT_LIST_SUB .= '<option value="'.$row["code"].'"';
								if($code_Sub_Div==$row["code"]){
									$SELECT_LIST_SUB .= 'selected';
								}
								$SELECT_LIST_SUB .= '>'.$row["code_Name"].'</option>';
							}
							echo $SELECT_LIST_SUB;
						?>
					</select>
					<span>미니카테고리 인 경우 상위 카테고리를 지정해주세요.</span>
				</td>
			</tr>
			<tr id="code_Color" style="<?if($code_Div != 'placeicon'){?>display:none;<?}?>">
				<th scope="row"><label for="code_Color">코드배경색상</label></th>
				<td id="code_Color">
					<div>
						<div id="code_Color" style="float:left;">
							<ul>
								<li style="padding: 8px 16px; text-align:center;position: relative;" class="active"><span>색상선택</span>
									<ul style="position: absolute;" id="list_Color">
										<li style="background-color:#ff625b;"><a href="javascript:;" onclick="sel_Color('#ff625b');"><span style="color:#fff;">#ff625b</span></a></li>
										<li style="background-color:#ff8037;"><a href="javascript:;" onclick="sel_Color('#ff8037');"><span style="color:#fff;">#ff8037</span></a></li>
										<li style="background-color:#ffc31d;"><a href="javascript:;" onclick="sel_Color('#ffc31d');"><span style="color:#fff;">#ffc31d</span></a></li>
										<li style="background-color:#b8db08;"><a href="javascript:;" onclick="sel_Color('#b8db08');"><span style="color:#fff;">#b8db08</span></a></li>
										<li style="background-color:#49be89;"><a href="javascript:;" onclick="sel_Color('#49be89');"><span style="color:#fff;">#49be89</span></a></li>
										<li style="background-color:#41c3bd;"><a href="javascript:;" onclick="sel_Color('#41c3bd');"><span style="color:#fff;">#41c3bd</span></a></li>
										<li style="background-color:#4cb5e7;"><a href="javascript:;" onclick="sel_Color('#4cb5e7');"><span style="color:#fff;">#4cb5e7</span></a></li>
										<li style="background-color:#866ec2;"><a href="javascript:;" onclick="sel_Color('#866ec2');"><span style="color:#fff;">#866ec2</span></a></li>
										<li style="background-color:#f075ac;"><a href="javascript:;" onclick="sel_Color('#f075ac');"><span style="color:#fff;">#f075ac</span></a></li>
										<li style="background-color:#7a4a3a;"><a href="javascript:;" onclick="sel_Color('#7a4a3a');"><span style="color:#fff;">#7a4a3a</span></a></li>
									</ul>
								</li>
							</ul>
						</div>
						<div style="float:left;">
							<ul>
							  <li style="background-color:<?=($code_Color == ""?"#a9aab5":$code_Color)?>;padding: 8px 16px; text-align:center;height:100%;" class="active" id="sel_Color"><span style="color:#fff;">선택한 색상</span></li>
							</ul>
						</div>
					</div>
					<input type="hidden" id="code_sel_Color" name="code_sel_Color" value="" />
					<input type="hidden" id="code_Color" name="code_Color" value="<?=$code_Color?>" />
					<br><br><span>지점아이콘 인 경우 배경색상을 지정해주세요.</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="code_Name">코드명</label></th>
				<td><input type="text" name="code_Name" id="code_Name" class="frm_input" size="15" maxlength="20" value="<?=$code_Name?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="code_On_Img">코드이미지(ON)</label></th>
				<td>
					<input type="file" name="code_On_ImgFile" id="code_On_ImgFile">
					<?
					//BLOB 파일 형태로 저장된 이미지 파일 출력되도록 ------------------- 2019.02.19
					if($code_On_Img){
					?>
					<img src="/udev/admin/data/code_img/photo.php?id=<? echo $code_On_Img?>" style="height:100px">
					<input type="checkbox" id="del_code_On_ImgFile" name="del_code_On_ImgFile" value="1">삭제
					<?
					}
					if($mode=="mod") { ?>
						<input type="hidden" name="code_On_Img" value="<?=$code_On_Img?>">
					<? } ?>
					<BR>
					<span>* 이미지가 한개인 경우 ON이미지에 업로드해주세요.</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="code_Off_Img">코드이미지(OFF)</label></th>
				<td>
					<input type="file" name="code_Off_ImgFile" id="code_Off_ImgFile">
					<?
					//BLOB 파일 형태로 저장된 이미지 파일 출력되도록 ------------------- 2019.02.19
					if($code_Off_Img){
					?>
					<img src="/udev/admin/data/code_img/photo.php?id=<? echo $code_Off_Img?>" style="height:100px">
					<input type="checkbox" id="del_code_Off_ImgFile" name="del_code_Off_ImgFile" value="1">삭제
					<?
					}
					if($mode=="mod") { ?>
						<input type="hidden" name="code_Off_Img" value="<?=$code_Off_Img?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="code_And_Img">코드이미지(AND)</label></th>
				<td>
					<input type="file" name="code_And_ImgFile" id="code_And_ImgFile">
					<?
					//BLOB 파일 형태로 저장된 이미지 파일 출력되도록 ------------------- 2019.02.19
					if($code_And_Img){
					?>
					<img src="/udev/admin/data/code_img/and/photo.php?id=<? echo $code_And_Img?>" style="height:100px">
					<input type="checkbox" id="del_code_And_ImgFile" name="del_code_And_ImgFile" value="1">삭제
					<?
					}
					if($mode=="mod") { ?>
						<input type="hidden" name="code_And_Img" value="<?=$code_And_Img?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="code_Ios_Img">코드이미지(IOS)</label></th>
				<td>
					<input type="file" name="code_Ios_ImgFile" id="code_Ios_ImgFile">
					<?
					//BLOB 파일 형태로 저장된 이미지 파일 출력되도록 ------------------- 2019.02.19
					if($code_Ios_Img){
					?>
					<img src="/udev/admin/data/code_img/ios/photo.php?id=<? echo $code_Ios_Img?>" style="height:100px">
					<input type="checkbox" id="del_code_Ios_ImgFile" name="del_code_Ios_ImgFile" value="1">삭제
					<?
					}
					if($mode=="mod") { ?>
						<input type="hidden" name="code_Ios_Img" value="<?=$code_Ios_Img?>">
					<? } ?>
				</td>
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
			<tr>
				<th scope="row"><label for="use_guest_Bit">GUEST사용여부</label></th>
				<td>
					<input type="radio" name="use_guest_Bit" value="0" id="use_guest_Bit" <?=($use_guest_Bit == "0" )?"checked":"";?> required class="required" checked />
					<label for="use_guest_Bit">사용</label>
					<input type="radio" name="use_guest_Bit" value="1" id="use_guest_Bit" <?=($use_guest_Bit == "1")?"checked":"";?> required class="required" />
					<label for="use_guest_Bit">사용안함</label>		
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
