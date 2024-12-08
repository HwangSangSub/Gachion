<?
	$menu = "1";
	$smenu = "4";

	include "../common/inc/inc_header.php";  //헤더 

	$DB_con = db1();
	
	if($mode=="mod") {
		$titNm = "포인트 정책관리 수정";

		$query = "";
		$query = "SELECT idx, point_Title, point_Num FROM TB_CPOINT WHERE idx = :idx" ;
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":idx",$idx);
		//$idx = trim($idx);
		$stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $idx =  trim($row['idx']);
        $point_Title = trim($row['point_Title']);
        $point_Num = $row['point_Num'];

	} else {
		$mode = "reg";
		$titNm = "포인트 정책관리 등록";

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
		<form name="fmember" id="fmember" action="memPointManagerProc.php" onsubmit="return fubmit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="point_Title">포인트제목<strong class="sound_only">필수</strong></label></th>
				<td colspan="3">
					<input type="text" name="point_Title" value="<?=$point_Title?>" id="point_Title" required class="frm_input required" size="150"  maxlength="500">
				</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="point_Num">점수<strong class="sound_only">필수</strong></label></th>
				<td>
					<input type="text" name="point_Num" value="<?=$point_Num?>" id="point_Num" required class="frm_input required" size="20" maxlength="20"> 점
				</td>
			</tr>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="memManagerList.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
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
