<?
	$menu = "2";
	$smenu = "5";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE 1 ";

	if($id != "")  {
		$sql_search .= " AND A.mem_Id = :memId ";
	}


	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	}

	if($findword != "")  {
		if ($findType == "mem_Id") {
		   $sql_search .= " AND A.mem_Id LIKE :findword "; 
		} else if ($findType == "mem_Tel") { 
			$sql_search .= " AND B.mem_Tel LIKE :findword "; 
		} else if ($findType == "idx") { 
			$sql_search .= " AND A.idx LIKE :findword "; 
		}
	}
	
	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(A.idx)  AS cntRow FROM TB_POINT_MIR A INNER JOIN TB_MEMBERS B ON A.mem_Id = B.mem_Id AND B.b_Disply = 'N' {$sql_search} " ;
	
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
	$memNmSql = "  , ( SELECT mem_NickNm FROM TB_MEMBERS B WHERE B.mem_Id = A.mem_Id AND B.b_Disply = 'N' limit 1 ) AS memNickNm   ";
	// 탈퇴회원명
	$memNmSql2 = "  , ( SELECT mem_NickNm FROM TB_MEMBERS B WHERE B.mem_Id = A.mem_Id AND B.b_Disply = 'Y' limit 1 ) AS memNickNm2  ";
	// 연락처
	$mem_Tel = " , ( SELECT mem_Tel FROM TB_MEMBERS B WHERE B.mem_Id = A.mem_Id AND B.b_Disply = 'N' limit 1 ) AS mem_Tel ";
	// 탈퇴연락처
	$mem_Tel2 = " , ( SELECT mem_Tel FROM TB_MEMBERS B WHERE B.mem_Id = A.mem_Id AND B.b_Disply = 'Y' limit 1 ) AS mem_Tel2  ";
	
	//목록
	$query = "";
	$query = "SELECT 
		A.idx
		, A.mem_Id
		, A.mir_Price
		, A.reg_Date {$memNmSql} {$memNmSql2} {$mem_Tel} {$mem_Tel2}   
		FROM TB_POINT_MIR A 
			INNER JOIN TB_MEMBERS B 
				ON A.mem_Id = B.mem_Id AND B.b_Disply = 'N' 
		{$sql_search} {$sql_order} limit  {$from_record}, {$rows} ;" ;
	
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

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);
	$sqstr = "id=".urlencode($id)."&amp;fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);
	
	

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">미르페이관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 건수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>&nbsp;
		</div>

		<form class="local_sch03 local_sch"  autocomplete="off">
		<div>
			<strong>구분</strong>
			<select name="point_Type" id="point_Type">
				<option value="point_All" <?if($point_Type=="taxi_OrdNickNm"){?>selected<?}?>>전체</option>
				<option value="point_Up" <?if($point_Type=="point_Up"){?>selected<?}?>>적립</option>
				<option value="point_Down" <?if($point_Type=="point_Down"){?>selected<?}?>>양도</option>
			</select>
		</div>
		<div>
			<strong>분류</strong>
			<select name="findType" id="findType">
				<option value="mem_Id" <?if($findType=="mem_Id"){?>selected<?}?>>아이디</option> 
				<option value="idx" <?if($findType=="idx"){?>selected<?}?>>미르환전번호</option>
				<option value="mem_Tel" <?if($findType=="mem_Tel"){?>selected<?}?>>핸드폰번호</option>
			</select>
			<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
			<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		</div>

		<div class="sch_last">
			<strong>등록일검색</strong>
			<input type="text" name="fr_date" id="fr_date" value="<?=$fr_date?>" class="frm_input" size="11" maxlength="10">
			<label for="fr_date" class="sound_only">시작일</label>
			~
			<input type="text" name="to_date" id="to_date" value="<?=$to_date?>"  class="frm_input" size="11" maxlength="10">
			<label for="to_date" class="sound_only">종료일</label>
			<input type="submit" value="검색" class="btn_submit">

			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>


	<div class="local_desc01 local_desc">
		<p>
			아래의 검색 리스트에서 마우스 커서를 잠깐 올려 놓으시면(2~3초 유지) 정보가 나옵니다.<br>
			이벤트 캐시 적립의 경우는 추천받을시, 최초추천(1회), 쿠폰적립으로 발생한 캐시 적립입니다.<br>
			주문번호의 경우 매칭관련결제, 환전의 경우 바로가기가 이동됩니다.<br>
			일자는 최근일자부터 역순으로 정렬됩니다.
		</p>
	</div>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$sqstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>미르페이 전환내역</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_id">아이디</th>
        <th scope="col" id="mb_list_tel">연락처</th>
        <th scope="col" id="mb_list_auth">환전금액</th> 
        <th scope="col" id="mb_list_mailr">환전일</th>
    </tr>
    </thead>
    <tbody>

    <?
	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);
		$from_record++;
		$memId = $row['mem_Id'];
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
		  $chkStr = "";
		  $chkClass = "td_numPoint td_num";
    ?>

    <tr class="<?=$bg?>" >
        <td headers="mb_list_idx"><?=$from_record?> </td>
        <td headers="mb_list_id"><a href="<?=DU_UDEV_DIR?>/member/memberReg.php?mode=mod&id=<?=$memId?>"><?=$memId?> (<?=$memNickNm?>)</a></td>
        <td headers="mb_list_tel"><?=$memTel?></td>
        <td headers="mb_list_point" class="<?=$chkClass?>"><?=$chkStr?> <?=number_format($row['mir_Price'])?></td> 
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
