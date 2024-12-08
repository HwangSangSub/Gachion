<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	
	$DB_con = db1();

	// 회원 기본정보
	$viewQuery = "SELECT * FROM TB_MEMBERS as A where A.mem_Id='".$id."' " ;
	$viewStmt = $DB_con->prepare($viewQuery);
	$viewStmt->execute();
	$row = $viewStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?=$row['mem_Id']?>(<?=$row['mem_NickNm']?>)&nbsp;회원상세보기</h1>


<style>
.ov_num{border-right:1px solid #fff;}
.ov_txt a{color:#fff;}
</style>
<div class="local_ov01 local_ov">
	<span class="btn_ov01">
		<span class="ov_txt"><a href="memberDetailView.php?id=<?=$id?>">기본정보</a> </span>
		<span class="ov_num"><a href="memberDetailView_contents.php?id=<?=$id?>">등록한 지도 내역</a></span>
		<span class="ov_num"><a href="memberDetailView_report.php?id=<?=$id?>">신고내역</a></span>
		<!--
		<span class="ov_num"><a href="memberDetailView_order.php?id=<?=$id?>">주문내역</a></span>
		<span class="ov_num"><a href="memberDetailView_taxiSharingList.php?id=<?=$id?>">매칭내역</a></span>
		<span class="ov_num"><a href="memberDetailView_coupon.php?id=<?=$id?>">쿠폰내역</a></span>
		<span class="ov_num"><a href="memberDetailView_inquiryList.php?id=<?=$id?>">문의리스트</a></span>
		-->
	</span>
</div>

		<form name="fmember" id="fmember" action="memberProc.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">	
		<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
		<input type="hidden" name="mem_Id" id="mem_Id" value="<?=$mem_Id?>">
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

				<th scope="row"><label for="id">아이디</label></th>
				<td>
					<?=$row['mem_Id']?>
					<input type="hidden" name="mem_Id"  id="mem_Id" value="<?=$mem_Id?>">
				</td>
				<th scope="row"><label for="memPwd">비밀번호</label></th>
				<td>	***</td>
			</tr>
			<tr>
				<th scope="row"><label for="mem_Nm">이름<strong class="sound_only">필수</strong></label></th>
				<td><?=$row['mem_Nm']?></td>
				<th scope="row"><label for="mem_Lv">회원 권한</label></th>
				<td>
					<?=($mem_Lv == '0' ? "관리자" : "일반회원")?>
					<input type="hidden" name="oldlev"  id="oldlev" value="<?=$mem_Lv?>">
				</td>

			</tr>
			<tr>
				<th scope="row"><label for="mem_Birth">생년월일</label></th>
				<td><?=$row['mem_Birth']?></td>
				<th scope="row"><label for="mem_Tel">휴대폰번호<strong class="sound_only">필수</strong></label></th>
				<td><?=$row['mem_Tel']?></td>
			</tr>

			<tr>
				<th scope="row"><label for="mem_Sex">성별</label></th>
				<td scope="row"><?
					if($row['mem_Sex'] == "1")  echo "여자";
					else  echo "남자";
					?>
				</td>
				<th scope="row"><label for="mem_Email">이메일</label></th>
				<td scope="row"><?=$row['mem_Email']?></td>
			</tr>			
			<tr>
				<th scope="row"><label for="mem_Memo">메모</label></th>
				<td colspan="3"><textarea name="mem_Memo" id="mem_Memo"><?=stripslashes($view['mem_Memo']);?></textarea></td>
			</tr>
			</tbody>
			</table>
		</div>




</div>    
<div class="btn_fixed_top">	
	<a href="memberList.php" id="bt_m_a_add" class="btn btn_01">회원목록</a>
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
