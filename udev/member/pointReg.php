<?
	$menu = "2";
	$smenu = "4";

	include "../common/inc/inc_header.php";  //헤더 
	
	$DB_con = db1();
	
	if($mode == "reg")
	{
		$titNm = "캐시등록";
	}
	

	//회원레벨
	$query = "select * from TB_MEMBER_LEVEL order by memLv desc";
	$stmt = $DB_con->prepare($query);
	$stmt->execute();
	


	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script>
//접속일자 기준 회원수 구하기
function getUserCount(val) {
	var memLv = val;

	$.ajax({
			url: "./get_user_count.php",
			data:"memLv="+memLv,
			type: "post",
			dataType : "json",
			success: function( data ) {
				$("#user_count").text(data);
			},
			error: function( xhr, status ) { 
				$("#progressbar").hide(); alert("웹서버의 응답이 없습니다. 다시 시도하여 주십시오."); 
			},
			complete: function( xhr, status ) { }
	});
}


function delayCheck() {
	if($("#setDelay").is(":checked")) {
		$(".delay").show();
	} else {
		$(".delay").hide();
		$("#send_date").val("");
	}
}


//회원선택에 따른 세부선택
function chkMem(val) {

	if(val == "level")
	{
		$("#mType1").show();
		$("#mType2").hide();
	}
	else if(val == "pub")
	{
		$("#mType1").hide();
		$("#mType2").show();
	}
	else
	{
		$("#mType1").hide();
		$("#mType2").hide();
	}
}


//pupup
function popup() {

	var sw=800;    //띄울 창의 넓이
	var sh=600;    //띄울 창의 높이

	var url = arguments[0];
	var width = arguments[1];
	var height = arguments[2];


	var cw=screen.availWidth;     //화면 넓이
	var ch=screen.availHeight;    //화면 높이


	 if(width > 0) {
		var sw=width;
	 }

	 if(height > 0) {
		var sh=height;
	 }


	 var ml=(cw-sw)/2;        //가운데 띄우기위한 창의 x위치
	 var mt=(ch-sh)/2;         //가운데 띄우기위한 창의 y위치

	 var urlList = url.split("/");
	 var page = urlList[urlList.length-1].split(".");

	window.open(url, page[0], 'toolbar=no, status=no, directories=no, scrollbars=yes, location=no, resizable=yes, border=0, menubar=no, left=' + ml + ', top=' + mt + ', width=' + sw + ', height=' + sh);
}

function setMemId(uid)
{
	//alert(uid);
	$('#taxi_MemId').val(uid);
}


function setReg()
{
	var type = "<?= $taxi_MemTeype ?>";
	
	if(type == "level")
	{
		$("#mType1").show();
		$("#mType2").hide();
	}
	else if(type == "pub")
	{
		$("#mType1").hide();
		$("#mType2").show();
	}
	else
	{
		$("#mType1").hide();
		$("#mType2").hide();
	}

}

// loading시 실행
window.onload=function(){ 
	setReg();
}

</script>
<style>
/* 예약하기  layer*/
.objects select {margin-left:10px; float:left;}
.objects input {margin-left:10px; float:left;}
.objects input[type=radio] {margin-left:10px; margin-top:2px; float:left;}
.objects input[type=checkbox] {margin-left:10px; margin-top:2px; float:left;}
.objects textarea {margin-left:10px; float:left;}
.objects span {margin-left:10px; float:left;}
.objects i {margin-left:10px; float:left;}
.objects div {margin-left:10px; float:left;}
.objects img {margin-left:10px; float:left;}
/* 예약라기 기본 선택 */
.delay {display:block;}


.PT5 {line-height:35px;}
</style>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="pointProc.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="mem_Id" id="mem_Id" value="<?=$mem_Id?>">
		<input type="hidden" name="qstr" id="qstr"  value="<?=$qstr?>">
		<input type="hidden" name="page"  id="page"  value="<?=$page?>">
		<input type="hidden" name="taxi_PState" id="taxi_PState" value="6"><!-- 관리자에 의한 적립/차감 -->

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<caption><?=$titNm?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
			<tr height="45">
				<th align="center">회원아이디</th>
				<td class="objects">
					<select name="taxi_MemTeype" id="taxi_MemTeype" onchange="chkMem(this.value)">
						<option value="ALL" <? if ($taxi_MemTeype == "ALL" || $taxi_MemTeype==""){?>selected<?}?>>전체</option>
						<option value="level"<? if ($taxi_MemTeype == "level"){?>selected<?}?>>등급별</option>
						<option value="pub"<? if ($taxi_MemTeype == "pub"){?>selected<?}?>>개별</option>
					</select>
					<div id="mType1" style="display:none">
						<select name="taxi_MemLevel" id="taxi_MemLevel" style="width:200px" onchange="getUserCount(this.value)">
							<option value="">전체</option>
						<?
						while($row =$stmt->fetch()) {
							echo "<option value='".$row['memLv']."' >".$row['memLv_Name']."</option>";
						}
						?>
						</select><span id="user_count" class="PT5"></span>
					</div>
					<div id="mType2" style="display:none;width:500px;">
						<input type="text" class="frm_input" name="taxi_MemId" id="taxi_MemId" style="width:60%;" placeholder="캐시 적립(차감) 회원아이디" readonly onclick="popup('pointIdSearch.php', 600, 400)" value="<?= $id?>">&nbsp;<a href="javascript:popup('pointIdSearch.php', 600, 400)" class="btn btn_02">회원아이디 검색</a>
					</div>					
				</td>
			</tr>
			<tr height="45">
				<th align="center">적립(차감)캐시</th>
				<td class="objects">
					<input type="text" class="frm_input" name="taxi_OrdPoint" id="taxi_OrdPoint" style="width:95%;" placeholder="적립(차감) 캐시">
				</td>
			</tr>		
			<tr height="45">
				<th align="center">적립(차감)캐시 메모</th>
				<td class="objects">
					<input type="text" class="frm_input" name="taxi_Memo" id="taxi_Memo" style="width:95%; " placeholder="캐시적립(차감) 사유 ">
				</td>
			</tr>
			<tr height="45">
				<th align="center">캐시 적립구분</th>
				<td class="objects">
					<select name="taxi_Sign" id="taxi_Sign" class="selectbox">
						<option value="">선택</option>
						<option value="0">적립</option>
						<option value="1">차감</option>
					</select>
				</td>
			</tr>			
		</table>

		<div class="btn_fixed_top">
			<a href="pointList.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>


		<script>
		function fmember_submit(f)
		{
			if (!f.taxi_OrdPoint.value) {
				alert('캐시 적립(차감)할 캐시를 입력해주세요.');
				return false;
			}
			if (!f.taxi_Sign.value) {
				alert('캐시 적립구분을 선택해주세요.');
				return false;
			}

			return true;
		}
		</script>

	</div>   
</div>   	


<script>
	$(function(){
		$("#send_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true,  minDate:"-0d"});
	});

</script>



<?
	dbClose($DB_con);
	$stmt = null;
	$meInfoStmt = null;
	$mEtcStmt = null;
	$mstmt = null;

	include "../common/inc/inc_footer.php";  //푸터 
	 
?>
