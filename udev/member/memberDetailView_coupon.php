<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE cou_MemId='".$id."' ";


	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	}

	if($findword != "")  {
	    if ($findType == "cou_No") {
	        $sql_search .= " AND A.cou_No LIKE :findword ";
	    } else if ($findType == "cou_MemId") {
	        $sql_search .= " AND A.cou_MemId LIKE :findword ";
	    }
	}
	
	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(A.idx)  AS cntRow FROM TB_COUPON_USE A LEFT OUTER JOIN TB_COUPON_HISTORY B ON A.cou_MemID = B.taxi_MemId AND A.idx = B.cou_UIdx  {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $cntStmt->bindValue(':findword','%'.$findword.'%');
	}

	$fr_date = trim($fr_date);
	$to_date = trim($to_date);
	$findType = trim($findType);
	$findword = trim($findword);

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];

	$cntStmt = null;

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "A.reg_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";
	
	//회원닉네임
	$memNickNm = "  , ( SELECT mem_NickNm FROM TB_MEMBERS C WHERE C.mem_Id = A.cou_MemId AND C.b_Disply = 'N' limit 1 ) AS memNickNm  ";

	$query = "";
	$query = "SELECT A.idx, A.cou_MemId, A.cou_No, A.cou_Use, A.cou_Idx, A.reg_Date {$memNickNm} FROM TB_COUPON_USE A LEFT OUTER JOIN TB_COUPON_HISTORY B ON A.cou_MemID = B.taxi_MemId AND A.idx = B.cou_UIdx {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	$stmt = $DB_con->prepare($query);
	/*
	echo $query;
	exit;
	*/
	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $stmt->bindValue(':findword','%'.$findword.'%');
	}

	$fr_date = trim($fr_date);
	$to_date = trim($to_date);
	$findType = trim($findType);
	$findword = trim($findword);

	$stmt->execute();
	$numCnt = $stmt->rowCount();


	// 페이지 타이틀	
	$sql = "select mem_NickNm from TB_MEMBERS where mem_id='".$id."' ";
	$sqltmt = $DB_con->prepare($sql);
	$sqltmt->execute();
	$sqlRow =$sqltmt->fetch();


	$qstr = "fr_date=".urlencode($fr_date)."&amp;o_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;id=".urlencode($id);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/coupon/js/coupon.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?= $id?>(<?=$sqlRow['mem_NickNm']?>)&nbsp;회원상세보기 - 쿠폰사용내역</h1>

		<style>
		.ov_num{border-right:1px solid #fff;}
		.ov_txt a{color:#fff;}
		</style>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01">
				<span class="ov_num"><a href="memberDetailView.php?id=<?=$id?>">기본정보</a> </span>
				<span class="ov_num"><a href="memberDetailView_point.php?id=<?=$id?>">캐시내역</a></span>
				<span class="ov_num"><a href="memberDetailView_order.php?id=<?=$id?>">주문내역</a></span>
				<span class="ov_num"><a href="memberDetailView_taxiSharingList.php?id=<?=$id?>">매칭내역</a></span>
				<span class="ov_txt"><a href="memberDetailView_coupon.php?id=<?=$id?>">쿠폰내역</a></span>
				<span class="ov_num"><a href="memberDetailView_inquiryList.php?id=<?=$id?>">문의리스트</a></span>
			</span>
		</div>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">
		<input type="hidden" name="id" id="id" value="<?=$id?>">
		<div>
				<strong>분류</strong>
				<select name="findType" id="findType">
					<option value="cou_No" <?if($findType=="cou_No"){?>selected<?}?>>쿠폰번호</option>
				</select>
				<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="findword" id="findword" value="<?=$findword?>" size="30"  class=" frm_input">
		</div>

		<div class="sch_last">
			<strong>기간검색</strong>
			<input type="text" name="fr_date" id="fr_date" value="<?=$fr_date?>" class="frm_input" size="11" maxlength="10">
			<label for="fr_date" class="sound_only">시작일</label>
			~
			<input type="text" name="to_date" id="to_date" value="<?=$to_date?>"  class="frm_input" size="11" maxlength="10">
			<label for="to_date" class="sound_only">종료일</label>
			<input type="submit" value="검색" class="btn_submit">

			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>

<div class="btn_fixed_top">	
	<a href="memberList.php" id="bt_m_a_add" class="btn btn_01">회원목록</a>
</div>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>쿠폰관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
		<th scope="col">순번</th>	
		<th scope="col">회원아이디</th>	
		<th scope="col">쿠폰번호</th>		 
		<th scope="col">쿠폰사용여부</th>
		<th scope="col">쿠폰등록일</th>
		<th scope="col" class="last_cell">관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);
			$from_record++;
    	    $cou_MemId = $row['cou_MemId']; 
    	    $cou_No = $row['cou_No']; 
			$cou_Use = $row['cou_Use'];
			$reg_Date = $row['reg_Date'];
			$cou_Idx = $row['cou_Idx'];
			$memNickNm = $row['memNickNm'];
			if($cou_Use == 'Y'){
				$cou_UseN = '사용';
			}else if($cou_Use == 'N'){
				$cou_UseN = '미사용';
			}
		    
    ?>
    <tr class="<?=$bg?>">
        <td><?=$from_record?></td>
		<td><a href="/udev/member/memberReg.php?mode=mod&id=<?=$cou_MemId?>"><?=$cou_MemId."<br>(".$memNickNm.")"?></a></td>
        <td><a href="/udev/coupon/couponManagerView.php?mode=view&idx=<?=$cou_Idx?>"><?=$cou_No?></a></td>
		<td><?=$cou_UseN?></td>
		<td><?=$reg_Date?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<?if($cou_Use == 'Y'){?>
			<a href="/udev/coupon/couponUseView.php?cou_UIdx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_04">상세</a>
			<?}?>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="5 class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	});

	function fvisit_submit(act)
	{
		var f = document.fvisit;
		f.action = act;
		f.submit();
	}

</script>

</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
