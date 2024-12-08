<?
	$menu = "2";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$DB_con = db1();
	
	if($mode=="mod") {
		$titNm = "회원등급관리 수정";

		$query = "";
		//$query = "SELECT memLv, memLv_Name, memIconFile, memMatCnt, memDc FROM TB_MEMBER_LEVEL WHERE idx = :idx" ;
		$query = "
			SELECT 
				member_level.memLv,
				member_level.memLv_Name, 
				member_level.memMatCnt, 
				member_level.memDc,
				member_level_img.memLv_update
			FROM 
				TB_MEMBER_LEVEL as member_level
				left outer join TB_MEMBER_LEVEL_PHOTO as member_level_img on member_level.memLv = member_level_img.memLv
			WHERE 
				member_level.idx = :idx" ;
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":idx",$idx);
		//$idx = trim($idx);
		$stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

		$memLv =  trim($row['memLv']);
		$memLv_Name = trim($row['memLv_Name']);
		//$memIconFile = $row['memIconFile'];
		$memMatCnt = trim($row['memMatCnt']);
		$memDc = trim($row['memDc']);
		$memLv_update = $row['memLv_update'];

	} else {
		$mode = "reg";
		$titNm = "회원등급관리 등록";

	}



	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="memManagerProc.php" onsubmit="return fubmit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="memLv_Name">회원등급명<strong class="sound_only">필수</strong></label></th>
				<td>
					<input type="text" name="memLv_Name" value="<?=$memLv_Name?>" id="memLv_Name" required class="frm_input required" size="50"  maxlength="50">
				</td>
					<? 
						$select_array = array(
							'1' => '1레벨',
							'2' => '2레벨',
							'3' => '3레벨',
							'4' => '4레벨',
							'5' => '5레벨',
							'6' => '6레벨',
							'7' => '7레벨',
							'8' => '8레벨',
							'9' => '9레벨',
						    '10' => '10레벨',
						    '11' => '11레벨',
						    '12' => '12레벨',
						    '13' => '13레벨',
							'14' => '14레벨'
						);
					?>	    

				<th scope="row"><label for="mem_Lv">레벨선택<strong class="sound_only">필수</strong></label></th>
				<td>
					<select id="mem_Lv" name="mem_Lv" class="selectBox" required class="frm_input required">
						<option value="">레벨선택</option>
						<? foreach($select_array as $k=>$v):?>
							<option value="<?=$k;?>" <? if ($mode == "mod") { ?><? if ( $k == $memLv ) { ?>selected="selected"<? } }?>><? echo $v?></option>
						<? endforeach;?>
					</select>
				</td>
			</tr>
			
			<? $chkImg = $memIconFile; ?>
			<tr>
				<th scope="row"><label for="mb_img">회원등급이미지</label></th>
				<td colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이 132픽셀 높이 132픽셀</strong>로 해주세요.</span>    
					<input type="file" name="mb_img" id="mb_img" <? if($chkImg == "") {?>required class="frm_input required"<? } ?>>
					<?
					/*
					if ($chkImg != "") {

					    $m_file = DU_DATA_PATH.'/levIcon/'.$memIconFile;
						if (file_exists($m_file)) {
						    $m_url = '/data/levIcon/'.$memIconFile;
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_mb_img" name="del_mb_img" value="1">삭제';
						}
					}
					*/
					//BLOB 파일 형태로 저장된 이미지 파일 출력되도록 ------------------- 2019.02.18
					if($memLv_update)
					{
						echo $mem
					?>
					<img src="/data/levIcon/photo.php?memLv=<? echo $memLv ?>" height="60">
					<input type="checkbox" id="del_mb_img1" name="del_mb_img1" value="1">삭제
					<?
					}

					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="mem_ImgFile" value="<?=$memIconFile?>">
					<? } ?>
				</td>
			</tr>
			
			
			
			<tr>
				<th scope="row"><label for="memMatCnt">회원등급조건<strong class="sound_only">필수</strong></label></th>
				<td>
					조건 <input type="text" name="memMatCnt" value="<?=$memMatCnt?>" id="memMatCnt" required class="frm_input required" size="20" maxlength="20"> 점
				</td>
				<th scope="row"><label for="mem_Lv">수수료<strong class="sound_only">필수</strong></label></th>
				<td>
					<input type="text" name="memDc" value="<?=$memDc?>" id="memDc" required class="frm_input required" size="20"  maxlength="20"> %
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
	dbClose($DB_con);
	$stmt = null;
	$mstmt = null;

	include "../common/inc/inc_footer.php";  //푸터 
	 
?>
