<?
	$menu = "2";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더
	
	$DB_con = db1();
	
	if($mode == "mod") {
		$titNm = "지점 수정";

		$query = "";
		// 지도 정보 가져오기
		$query = "
			SELECT *
			FROM 
				TB_PLACE 
			WHERE idx = :idx" ;	
		//echo $query."<BR>";
		//exit;
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":idx",$idx);
		$stmt->execute();
		$num = $stmt->rowCount();
		
		if($num < 1)  { //아닐경우
		} else {
		    
		  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    		$idx = trim($row['idx']);
    		$con_Idx =  trim($row['con_Idx']);
    		$area_Code =  trim($row['area_Code']);
    		$category =  trim($row['category']);
    		$place_Name =  trim($row['place_Name']);
			$place_Icon = trim($row['place_Icon']);
    		$memo =  trim($row['memo']);
    		$smemo =  trim($row['smemo']);
    		$tel =  trim($row['tel']);
    		$otime_Day =  trim($row['otime_Day']);
			$otime_Day_1 = substr($otime_Day, 0, 5);
			$otime_Day_2 = substr($otime_Day, 6, 10);
    		$otime_Week =  trim($row['otime_Week']);
			$otime_Week_1 = substr($otime_Week, 0, 5);
			$otime_Week_2 = substr($otime_Week, 6, 10);
    		$img = $row['img'];
    		$like_Cnt = $row['like_Cnt'];
    		$share_Cnt = $row['share_Cnt'];
    		$addr = trim($row['addr']);
    		$lng = trim($row['lng']);
    		$lat = trim($row['lat']);
    		$reg_Id =  trim($row['reg_Id']);
    		$reg_Date =  trim($row['reg_Date']);
    		
		  }
	   }

	} else {
	    $titNm = "지점 등록";
	}

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
/*
	$(document).ready(function() {
		
	});
*/
	function openDaumZipAddress() {
		new daum.Postcode({
			oncomplete:function(data) {
				jQuery("#addr").val(data.address);
				jQuery("#addr_etc").focus();
			}
		}).open();
	}
