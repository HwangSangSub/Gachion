<?
	$menu = "1";
	$smenu = "6";

	include "../common/inc/inc_header.php";  //헤더 

	$titNm = "가이드이미지등록";

	$DB_con = db1();
	
	$query = "";
	$query = "SELECT idx, con_GuideUrl1, con_GuideUrl2, con_GuideUrl3, con_GuideUrl4, con_GuideUrl5 FROM TB_CONFIG_GUIDE  LIMIT 1" ;
	$stmt = $DB_con->prepare($query);
	$stmt->execute();

	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$idx = $row['idx'];
	$con_GuideUrl1 = $row['con_GuideUrl1'];		//만남완료변수설정(거리_m)
	$con_GuideUrl2 = $row['con_GuideUrl2'];		//만남완료변수설정(시간_초)
	$con_GuideUrl3 = $row['con_GuideUrl3'];				//노선유효시간(분)
	$con_GuideUrl4 = $row['con_GuideUrl4'];		//매칭가능거리(m)
	$con_GuideUrl5 = $row['con_GuideUrl5'];				//매칭증가요금(%)


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
		<form name="fmember" id="fmember" action="configGuideProc.php" onsubmit="return f_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="guide_Img">가이드이미지경로(생성)</label></th>
				<!--<td><input type="text" class="frm_input" id="con_PopupUrl" name="con_PopupUrl"  size="150" value="<?=$con_PopupUrl?>" /></td>-->
				<td colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이는 720픽셀 높이는 최대 5000픽셀</strong>로 해주세요.</span>    
					<input type="file" name="guide_Img1" id="guide_Img1">
					<?

					$chkImg = $con_GuideUrl1;
					if ($chkImg != "") {

						$m_file = DU_DATA_PATH.'/guide/'.$con_GuideUrl1;
						if (file_exists($m_file)) {
							$m_url = '/data/guide/'.$con_GuideUrl1;
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_guide_Img1" name="del_guide_Img1" value="1">삭제';
						}
					}

					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="con_GuideUrl1" value="<?=$con_GuideUrl1?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="guide_Img2">가이드이미지경로(현황)</label></th>
				<!--<td><input type="text" class="frm_input" id="con_PopupUrl" name="con_PopupUrl"  size="150" value="<?=$con_PopupUrl?>" /></td>-->
				<td colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이는 720픽셀 높이는 최대 5000픽셀</strong>로 해주세요.</span>    
					<input type="file" name="guide_Img2" id="guide_Img2">
					<?

					$chkImg = $con_GuideUrl2;
					if ($chkImg != "") {

						$m_file = DU_DATA_PATH.'/guide/'.$con_GuideUrl2;
						if (file_exists($m_file)) {
							$m_url = '/data/guide/'.$con_GuideUrl2;
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_guide_Img2" name="del_guide_Img2" value="1">삭제';
						}
					}

					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="con_GuideUrl2" value="<?=$con_GuideUrl2?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="guide_Img3">가이드이미지경로(만남)</label></th>
				<!--<td><input type="text" class="frm_input" id="con_PopupUrl" name="con_PopupUrl"  size="150" value="<?=$con_PopupUrl?>" /></td>-->
				<td colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이는 720픽셀 높이는 최대 5000픽셀</strong>로 해주세요.</span>    
					<input type="file" name="guide_Img3" id="guide_Img3">
					<?

					$chkImg = $con_GuideUrl3;
					if ($chkImg != "") {

						$m_file = DU_DATA_PATH.'/guide/'.$con_GuideUrl3;
						if (file_exists($m_file)) {
							$m_url = '/data/guide/'.$con_GuideUrl3;
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_guide_Img3" name="del_guide_Img3" value="1">삭제';
						}
					}

					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="con_GuideUrl3" value="<?=$con_GuideUrl3?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="guide_Img4">가이드이미지경로(이동)</label></th>
				<!--<td><input type="text" class="frm_input" id="con_PopupUrl" name="con_PopupUrl"  size="150" value="<?=$con_PopupUrl?>" /></td>-->
				<td colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이는 720픽셀 높이는 최대 5000픽셀</strong>로 해주세요.</span>    
					<input type="file" name="guide_Img4" id="guide_Img4">
					<?

					$chkImg = $con_GuideUrl4;
					if ($chkImg != "") {

						$m_file = DU_DATA_PATH.'/guide/'.$con_GuideUrl4;
						if (file_exists($m_file)) {
							$m_url = '/data/guide/'.$con_GuideUrl4;
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_guide_Img4" name="del_guide_Img4" value="1">삭제';
						}
					}

					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="con_GuideUrl4" value="<?=$con_GuideUrl4?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="guide_Img5">가이드이미지경로(종료)</label></th>
				<!--<td><input type="text" class="frm_input" id="con_PopupUrl" name="con_PopupUrl"  size="150" value="<?=$con_PopupUrl?>" /></td>-->
				<td colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이는 720픽셀 높이는 최대 5000픽셀</strong>로 해주세요.</span>    
					<input type="file" name="guide_Img5" id="guide_Img5">
					<?

					$chkImg = $con_GuideUrl5;
					if ($chkImg != "") {

						$m_file = DU_DATA_PATH.'/guide/'.$con_GuideUrl5;
						if (file_exists($m_file)) {
							$m_url = '/data/guide/'.$con_GuideUrl5;
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_guide_Img5" name="del_guide_Img5" value="1">삭제';
						}
					}

					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="con_GuideUrl5" value="<?=$con_GuideUrl5?>">
					<? } ?>
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

<? include "../common/inc/inc_footer.php";  //푸터 ?>
