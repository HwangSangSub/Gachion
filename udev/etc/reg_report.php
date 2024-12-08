<?
	$menu = "3";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더
	
	$DB_con = db1();
	
	if($mode == "mod") {
		$titNm = "신고 관리";

		$query = "";
		// 콘텐츠 정보 가져오기
		$query = "
			SELECT *
			FROM 
				TB_MEMBERS_REPORT 
			WHERE idx = :idx" ;	
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindparam(":idx",$idx);
		$stmt->execute();
		$num = $stmt->rowCount();
		
		if($num < 1)  { //아닐경우
		} else {
		    
		  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    		$idx = trim($row['idx']);
    		$member_Idx =  trim($row['member_Idx']);
    		$con_Idx =  trim($row['con_Idx']);
    		$place_Idx = $row['place_Idx'];
			$place_name_query = "
				SELECT place_Name
				FROM TB_PLACE
				WHERE idx = :place_Idx
				;
			";
			$place_stmt = $DB_con->prepare($place_name_query);
			$place_stmt->bindparam(":place_Idx", $place_Idx);
			$place_stmt->execute();
			$place_row = $place_stmt->fetch(PDO::FETCH_ASSOC);
    		$place_Name = $place_row['place_Name'];
    		$report_Idx = $row['report_Idx'];
    		$report = trim($row['report']);
    		$admin_Bit = trim($row['admin_Bit']);
			if($admin_Bit == "N"){
				$adminBit = "처리대기중";
			}else{
				$adminBit = "처리완료";
			}
    		$penalty_Bit = trim($row['penalty_Bit']);
			if($penalty_Bit == "Y"){
				$penaltyBit = "부과안함";
			}else if($penalty_Bit == "A"){
				$penaltyBit = "A 타입 : 해당 유저가 좋아요 했을 경우 취소처리";
			}else if($penalty_Bit == "B"){
				$penaltyBit = "B 타입 : 해당 지도의 메인/검색 리스트 미노출";
			}else{
				$penaltyBit = "부과대기중";
			}
    		$reg_Id = trim($row['reg_Id']);
    		$reg_Date = $row['reg_Date'];
			if($reg_Date != ""){
				$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
			}else{
				$regDate = "-";
			}
		  }
	   }

	} else {
	    $titNm = "신고 관리";
	}

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<script type="text/javascript">
	function proc_Report(idx){
		var con_test = confirm("해당코드를 삭제하시겠습니까?");
		if(con_test == true){
			var allData = { 
				"idx" : idx
				,"qstr" : "<?php echo $qstr;?>"
				,"page" : "<?php echo $page;?>"
			};
			$.ajax({
			url:"/udev/etc/proc_report.php",
				type:'POST',
				dataType : 'json',
				data: allData,
				success:function(data){
					location.reload();
				},
				error:function(jqXHR, textStatus, errorThrown){
					alert("에러 발생~~ \n" + textStatus + " : " + errorThrown);
					location.reload();
				}
			});
		}else if(con_test == false){
			location.reload();
		}
	}
</script>
<div id="wrapper">

    <div id="container" class="">
        <h1 id="container_title"><?=$titNm?></h1>
        <div class="container_wr">
		<form name="fmember" id="fmember" action="proc_report.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="con_Name">신고한 지점이 소속된 지도</label></th>
				<td>
					<a href="../contents/reg_contents.php?mode=mod&idx=<?=$con_Idx?>" style="cursor: pointer;"><input type="text" style="cursor: pointer;" name="con_Name" value="<?=$con_Idx?>" id="con_Name" required class="frm_input" size="50"  maxlength="20" readonly /></a>
					<input type="hidden" name="con_Idx"  id="con_Idx" value="<?=$con_Idx?>">
				</td>
				<th scope="row"><label for="place_Name">신고한 지점명</label></th>
				<td>
					<input type="text" name="place_Name" value="<?=$place_Name?>" id="place_Name" required class="frm_input" size="50"  maxlength="20" readonly />
					<input type="hidden" name="place_Idx"  id="place_Idx" value="<?=$place_Idx?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="report_Code">신고코드</label></th>
				<td>
					<span><?=$report_Idx?></span>
					<input type="hidden" name="report_Idx" id="report_Idx" value="<?=$report_Idx?>">
				</td>
				<th scope="row"><label for="report_Name">신고사유<strong class="sound_only">필수</strong></label></th>
				<td>
					<span><?=$report?></span>
					<input type="hidden" name="report" id="report" value="<?=$report?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="report_Id">신고자</label></th>
				<td>
					<span><a href="../member/memberReg.php?mode=mod&id=<?=$reg_Id?>" style="cursor: pointer;"><?=$reg_Id?></a></span>
					<input type="hidden" name="reg_Id" id="reg_Id" value="<?=$reg_Id?>">
				</td>
				<th scope="row"><label for="report_Date">신고일<strong class="sound_only">필수</strong></label></th>
				<td>
					<span><?=$reg_Date?></span>
					<input type="hidden" name="reg_Date" id="reg_Date" value="<?=$reg_Date?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="admin_Bit">본사처리여부</label></th>
				<td scope="row">
					<input type="text" name="admin_Bit" value="<?=$adminBit?>" id="admin_Bit"  class="frm_input " size="50"  maxlength="20" readonly />		
				</td>
				<th scope="row"><label for="penalty_Bit">패널티부과여부</label></th>
				<td>
					<? if($penalty_Bit == "N"){ ?>
						<input type="radio" name="penalty_Bit" value="Y" id="penalty_Bit" <?=($penalty_Bit == "Y")?"checked":"";?> >
						<label for="penalty_Bit">신고부적합</label>&nbsp;&nbsp;
						<input type="radio" name="penalty_Bit" value="A" id="penalty_Bit" <?=($penalty_Bit == "A")?"checked":"";?>>
						<label for="penalty_Bit">패널티 A : 해당 유저가 좋아요 했을 경우 취소처리</label>&nbsp;&nbsp;
						<input type="radio" name="penalty_Bit" value="B" id="penalty_Bit" <?=($penalty_Bit == "B")?"checked":"";?>>
						<label for="penalty_Bit">패널티 B : 해당 지도의 메인/검색 리스트 미노출</label>	&nbsp;&nbsp;
					<? }else{ ?>
						<span><?= $penaltyBit; ?></span>
					<? } ?>
				</td>
			</tr>
			</tbody>
			</table>
		</div>

		<div class="btn_fixed_top">
			<a href="list_contents.php?<?=$qstr?>&page=<?=$page?>" class="btn btn_02">목록</a>
<? if($_COOKIE['du_udev']['id'] != 'admin2'){?>
			<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
<? } ?>
		</div>
		</form>


		<script>
		function fmember_submit(f)
		{
			if (!f.img.value.match(/\.(gif|jpe?g|png)$/i) && f.img.value) {
				alert('콘텐츠이미지는 이미지 파일만 가능합니다.');
				return false;
			}
			if (!f.img.kml_File.match(/\.(kml)$/i) && f.kml_File.value) {
				alert('콘텐츠이미지는 이미지 파일만 가능합니다.');
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