</script>
<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="proc_place.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="place_Name">지점명</label></th>
				<td>
					<input type="text" name="place_Name" value="<?=$place_Name?>" id="place_Name" required class="frm_input required" size="50"  maxlength="20">
					<input type="hidden" name="org_place_Name"  id="org_place_Name" value="<?=$place_Name?>">
				</td>
				<th scope="row"><label for="place_Icon">지점대표아이콘</label></th>
				<td scope="row">
					<?if($mode == "mod"){
						$place_Icon_query = "
							SELECT code_Name, code_on_Img
							FROM TB_CONFIG_CODE
							WHERE code = '".$place_Icon."'
								AND code_Div = 'placeicon'
								AND use_Bit = '0'
						";
						$place_Icon_stmt = $DB_con->prepare($place_Icon_query);
						$place_Icon_stmt->execute();
						$place_Icon_row =$place_Icon_stmt->fetch();
						$code_Img = $place_Icon_row['code_on_Img'];	
						$code_Name = $place_Icon_row['code_Name'];	
						$code_ImgFile = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_Img;
					?>
						<input type="hidden" id="place_Icon" name="place_Icon" />
						<input type="text" id="place_Icon_name" name="place_Icon_name" onclick="window.open('piconsearch.php','지점아이콘검색','width=600,height=600,top=100,left=100');" value="<?=$code_Name?>"/>
						<img name="place_Icon_img" id="place_Icon_img" src="<?=$code_ImgFile?>" style="height:100px">
						<input type="hidden" id="org_place_Icon" name="org_place_Icon" value="<?=$place_Icon?>"/>
						<input type="hidden" id="org_category" name="org_category" value="<?=$category?>" />
					<?}else{?>
						<input type="hidden" id="place_Icon" name="place_Icon" />
						<input type="text" id="place_Icon_name" name="place_Icon_name" onclick="window.open('piconsearch.php','지점아이콘검색','width=600,height=600,top=100,left=100');" value=""/>
						<img name="place_Icon_img" id="place_Icon_img" src="" style="height:100px">
					<?}?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="con_Idx">소속지도</label></th>
				<td>
					<?if($mode == "mod"){
						$chk_c_query = "
							SELECT idx, con_Name
							FROM TB_CONTENTS
							WHERE idx = :idx
						";
						$chk_c_stmt = $DB_con->prepare($chk_c_query);
						$chk_c_stmt->bindValue(":idx",$con_Idx);
						$chk_c_stmt->execute();
						$chk_c_row =$chk_c_stmt->fetch();
						$con_Name = $chk_c_row['con_Name'];	
					?>
						<input type="hidden" name="con_Idx" id="con_Idx" class="frm_input">
						<input type="hidden" name="org_con_Idx" id="org_con_Idx" class="frm_input"value="<?=$con_Idx?>">
						<input type="text" id="con_Name" name="con_Name" onclick="window.open('consearch.php','지도검색','width=600,height=600,top=100,left=100');" value="<?=$con_Name?>"/>
					<?}else{?>
						<input type="hidden" name="con_Idx" id="con_Idx" class="frm_input">
						<input type="text" id="con_Name" name="con_Name" onclick="window.open('consearch.php','지도검색','width=600,height=600,top=100,left=100');" value=""/>
					<?}?>
				</td>
				<?
					if($con_Idx != ""){
						$con_query = "
							SELECT con_Lv
							FROM TB_CONTENTS
							WHERE idx = :idx
						";
						$con_stmt = $DB_con->prepare($con_query);
						$con_stmt->bindValue(":idx",$con_Idx);
						$con_stmt->execute();
						$con_row =$con_stmt->fetch();
						$con_Lv = $con_row['con_Lv'];	
					}
				?>
				<? if($con_Lv != "1"){ ?>
					<th scope="row"><label for="area_Code">지역코드<strong class="sound_only">필수</strong></label></th>
					<td>
						<? if($mode == "mod"){ ?>
							<input type="hidden" name="org_area_Code" value="<?=$area_Code?>" id="org_area_Code">
							<select name="area_Code" id="area_Code">
								<?if($con_Idx != ""){
									$con_area_query = "
										SELECT area_Code
										FROM TB_CONTENTS
										WHERE idx = :con_Idx
										;
									";
									$con_area_stmt = $DB_con->prepare($con_area_query);
									$con_area_stmt->bindValue(":con_Idx",$con_Idx);
									$con_area_stmt->execute();
									while($con_area_row =$con_area_stmt->fetch()){
										$con_area_Code = $con_area_row['area_Code'];	
										$con_area_Code_exp = explode( ',', $con_area_Code);
										$con_area_cnt = count($con_area_Code_exp);
										for($i = 0; $i < $con_area_cnt; $i++){
											$carea_Code = $con_area_Code_exp[$i];
								?>
											<option value="<?=$carea_Code?>" <?if($carea_Code == $area_Code){?>selected<?}?>><?=$carea_Code?></option>	
								<?	
										}
									}
								}?>
							</select>
						<? }else{ ?>
							<select name="area_Code" id="area_Code">
							</select>
						<? } ?>
					</td>
					<? } ?>
			</tr>
			<tr>
				<th scope="row"><label for="tel">연락처</label></th>
				<td scope="row">
					<input type="text" name="tel" value="<?=$tel?>" id="tel"  class="frm_input " size="50"  maxlength="20">		
				</td>
				<th scope="row"><label for="addr">주소</label></th>
				<td scope="row">
					<?if($mode == "mod"){?>
						<input type="text" id="addr" name="addr" style="width:300px;height:35px;" onClick="openDaumZipAddress();" readonly value="<?=$addr?>"/>
					<?}else{?>
						<input type="text" id="addr" name="addr" style="width:300px;height:35px;" onClick="openDaumZipAddress();" readonly />
						<input type="text" id="addr_etc" name="addr_etc" style="width:200px;" />	
					<?}?>
				</td>
			</tr>
			<!--
			<tr>
				<th scope="row"><label for="otime_Day">영업시간(평일)</label></th>
				<td scope="row">
					<select id="otime_Day_1" name="otime_Day_1">
						<option value="">오픈시간</option>
						<?
							for($i = 0; $i < 25; $i++){
								if($i <10){
									$i = "0".$i;
								}else{
									$i = $i;
								}
								?>
									<option value="<?=$i?>:00" <? if ( $otime_Day_1 == $i.":00" ) { ?>selected="selected"<? } ?>><?=$i?>:00</option>
									<option value="<?=$i?>:30" <? if ( $otime_Day_1 == $i.":30" ) { ?>selected="selected"<? } ?>><?=$i?>:30</option>
						<?  } ?>
					</select>
					<span> ~ </span>
					<select id="otime_Day_2" name="otime_Day_2">
						<option value="">마감시간</option>
						<?
							for($i = 0; $i < 25; $i++){
								if($i <10){
									$i = "0".$i;
								}else{
									$i = $i;
								}
								?>
									<option value="<?=$i?>:00" <? if ( $otime_Day_2 == $i.":00" ) { ?>selected="selected"<? } ?>><?=$i?>:00</option>
									<option value="<?=$i?>:30" <? if ( $otime_Day_2 == $i.":30" ) { ?>selected="selected"<? } ?>><?=$i?>:30</option>
						<?  } ?>
					</select>
					<input type='hidden' id="otime_Day" name="otime_Day" value="<?=$otime_Day?>" /><br> 
				</td>
				<th scope="row"><label for="otime_Week">영업시간(주말)</label></th>
				<td scope="row">
					<select id="otime_Week_1" name="otime_Week_1">
						<option value="">오픈시간</option>
						<?
							for($i = 0; $i < 25; $i++){
								if($i <10){
									$i = "0".$i;
								}else{
									$i = $i;
								}
								?>
									<option value="<?=$i?>:00" <? if ( $otime_Week_1 == $i.":00" ) { ?>selected="selected"<? } ?>><?=$i?>:00</option>
									<option value="<?=$i?>:30" <? if ( $otime_Week_1 == $i.":30" ) { ?>selected="selected"<? } ?>><?=$i?>:30</option>
						<?  } ?>
					</select>
					<span> ~ </span>
					<select id="otime_Week_2" name="otime_Week_2">
						<option value="">마감시간</option>
						<?
							for($i = 0; $i < 25; $i++){
								if($i <10){
									$i = "0".$i;
								}else{
									$i = $i;
								}
								?>
									<option value="<?=$i?>:00" <? if ( $otime_Week_2 == $i.":00" ) { ?>selected="selected"<? } ?>><?=$i?>:00</option>
									<option value="<?=$i?>:30" <? if ( $otime_Week_2 == $i.":30" ) { ?>selected="selected"<? } ?>><?=$i?>:30</option>
						<?  } ?>
					</select>
					<input type='hidden' id="otime_Week" name="otime_Week" value="<?=$otime_Week?>" /><br>
				</td>
			</tr>
			-->
			<? if($mode == "mod"){ ?>
				<tr>
					<th scope="row"><label for="like_Cnt">좋아요 수</label></th>
					<td scope="row">
						<span name="like_Cnt" id="like_Cnt"><?=$like_Cnt?></span>
					</td>
					<th scope="row"><label for="share_Cnt">공유 수</label></th>
					<td scope="row">
						<span name="share_Cnt" id="share_Cnt"><?=$share_Cnt?></span>
					</td>
				</tr>
			<? } ?>
			<tr>
				<th scope="row"><label for="memo">지점 설명</label></th>
				<td  colspan="3">
					<textarea id="memo" name="memo" class="frm_input "><?=$memo?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="img">지점이미지</label></th>
				<td  colspan="3">
					<span class="frm_info">이미지 크기는 <strong>넓이 100픽셀 높이 100픽셀</strong>로 해주세요.</span>    
					<input type="file" name="img" id="img">
					<?
					//이미지 출력 방법변경으로 인한 출력 방법 변경 ------------------- 2019.02.15
					$chkImg = $img;
					if ($chkImg != "") {
				    	$m_file = DU_PATH.'contents/place_img/'.$img;
						if (file_exists($m_file)) {
							$m_url = '../../contents/place_img/'.$img;
							
							echo '<img src="'.$m_url.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_img" name="del_img" value="1">삭제';
						}
					}
					/*
					//BLOB 파일 형태로 저장된 이미지 파일 출력되도록 ------------------- 2019.02.15
					
					if($mem_profile_update)
					{
					?>
					<img src="/contents/img/img.php?memId=<? echo $mem_Id ?>" height="60">
					<input type="checkbox" id="del_img1" name="del_img1" value="1">삭제
					<?
					}
						*/
					
					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="img" value="<?=$img?>">
					<? } ?>
				</td>
			</tr>
			<? if($mode == "mod"){ ?>
				<tr>
					<th scope="row"><label for="Map">위치</label></th>
					<td colspan="3"><div id='map' style='width: 400px; height: 300px;'></div></td>
				</tr>	
			<? } ?>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="list_place.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>
		<script>
			function search_contents(){

			}
		</script>

		<script>
		function fmember_submit(f)
		{
			if (!f.img.value.match(/\.(gif|jpe?g|png)$/i) && f.img.value) {
				alert('지도이미지는 이미지 파일만 가능합니다.');
				return false;
			}
			if (!f.img.kml_File.match(/\.(kml)$/i) && f.kml_File.value) {
				alert('지도이미지는 이미지 파일만 가능합니다.');
				return false;
			}

			return true;
		}
		</script>
		<script>
			mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
			var lng = <?php echo $lng ?>;
			var lat = <?php echo $lat ?>;
			var place_Name = "<?php echo $place_Name ?>";
			var map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
				zoom: 12,
				center: [lng, lat]
			});
			map.on('load', function () {
				map.addLayer({
					"id": "points",
					"type": "symbol",
					"source": {
						"type": "geojson",
						"data": {
							"type": "FeatureCollection",
							"features": [{
							"type": "Feature",
							"geometry": {
								"type": "Point",
								"coordinates": [lng, lat]
							},
							"properties": {
								"title": place_Name
								,"icon": "marker"
							}
						}]
					}
				},
				"layout": {
					"icon-image": "{icon}-11",
					"text-field": "{title}",
					"text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
					"text-offset": [0, 0.6],
					"text-anchor": "top"
				}
				});
			});
			 
		</script>

	</div>    

<?
	dbClose($DB_con);
	$stmt = null;
	$meInfoStmt = null;
	$mEtcStmt = null;
	$mstmt = null;

	include "../common/inc/inc_footer.php";  //푸터 
	 
?>
