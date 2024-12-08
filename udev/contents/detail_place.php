<?
	$menu = "2";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	
	$DB_con = db1();

	// 회원 기본정보
	$viewQuery = "SELECT * FROM TB_CONTENTS where idx='".$idx."' " ;
	$viewStmt = $DB_con->prepare($viewQuery);
	$viewStmt->execute();
	$row = $viewStmt->fetch(PDO::FETCH_ASSOC);

	$idx = trim($row['idx']);
	$con_Name =  trim($row['con_Name']);
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

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 
	include "../common/inc/inc_mir.php";		//미르페이  

	$chk_mirpay = mirpay('https://www.mirland.net/api/searchCoin.php', array("userId" => $row['mem_Tel'], "apiKey" => '8d8aad8bfda4102f88778615b1a75f66'));
	$h_mirpay = $chk_mirpay['pay'];

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">[<?=$row['con_Name']?>]&nbsp;상세보기</h1>


<style>
.ov_num{border-right:1px solid #fff;}
.ov_txt a{color:#fff;}
</style>
<div class="local_ov01 local_ov">
	<span class="btn_ov01">
		<span class="ov_txt"><a href="detail_place.php?id=<?=$idx?>">기본정보</a> </span>
		<!--<span class="ov_num"><a href="detail_place.php?id=<?=$idx?>">캐시내역</a></span>
		<span class="ov_num"><a href="detail_place.php?id=<?=$idx?>">주문내역</a></span>
		<span class="ov_num"><a href="detail_place.php?id=<?=$idx?>">매칭내역</a></span>
		<span class="ov_num"><a href="detail_place.php?id=<?=$idx?>">쿠폰내역</a></span>
		<span class="ov_num"><a href="detail_place.php?id=<?=$idx?>">문의리스트</a></span>-->
	</span>
</div>

		<form name="fmember" id="fmember" action="reg_contents.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
				<th scope="row"><label for="con_Name">지도명</label></th>
				<td>
					<span><?=$con_Name?></span>
				</td>
				<th scope="row"><label for="category">카테고리</label></th>
				<td>
					<span><? if ( $category == "1" ) { ?>음식점<?}else if($category == "2"){?>카페<?}else{?>ent<?}?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">지역코드</th>
				<td><span><?=$area_Code?></span></td>
				<th scope="row">지역수</th>
				<td><span><?=$locat_Cnt?></span></td>
			</tr>

			<tr>
				<th scope="row"><label for="tag">태그</label></th>
				<td><span><?=$tag?></span></td>
				<th scope="row">공개여부</th>
				<td>
					<span><? if ( $open_Bit == "1" ) { ?>전체공개<?}else if($open_Bit == "2"){?>구독자만공개<?}else{?>비공개<?}?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="img">지도이미지</label></th>
				<td  colspan="3">
					<?
					//이미지 출력 방법변경으로 인한 출력 방법 변경 ------------------- 2019.02.15
					$chkImg = $img;
					if ($chkImg != "") {
				    	$m_file = DU_PATH.'contents/img/'.$img;
						if (file_exists($m_file)) {
							$m_url = '../../contents/img/'.$img;
							
							echo '<img src="'.$m_url.'" alt="" height="60">';
						}
					}
					?>
				</td>
			</tr>	
			<tr>
				<th scope="row"><label for="kml_File">kml파일</label></th>
				<td  colspan="3">
					<?
					//이미지 출력 방법변경으로 인한 출력 방법 변경 ------------------- 2019.02.15
					$chkKml = $kml_File;
					if ($chkKml != "") {
				    	$m_file = DU_PATH.'contents/kmlfile/'.$kml_File;
					
						if (file_exists($m_file)) {
							$m_url = '../../contents/kmlfile/'.$kml_File;
							
							//echo '<input type="file" name="kmlfile" id ="kmlfile" src="'.$m_url.'">';
							echo '<a href="'.$m_url.'">'.$kml_File.'</a>';
						}
					}
					?>
				</td>
			</tr>
			</tbody>
			</table>
		</div>



</div>    
<div class="btn_fixed_top">	
	<a href="list_place.php" id="bt_m_a_add" class="btn btn_01">핀목록</a>
</div>

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;
	$mcntStmt = null;
	$mcntStmt2 = null;
	$mcntStmt3 = null;
	$mstmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
