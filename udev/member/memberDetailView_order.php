<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	
	$sql_search=" WHERE taxi_OrdMemId='".$id."' ";

	if ($od_status != "" ) {
	    $sql_search .= " AND taxi_OrdState = :od_status ";
	}
	
	if ($fr_date != "" || $to_date != "" ) {
		/*
			기존은 21번 라인 주석처리 22번 라인을 사용하고 있었으나 검색 시 cou_Sdate, cou_Edate 컬럼이 존재하지 않아 오류발생

			23번 라인을 새로 추가하여 등록일 조회임으로 reg_date로 처리 2019-01-02
		*/
	    //$sql_search.=" AND ((DATE_FORMAT(reg_Date,'%Y-%m-%d') between ':fr_date' AND ':to_date')";
	    //$sql_search.=" AND (DATE_FORMAT(cou_Sdate,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(cou_Edate,'%Y-%m-%d') <= :to_date)";
	    $sql_search.=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	}
	
	if($findword != "")  {
	    if ($findType == "taxi_OrdNo") {
	        $sql_search .= " AND taxi_OrdNo LIKE :findword ";
	    } else if ($findType == "taxi_OrdNickNm") {
	        $sql_search .= " AND taxi_OrdNickNm LIKE :findword ";
	    }
	}
	
	
	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx) AS cntRow FROM TB_ORDER  {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
	//echo $cntQuery."<BR>";
	//exit;	
	if ($od_status != "" ) {
	    $cntStmt->bindValue(":od_status",$od_status);
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
	    $sort1  = "reg_Date";
	    $sort2 = "DESC";
	}
	
	$sql_order = "order by $sort1 $sort2";
	
	//목록
	$query = "";
	$query = "SELECT idx, taxi_SIdx, taxi_RIdx, taxi_OrdNo, taxi_OrdTit, taxi_OrdNickNm, taxi_OrdTel, taxi_OrdPrice, taxi_OrdType, taxi_OrdState, reg_Date FROM TB_ORDER {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	
	//echo $query."<BR>";
	//exit;
	$stmt = $DB_con->prepare($query);
	
	if ($od_status != "" ) {
	    $stmt->bindValue(":od_status",$od_status);
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
	$findType = trim($findType);
	$findword = trim($findword);
	
	$stmt->execute();
	$numCnt = $stmt->rowCount();
	
	
	$qstr = "od_status=".urlencode($od_status)."&amp;fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;id=".urlencode($id);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 



	// 페이지 타이틀	
	$sql = "select mem_NickNm from TB_MEMBERS where mem_id='".$id."' ";
	$sqltmt = $DB_con->prepare($sql);
	$sqltmt->execute();
	$sqlRow =$sqltmt->fetch();
?>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?= $id?>(<?=$sqlRow['mem_NickNm']?>)&nbsp;회원상세보기 - 주문내역</h1>
		
		<style>
		.ov_num{border-right:1px solid #fff;}
		.ov_txt a{color:#fff;}
		</style>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01">
				<span class="ov_num"><a href="memberDetailView.php?id=<?=$id?>">기본정보</a> </span>
				<span class="ov_num"><a href="memberDetailView_point.php?id=<?=$id?>">캐시내역</a></span>
				<span class="ov_txt"><a href="memberDetailView_order.php?id=<?=$id?>">주문내역</a></span>
				<span class="ov_num"><a href="memberDetailView_taxiSharingList.php?id=<?=$id?>">매칭내역</a></span>
				<span class="ov_num"><a href="memberDetailView_coupon.php?id=<?=$id?>">쿠폰내역</a></span>
				<span class="ov_num"><a href="memberDetailView_inquiryList.php?id=<?=$id?>">문의리스트</a></span>
			</span>
		</div>


		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 건수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">
		<input type="hidden" name="id" id="id" value="<?=$id?>">
		<div>
				<strong>분류</strong>
				<label for="findType" class="sound_only">검색대상</label>
				<select name="findType" id="findType">
					<option value="taxi_OrdNo" <?if($findType=="taxi_OrdNo"){?>selected<?}?>>주문번호</option>
				</select>
				<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		</div>


        <div>
            <strong>상태</strong>
        	<span class="bg <? if ($od_status == "") { ?>all_on<? } ?>">
            <input type="radio" name="od_status" value="" id="od_status_all" <?php echo get_checked($od_status, ''); ?>>
            <label for="od_status_all">전체</label>
        	</span>
        	<span class="bg <? if ($od_status == "0") { ?>c01_on<? } ?>">
            <input type="radio" name="od_status" value="0" id="od_status_matchS" <?php echo get_checked($od_status, '0'); ?>>
            <label for="od_status_matchS">접수</label>
        	</span>
        	<span class="bg <? if ($od_status == "1") { ?>c02_on<? } ?>">
            <input type="radio" name="od_status" value="1" id="od_status_matchR" <?php echo get_checked($od_status, '1'); ?>>
            <label for="od_status_matchR">결제완료</label>
        	</span>
        	<span class="bg <? if ($od_status == "2") { ?>c03_on<? } ?>">
            <input type="radio" name="od_status" value="2" id="od_status_meetS" <?php echo get_checked($od_status, '2'); ?>>
            <label for="od_status_meetS">양도완료</label>
        	</span>
        	<span class="bg <? if ($od_status == "3") { ?>c04_on<? } ?>">
            <input type="radio" name="od_status" value="3" id="od_status_meetC" <?php echo get_checked($od_status, '3'); ?>>
            <label for="od_status_meetC">취소</label>
        	</span>
        	<span class="bg <? if ($od_status == "4") { ?>c05_on<? } ?>">
            <input type="radio" name="od_status" value="4" id="od_status_move" <?php echo get_checked($od_status, '4'); ?>>
            <label for="od_status_move">거래취소확인</label>
        	</span>
        	<span class="bg <? if ($od_status == "5") { ?>c06_on<? } ?>">
            <input type="radio" name="od_status" value="5" id="od_status_complte" <?php echo get_checked($od_status, '5'); ?>>
            <label for="od_status_complte">거래완료확인</label>
        	</span>
        </div>


		<div class="sch_last">
			<strong>등록일자</strong>
			<input type="text" id="fr_date"  name="fr_date" value="<?=$fr_date?>" class="frm_input" size="10" maxlength="10"> ~
			<input type="text" id="to_date"  name="to_date" value="<?=$to_date?>" class="frm_input" size="10" maxlength="10">
			<button type="button" onclick="javascript:set_date('오늘');">오늘</button>
			<button type="button" onclick="javascript:set_date('어제');">어제</button>
			<button type="button" onclick="javascript:set_date('이번주');">이번주</button>
			<button type="button" onclick="javascript:set_date('이번달');">이번달</button>
			<button type="button" onclick="javascript:set_date('지난주');">지난주</button>
			<button type="button" onclick="javascript:set_date('지난달');">지난달</button>
			<button type="button" onclick="javascript:set_date('전체');">전체</button>
			<input type="submit" value="검색" class="btn_submit">

			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</na

<form name="fsharlist" id="fsharlist"  method="post" autocomplete="off">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>주문관리 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="mb_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll">
        </th>
        <th scope="col" id="">주문번호</th>
        <th scope="col" id="mb_list_id">주문자</th>
        <th scope="col" id="mb_list_id">경로</th>
		<th scope="col" id="th_odrid">택시요금</th>
		<th scope="col" id="mb_list_auth">상태</th>
        <th scope="col" id="mb_list_mailc">등록일</th>
		<th scope="col" id="mb_list_mng">관리</th>
    </tr>


    </thead>
    <tbody>
    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		$i = 0;

		while($row =$stmt->fetch()) {
		$i = $i + 1;
        $bg = 'bg'.($i%2);

		$taxi_OrdState = $row['taxi_OrdState'] ;
		if($taxi_OrdState == 0) { 
			$taxiOrdState = "접수"; 
		} else if($taxi_OrdState == 1) { 
			$taxiOrdState = "결제완료"; 
		} else if($taxi_OrdState == 2) { 
			$taxiOrdState = "양도완료"; 
		} else if($taxi_OrdState == 3) { 
			$taxiOrdState = "취소"; 
		}


		$taxi_OrdTit = $row['taxi_OrdTit'];
		$taxi_OrdTit = str_replace("null","",$taxi_OrdTit);

    ?>



    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk">
            <input type="hidden" name="mb_id[<?=$row['idx']?>]" id="mb_id_<?=$row['idx']?>" value="<?=$row['idx'] ?>" >
            <input type="checkbox"  id="chk" class="chk" name="chk" value="<?=$row['idx']?>">
        </td>
        <td headers="mb_list_id" ><?=$row['taxi_OrdNo']?></td>
        <td headers="mb_list_id" ><?=$row['taxi_OrdNickNm']?></td>
        <td headers="mb_list_id" ><?=$taxi_OrdTit?></td>
		<td headers="mb_list_open" class="td_num"><?=number_format($row['taxi_OrdPrice'])?></td>
        <td headers="mb_list_id" ><a href="#" class="btn btn_a<?=$taxi_OrdState?>"><?=$taxiOrdState?></a></td>
		<td headers="mb_list_open" class="td_name sv_use"><?=$row['reg_Date']?></td>
        <td headers="mb_list_mng" class="td_mng td_mng_s">
<? if($_COOKIE['du_udev']['id'] != 'admin2'){ ?>
			<a href="javascript:chkDel('<?=$row['idx']?>')" class="btn btn_02">삭제</a>
<? } ?>
		</td>
    </tr>

    <? 
		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="10" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">	
	<a href="memberList.php" id="bt_m_a_add" class="btn btn_01">회원목록</a>
</div>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true });
	});


	function set_date(today)
	{
		<?
			 $date_term = date('w', DU_SERVER_TIME);
			 $week_term = $date_term + 7;
			 $last_term = strtotime(date('Y-m-01', DU_SERVER_TIME));
		?>
		if (today == "오늘") {
			document.getElementById("fr_date").value = "<?php echo DU_TIME_YMD; ?>";
			document.getElementById("to_date").value = "<?php echo DU_TIME_YMD; ?>";
		} else if (today == "어제") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-d', DU_SERVER_TIME - 86400); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', DU_SERVER_TIME - 86400); ?>";
		} else if (today == "이번주") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', DU_SERVER_TIME)); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', DU_SERVER_TIME); ?>";
		} else if (today == "이번달") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-01', DU_SERVER_TIME); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', DU_SERVER_TIME); ?>";
		} else if (today == "지난주") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', DU_SERVER_TIME)); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', DU_SERVER_TIME)); ?>";
		} else if (today == "지난달") {
			document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
			document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
		} else if (today == "전체") {
			document.getElementById("fr_date").value = "";
			document.getElementById("to_date").value = "";
		}
	}
</script>

</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>
