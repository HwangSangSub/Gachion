<?
	$menu = "0";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$titNm = "환경설정";

	$DB_con = db1();
	
	$query = "SELECT idx, content_MaxCnt, place_MaxCnt, list_PlaceCnt, total_LikeCnt FROM TB_CONFIG  LIMIT 1" ;
	$stmt = $DB_con->prepare($query);
	$stmt->execute();

	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$idx = $row['idx'];
	$content_MaxCnt = $row['content_MaxCnt'];				//지도생성제한
	$place_MaxCnt = $row['place_MaxCnt'];						//지점생성제한
	$list_PlaceCnt = $row['list_PlaceCnt'];							//메인지도 노출조건(최소 지점 보유 수)
	$total_LikeCnt = $row['total_LikeCnt'];						//통합좋아요 노출조건(최소 좋아요 수)


	if ($idx == "") {
		$conRecom_BRPode = "reg";
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
		<form name="fmember" id="fmember" action="configProc.php" onsubmit="return f_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="content_MaxCnt">지도생성제한</label></th>
				<td><input type="text" name="content_MaxCnt" id="content_MaxCnt" class="frm_input" size="15" maxlength="20" value="<?=$content_MaxCnt?>"> 개</td>
				<th scope="row"><label for="place_MaxCnt">지점생성제한</label></th>
				<td><input type="text" name="place_MaxCnt" id="place_MaxCnt" class="frm_input" size="15" maxlength="20" value="<?=$place_MaxCnt?>"> 개</td>
			</tr>
			<tr>
				<th scope="row"><label for="list_PlaceCnt">메인지도 노출조건<br>(최소 지점 보유 수)</label></th>
				<td><input type="text" name="list_PlaceCnt" id="list_PlaceCnt" class="frm_input" size="15" maxlength="20" value="<?=$list_PlaceCnt?>"> 개</td>
				<th scope="row"><label for="total_LikeCnt">통합좋아요 노출조건<br>(최소 좋아요 수)</label></th>
				<td><input type="text" name="total_LikeCnt" id="total_LikeCnt" class="frm_input" size="15" maxlength="20" value="<?=$total_LikeCnt?>"> 개</td>
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
