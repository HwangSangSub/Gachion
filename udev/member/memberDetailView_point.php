<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	$DB_con = db1();
	

	// 페이지 타이틀	
	$sql = "select mem_NickNm from TB_MEMBERS where mem_id='".$id."' ";
	$sqltmt = $DB_con->prepare($sql);
	$sqltmt->execute();
	$sqlRow =$sqltmt->fetch();

	$titNm = $id."(".$sqlRow['mem_NickNm'].")&nbsp;회원상세보기 - 캐시적립/차감내역";



	$sql_search=" WHERE 1 ";

	if($id != "")  {
		$sql_search .= " AND A.taxi_MemId = :memId ";
	}

	if($point_Type == "point_Up")  {
		$sql_search .= " AND A.taxi_Sign = 0 ";
	}else if($point_Type == "point_Down"){
		$sql_search .= " AND A.taxi_Sign = 1 ";
	}

	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	}

	if($findword != "")  {
		if ($findType == "taxi_memId") {
		   $sql_search .= " AND A.taxi_memId LIKE :findword "; 
		} else if ($findType == "taxi_SIdx") {
			$sql_search .= " AND B.taxi_SIdx LIKE :findword "; 
		} else if ($findType == "taxi_OrdNo") { 
			$sql_search .= " AND A.taxi_OrdNo LIKE :findword "; 
		} else if ($findType == "mem_Tel") { 
			$sql_search .= " AND C.mem_Tel LIKE :findword "; 
		}
	}
	
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(A.idx)  AS cntRow FROM TB_POINT_HISTORY A LEFT OUTER JOIN TB_ORDER B ON A.taxi_OrdNo = B.taxi_OrdNo INNER JOIN TB_MEMBERS C ON A.taxi_MemId = C.mem_Id AND C.b_Disply = 'N' {$sql_search} " ;
	
	$cntStmt = $DB_con->prepare($cntQuery);

	if($id != "")  {
	    $cntStmt->bindValue(":memId",$id);
	}

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}
	if($findword != "")  {
		$cntStmt->bindValue(':findword','%'.$findword.'%');
	}

	$fr_date = trim($fr_date);
	$to_date = trim($to_date);

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

	// 회원명
	$memNmSql = "  , ( SELECT mem_NickNm FROM TB_MEMBERS C WHERE C.mem_Id = A.taxi_MemId AND C.b_Disply = 'N' limit 1 ) AS memNickNm  ";
	// 탈퇴회원명
	$memNmSql2 = "  , ( SELECT mem_NickNm FROM TB_MEMBERS C WHERE C.mem_Id = A.taxi_MemId AND C.b_Disply = 'Y' limit 1 ) AS memNickNm2  ";
	// 연락처
	$mem_Tel = " , ( SELECT mem_Tel FROM TB_MEMBERS C WHERE C.mem_Id = A.taxi_MemId AND C.b_Disply = 'N' limit 1 ) AS mem_Tel ";
	// 탈퇴연락처
	$mem_Tel2 = " , ( SELECT mem_Tel FROM TB_MEMBERS C WHERE C.mem_Id = A.taxi_MemId AND C.b_Disply = 'Y' limit 1 ) AS mem_Tel2  ";
	// 연락처
	$mem_DC = " , ( SELECT memDc FROM TB_MEMBER_LEVEL D WHERE D.memLv = C.mem_Lv AND C.b_Disply = 'N' limit 1 ) AS mem_DC ";
	//목록
	$query = "";
	$query = "SELECT A.idx, A.taxi_OrdNo, A.taxi_memId, A.taxi_OrdPoint, A.taxi_Memo, A.taxi_Sign, A.reg_Date, A.taxi_PState, A.taxi_OrdType, B.taxi_SIdx {$memNmSql} {$memNmSql2} {$mem_Tel} {$mem_Tel2} {$mem_DC}  FROM TB_POINT_HISTORY A LEFT OUTER JOIN TB_ORDER B ON A.taxi_OrdNo = B.taxi_OrdNo INNER JOIN TB_MEMBERS C ON A.taxi_MemId = C.mem_Id AND C.b_Disply = 'N' {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ;" ;
	
	//echo $query;
	//exit;
	$stmt = $DB_con->prepare($query);
	if($id != "")  {
	    $stmt->bindValue(":memId",$id);
	}

	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
		$stmt->bindValue(':findword','%'.$findword.'%');
	}
	$fr_date = trim($fr_date);
	$to_date = trim($to_date);

	$stmt->execute();
	$numCnt = $stmt->rowCount();


	$DB_con = null;

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;mode=".urlencode($mode);
	$sqstr = "id=".urlencode($id)."&amp;fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;mode=".urlencode($mode);
	
	

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 



	

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?= $titNm?></h1>

		<style>
		.ov_num{border-right:1px solid #fff;}
		.ov_txt a{color:#fff;}
		</style>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01">
				<span class="ov_num"><a href="memberDetailView.php?id=<?=$id?>">기본정보</a> </span>
				<span class="ov_txt"><a href="memberDetailView_point.php?id=<?=$id?>">캐시내역</a></span>
				<span class="ov_num"><a href="memberDetailView_order.php?id=<?=$id?>">주문내역</a></span>
				<span class="ov_num"><a href="memberDetailView_taxiSharingList.php?id=<?=$id?>">매칭내역</a></span>
				<span class="ov_num"><a href="memberDetailView_coupon.php?id=<?=$id?>">쿠폰내역</a></span>
				<span class="ov_num"><a href="memberDetailView_inquiryList.php?id=<?=$id?>">문의리스트</a></span>
			</span>
		</div>


<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$sqstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>캐시내역 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_id">아이디</th>
        <th scope="col" id="mb_list_tel">연락처</th>
        <th scope="col" id="mb_list_sidx">노선번호</th>
        <th scope="col" id="mb_list_mailc">주문번호</th>
        <th scope="col" id="mb_list_auth">캐시구분</th> 
        <th scope="col" id="mb_list_auth">캐시</th> 
        <th scope="col" id="mb_list_mailr">등록일</th>
    </tr>
    </thead>
    <tbody>

    <?
	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);
		$from_record++;
		$memId = $row['taxi_memId'];
		$regDate = $row['reg_Date'];
		$reg_Date = substr(trim($regDate),0,9);
		$memNickNm1 = $row['memNickNm'] ;
		$memNickNm2 = $row['memNickNm2'] ;

		if ($memNickNm1 != "" ) {
			$memNickNm = $memNickNm1;
		} else if ($memNickNm2 != "" ) {
			$memNickNm = $memNickNm2;
		} else {
			$memNickNm = "비회원";
		}
		$memTel1 = $row['mem_Tel'];
		$memTel2 = $row['mem_Tel2'];

		if($memTel1 != ''){
			$memTel = $memTel1;
		}else if($memTel2 != ''){
			$memTel = $memTel2;
		}else{
			$memTel = '-';
		}
	   if ($row['taxi_SIdx'] == ''){
			$taxi_SIdx = '-';
	   }else{
			$taxi_SIdx = $row['taxi_SIdx'];
	   }
	   if ($row['taxi_Sign'] == '0') {
		  $chkStr = "";
		  $chkClass = "td_numPoint td_num";
	   } else {
		  $chkStr = "-";
		  $chkClass = "td_numcancel td_num";
	   }
	   $mem_DC = $row['mem_DC'];
		//사용중인 수수료 할인형 쿠폰 조회하기
	   $taxi_Memo = $row['taxi_Memo'];
	   
		$taxiSign = trim($row['taxi_Sign']);					// 캐시구분 (0: +, 1: -)
		$taxi_PState = trim($row['taxi_PState']);	        // 구분 (0: 매칭, 1: 적립, 2: 환전)
		$taxi_OrdType = trim($row['taxi_OrdType']);	        // 결제타입 (1: 카드, 2: 보유캐시결제)
		if($taxi_PState == '0' && $taxiSign == '0' && $taxi_OrdType == '2'){
			$taxiPState = '캐시 적립';
			$pointchk = "0";
		}else if($taxi_PState == '4' && $taxiSign == '0' && $taxi_OrdType == '1'){
			$taxiPState = '캐시 적립 (카드)';
			$pointchk = "0";
		}else if($taxi_PState == '0' && $taxiSign == '1'){
			$taxiPState = '캐시 사용';
			$pointchk = "0";
		}else if($taxi_PState == '1'){
			$taxiPState = '이벤트 캐시 적립';
			$pointchk = "1";
		}else if($taxi_PState == '2' && $taxiSign == '0'){
			$taxiPState = '캐시 환전 (재적립)';
			$pointchk = "2";
		}else if($taxi_PState == '2' && $taxiSign == '1'){
			$taxiPState = '캐시 환전';
			$pointchk = "2";
		}else if($taxi_PState == '3' && $taxiSign == '1'){
			$taxiPState = '미르랜드 전환';
			$pointchk = "3";
		// 관리자 캐시 임의 관리
		}else if($taxi_PState == '6' && $taxiSign == '0'){
			$taxiPState = '관리자 적립';
			$pointchk = "";
		}else if($taxi_PState == '6' && $taxiSign == '1'){
			$taxiPState = '관리자 차감';
			$pointchk = "";
		}
    ?>

    <tr class="<?=$bg?>" title="<?=$taxi_Memo?>">
        <td headers="mb_list_idx"><?=$from_record?> </td>
        <td headers="mb_list_id"><a href="<?=DU_UDEV_DIR?>/member/memberReg.php?mode=mod&id=<?=$memId?>"><?=$memId?> (<?=$memNickNm?>)</a></td>
        <td headers="mb_list_tel"><?=$memTel?></td>
        <td headers="mb_list_tel"><?=($taxi_SIdx == "-"?'':'<a href="'.DU_UDEV_DIR.'/taxiSharing/taxiSharingReg.php?mode=mod&idx='.$taxi_SIdx.'&'.$qstr.'&page='.$page.'" >')?><?=$taxi_SIdx?><?=($taxi_SIdx == "-"?'':'</a>')?></td>
		<? if($pointchk == "0"){ ?> <!--일반 주문-->
			<td headers="mb_list_ordno" ><a href="<?=DU_UDEV_DIR?>/order/orderList.php?findType=taxi_OrdNo&findword=<?=$row['taxi_OrdNo']?>"><?=$row['taxi_OrdNo']?></a></td>
		<? }else if($pointchk == "1"){ ?> <!--이벤트캐시적립-->
			<td headers="mb_list_ordno" ><?=$row['taxi_OrdNo']?></td>
		<? }else if($pointchk == "2"){ ?> <!--환전-->
			<td headers="mb_list_ordno" ><a href="<?=DU_UDEV_DIR?>/account/pointExcList.php?findType=idx&findword=<?=$row['taxi_OrdNo']?>"><?=$row['taxi_OrdNo']?></a></td>
		<? }else if($pointchk == "3"){ ?> <!--미르랜드 환전-->
			<td headers="mb_list_ordno" ><a href="<?=DU_UDEV_DIR?>/member/mirpayList.php?findType=idx&findword=<?=$row['taxi_OrdNo']?>"><?=$row['taxi_OrdNo']?></a></td>
		<? }else{ ?> <!--그외-->
			<td headers="mb_list_ordno" ><?=$row['taxi_OrdNo']?></td>
		<? } ?>
        <td headers="mb_list_point"><?=$taxiPState?></td> 
        <td headers="mb_list_point" class="<?=$chkClass?>"><?=$chkStr?> <?=number_format($row['taxi_OrdPoint'])?></td> 
        <td headers="mb_list_lastcall" class="td_date"><?=$regDate?></td>
    </tr>
    <? 

		}
		$stmt = null;
		$couPonStmt = null;
	?>   
	<? } else { ?>
	<tr>
		<td colspan="7" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>
<div class="btn_fixed_top">	
	<a href="memberList.php" id="bt_m_a_add" class="btn btn_01">회원목록</a>
	<a href="pointReg.php?mode=reg&taxi_MemTeype=pub&id=<?= $id?>" id="bt_m_a_add" class="btn btn_02">캐시등록</a>
</div>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$sqstr"); ?>
</nav>

<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	});

</script>

</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>
