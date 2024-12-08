<?
	$menu = "2";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더
	
	$DB_con = db1();
	
	if($mode == "mod") {
		$titNm = "지도 수정";

		$query = "";
		// 지도 정보 가져오기
		$query = "
			SELECT *
			FROM 
				TB_CONTENTS 
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
    		$con_Name =  trim($row['con_Name']);
			$con_Lv = trim($row['con_Lv']);
			$memo = trim($row['memo']);
			$category =  trim($row['category']);
    		$img = $row['img'];
    		$tag = $row['tag'];
    		$open_Bit = trim($row['open_Bit']);
    		$like_Cnt = trim($row['like_Cnt']);
    		$kml_File = trim($row['kml_File']);
    		$area_Code = $row['area_Code'];
    		$locat_Cnt =  trim($row['locat_Cnt']);
    		$reg_Id =  trim($row['reg_Id']);
    		$reg_Date =  trim($row['reg_Date']);
    		$end_Date =  trim($row['end_Date']);
			$endDate = substr($end_Date,0, 10);
		  
		  }
	   }

	} else {
	    $titNm = "지도 등록";
	}

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<script>
	function conLv(){
		var con_Lv = $('#con_Lv').val();
		if(con_Lv == '1'){
			$('.dateBit').hide();
		}else{			
			$('.dateBit').show();
		}
	}
</script>
<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="proc_contents.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
			<? if($mode=="mod") { ?>
				<th scope="row"><label for="con_Name">지도명</label></th>
				<td>
					<input type="text" name="con_Name" value="<?=$con_Name?>" id="con_Name" required class="frm_input required" size="50"  maxlength="20">
					<input type="hidden" name="org_con_Name"  id="org_con_Name" value="<?=$con_Name?>">
				</td>
				<th scope="row"><label for="category">카테고리</label></th>
				<td>
					<input type="hidden" name="oldlev"  id="oldlev" value="<?=$category?>">
					<select id="category" name="category">
						<option value="">카테고리선택</option>
						<?
							$cate_opt_query = "
								SELECT *
								FROM TB_CONFIG_CODE
								WHERE code_Div = 'category'
							";
							$cate_opt_stmt = $DB_con->prepare($cate_opt_query);
							$cate_opt_stmt->execute();
							while($cate_opt_row = $cate_opt_stmt->fetch(PDO::FETCH_ASSOC)) {										
								$code = $cate_opt_row['code'];
								$code_Name = $cate_opt_row['code_Name'];
								$option = "<option value='".$code."' ".( $category == $code?"selected='selected'":"").">".$code_Name."</option>";
								echo $option;
							}
						?>
					</select>
				</td>
			<? }else{ ?>
				<th scope="row"><label for="con_Name">지도명<strong class="sound_only">필수</strong></label></th>
				<td>
					<input type="text" name="con_Name" value="" id="con_Name" required class="frm_input required" size="50"  maxlength="20">
				</td>
				<th scope="row"><label for="category">카테고리<strong class="sound_only">필수</strong></label></th>
				<td>
					<input type="hidden" name="oldlev"  id="oldlev" value="<?=$category?>">
					<select id="category" name="category">
						<option value="">카테고리선택</option>
						<?
							$cate_opt_query = "
								SELECT *
								FROM TB_CONFIG_CODE
								WHERE code_Div = 'category'
							";
							$cate_opt_stmt = $DB_con->prepare($cate_opt_query);
							$cate_opt_stmt->execute();
							while($cate_opt_row = $cate_opt_stmt->fetch(PDO::FETCH_ASSOC)) {										
								$code = $cate_opt_row['code'];
								$code_Name = $cate_opt_row['code_Name'];
								$option = "<option value='".$code."'>".$code_Name."</option>";
								echo $option;
							}
						?>
					</select>
				</td>
			<? } ?>
			</tr>
			<tr>
				<? if($mode=="mod") { ?>
					<th scope="row"><label for="reg_Id">등록자</label></th>
					<td  colspan="3">
						<input type="text" id="reg_Id" name="reg_Id" onclick="window.open('idsearch.php','아이디검색','width=600,height=600,top=100,left=100');" value="<?=$reg_Id?>"/>
						<input type="hidden" id="org_reg_Id" name="org_reg_Id" value="<?=$reg_Id?>"/>
					</td>
				<?}else{?>
					<th scope="row"><label for="reg_Id">등록자</label></th>
					<td  colspan="3">
						<input type="text" id="reg_Id" name="reg_Id" onclick="window.open('idsearch.php','아이디검색','width=600,height=600,top=100,left=100');" value="<?=$reg_Id?>"/>
					</td>
				<?}?>
			</tr>
			<tr>
				<th scope="row"><label for="memo">지도설명</label></th>
				<td  colspan="3">
					<textarea id="memo" name="memo" class="frm_input "><?=$memo?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="con_Lv">지도등급</label></th>
				<td>
					<input type="hidden" name="oldlev"  id="oldlev" value="<?=$con_Lv?>">
					<select id="con_Lv" name="con_Lv" onchange="conLv()">
						<option value="">등급선택</option>
						<option value="0" <?if($con_Lv == "0"){?>selected<?}?>>SPECIAL</option>
						<option value="1" <?if($con_Lv == "1"){?>selected<?}?>>USER</option>
						<option value="2" <?if($con_Lv == "2"){?>selected<?}?>>COMPANY</option>
						<option value="3" <?if($con_Lv == "3"){?>selected<?}?>>EVENT</option>
					</select>
				</td>
				<th scope="row" class="dateBit" style="<?if($con_Lv == '1' || $con_Lv == ''){?>display:none;<?}else{?><?}?>"><label for="end_Date">마감일</label></th>
				<td class="dateBit" style="<?if($con_Lv == '1' || $con_Lv == ''){?>display:none;<?}else{?><?}?>">
					<input type="text" id="end_Date" name="end_Date" value="<?=$endDate?>" class="frm_input" size="10" maxlength="10">
				</td>
			</tr>

			<? if($con_Lv != '1' && $con_Lv != '') { ?>
			<tr>
				<th scope="row"><label for="area_Code">지역코드</label></th>
				<td><input type="text" name="area_Code" value="<?=$area_Code?>" id="area_Code" class="frm_input" size="50"  maxlength="20" readonly></td>
				<th scope="row"><label for="locat_Cnt">지역수<strong class="sound_only">필수</strong></label></th>
				<td><input type="text" name="locat_Cnt" value="<?=$locat_Cnt?>" id="locat_Cnt" required class="required frm_input" size="50"  maxlength="20" readonly></td>
			</tr>
			<? } ?>
			<tr>
				<th scope="row"><label for="tag">태그</label></th>
				<td scope="row">
					<span class="frm_info">태그는 최대 <strong>6개 까지 가능하며 각 태그는 ,(쉼표)로 구분</strong> 해주세요.</span>
					<input type="text" name="tag" value="<?=$tag?>" id="tag"  class="frm_input " size="50"  maxlength="50">		
				</td>
				<th scope="row"><label for="open_Bit">공개여부</label></th>
				<td>
					<input type="radio" name="open_Bit" value="0" id="open_Bit" <?=($open_Bit == "0" || !$open_Bit)?"checked":"";?> >
					<label for="open_Bit">전체공개</label>
					<input type="radio" name="open_Bit" value="1" id="open_Bit" <?=($open_Bit == "1")?"checked":"";?>>
					<label for="open_Bit">비공개</label>				
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="admin_Bit">관리자 공개여부</label></th>
				<td colspan="3">
					<span class="frm_info">신고당한 지점을 관리자가 패널티로 공개여부를 결정합니다.</span>
					<input type="radio" name="admin_Bit" value="0" id="admin_Bit" <?=($admin_Bit == "0" || !$admin_Bit)?"checked":"";?> >
					<label for="admin_Bit">공개</label>
					<input type="radio" name="admin_Bit" value="1" id="admin_Bit" <?=($admin_Bit == "1")?"checked":"";?>>
					<label for="admin_Bit">비공개</label>				
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="img">썸네일이미지</label></th>
				<td  colspan="3">
					<input type="file" name="img" id="img">
					<?
					//이미지 출력 방법변경으로 인한 출력 방법 변경 ------------------- 2019.02.15
					$chkImg = $img;
					if ($chkImg != "") {
				    	$m_file = DU_PATH.'contents/img/'.$img;
						if (file_exists($m_file)) {
							$con_Img = "https://places.gachita.kr/contents/img/photo.php?id=".$img;
							
							echo '<img src="'.$con_Img.'" alt="" height="60">';
							echo '&nbsp;<input type="checkbox" id="del_img" name="del_img" value="1">삭제';
						}
					}
					?>

					<? if($mode=="mod") { ?>
						<input type="hidden" name="img" value="<?=$img?>">
					<? } ?>
				</td>
			</tr>	
			<tr>
				<th scope="row"><label for="kml_File">kml파일</label></th>
				<td  colspan="3">
					<span class="frm_info">파일형식은 <strong>반드시 .kml 형식</strong>으로 해주세요.</span>
					<input type="file" name="kml_File" id="kml_File">
					<?
					//이미지 출력 방법변경으로 인한 출력 방법 변경 ------------------- 2019.02.15
					$chkKml = $kml_File;
					if ($chkKml != "") {
				    	$k_file = DU_PATH.'contents/kmlfile/'.$kml_File;
					
						if (file_exists($k_file)) {
							$k_url = '../../contents/kmlfile/'.$kml_File;
							
							//echo '<input type="file" name="kmlfile" id ="kmlfile" src="'.$m_url.'">';
							echo '<a href="'.$k_url.'">'.$kml_File.'</a>';
							echo '&nbsp;<input type="checkbox" id="del_kml_File" name="del_kml_File" value="1">삭제';
						}
					}
					?>
					<? if($mode=="mod") { ?>
						<input type="hidden" name="org_kml_File" value="<?=$kml_File?>">
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="taxi_Map">경로</label></th>
				<td colspan="3"><?if ($chkKml != "") {?><div id='map' style='width: 100%; height: 400px;'></div><?}else{?>kml파일이 없거나 해당 지역을 불러 올 수 없습니다.<?}?></td>
			</tr>	
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="list_contents.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
		</div>
		</form>


		<script>
		$(function(){
			$("#end_Date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true });
		});
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
		<?
		 //xml파일 지정
		$xml = file_get_contents("../../contents/kmlfile/".$kml_File);
		//xml파일 읽어오기
		$result_xml = simplexml_load_string($xml);
		// 읽어온 파일 확인하기
		//echo '<pre>' . print_r($result_xml, true) . '</pre>';
		//특정부분만 가져오기
		/*
		stdClass Object
		(
			[data] => stdClass Object
				(
					[employee] => Array
						(
							[0] => stdClass Object
								(
									[firstName] => John
									[lastName] => Doe
								)
		 
							[1] => stdClass Object
								(
									[firstName] => Anna
									[lastName] => Smith
								)
		 
							[2] => stdClass Object
								(
									[firstName] => Peter
									[lastName] => Jones
								)
						)
				)
		)
		객체나 배열이나 본인이 원하는 형태로 decode 해서 필요한 데이터만 가져올 수 있습니다.
		객체인 경우 -> 
		배열인 경우 employee[0] 표현하여 가져오기
		print_r($result_json2->data->employee[0]->firstName); // John
		echo " ";
		*/
		//echo $result_xml->Document->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates; // John
		$Placemark = $result_xml->Document->Placemark;
		$pm_cnt = count($Placemark);
		//echo $pm_cnt;
		$name = [];
		for($pm = 0; $pm < $pm_cnt; $pm++){
			$name_chk = $result_xml->Document->Placemark[$pm]->name;
			array_push($name, $name_chk);
		}
		//print_r($name);
		$name_cnt = count($name);
		//echo $name_cnt;
		//echo "<br>";
		$res_data = [];
		for($nm = 0; $nm < $name_cnt; $nm++){
			$locat = $result_xml->Document->Placemark[$nm]->Polygon->outerBoundaryIs->LinearRing->coordinates; 
		//	echo $locat;
			$locat_poi = explode( ',', $locat);
			//print_r($locat_poi);
			$poi_cnt = count($locat_poi);
			//echo $name[$nm]['0'];
			$lng = []; //경도
			$lnt = []; //위도
			for($i = 0; $i < $poi_cnt; $i++){
				if($i % 2 != 0){
					//경도
					//echo $locat_poi[$i];
					array_push($lng, $locat_poi[$i]);
				}else{
					//위도
					//echo $locat_poi[$i];		
					array_push($lnt, str_replace(" ","",str_replace("0 ", "", $locat_poi[$i])));
					/*
					echo "locat_poi : ".$locat_poi[$i];
					print_r($lng);
					echo "<br>";
					*/
				}
			}
			/*
			print_r($lnt);
			print_r($lng);
			*/
			$poi_cnt2 = count($lnt);
			$$coord_f = "";
			$coordinates = "";
			for($j = 0; $j < $poi_cnt2; $j++){
				$coord = "[".$lnt[$j].",".$lng[$j]."]";
				if($j == 0){
					$coordinates = $coord;
					$coord_f = $coord;
				}else{
					if($j % 6 == 0){
						$coordinates = $coordinates.",".$coord;
					}
				}
			}
			$name_sort = $name[$nm];
			$data = array("$nm" =>$coordinates);
			array_push($res_data, $data);
		}
		$res_cnt = count($res_data);
		$map_on = "";
		$color = ["#f53953","#ff8800","#ffca00","#7ed273","#33bce8"];
		for($k = 0; $k < $res_cnt; $k++){
			$rdata = $res_data[$k][$k];
			$mapadd =
				"
					map.addLayer({
						'id': '".($k+1)."',
						'type': 'fill',
						'source': {
							'type': 'geojson',
							'data': {
								'type': 'Feature',
								'geometry': {
									'type': 'Polygon',
									'coordinates': [[".$rdata."]]
								}
							}
						},
						'layout': {},
						'paint': {
							'fill-color': '".$color[$k]."',
							'fill-opacity': 0.4
						}
					});"
			;
			if($k == 0){
				$map_on = $mapadd;
			}else{
				$map_on = $map_on.$mapadd;
			}
		}
			
		?>
		<script>
			mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
			var map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
				center: <?php echo $coord_f ?>,
				zoom: 12
			});
			map.on('load', function () {
				<?php echo $map_on ?>
				/*map.addLayer({
					'id': '<?php echo $idx ?>',
					'type': 'fill',
					'source': {
						'type': 'geojson',
						'data': {
							'type': 'Feature',
							'geometry': {
								'type': 'Polygon',
								'coordinates': [[<?php echo $coordinates ?>]]
							}
						}
					},
					'layout': {},
					'paint': {
						'fill-color': '#088',
						'fill-opacity': 0.2
					}
				});*/
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
