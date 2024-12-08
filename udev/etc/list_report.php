<?
	$menu = "3";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE 1=1 ";
		
	if($findword != "")  {
	    if ($findType == "reg_Id") {
	        $sql_search .= " AND reg_Id LIKE :findword ";
	    }else if($findType == "mem_Tel"){
			$sql_search .= " AND mem_Tel LIKE :findword ";
	    }
	}
	

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_MEMBERS_REPORT {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $cntStmt->bindValue(':findword','%'.trim($findword).'%');
	}

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];


	if($rows == ''){
		$rows = '10';
	}
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "reg_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by {$sort1} {$sort2}";

	//목록
	$query = "";
	$query = " SELECT *" ;
	$query .= " FROM TB_MEMBERS_REPORT ";
	$query .= " {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ;";
	//echo $query."<BR>";
	//exit;

	$stmt = $DB_con->prepare($query);

	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $stmt->bindValue(':findword','%'.trim($findword).'%');
	}
	
	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findMir=".urlencode($findMir)."&amp;rows=".urlencode($rows)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";		//헤더 
	include "../common/inc/inc_menu.php";		//메뉴 
	include "../common/inc/inc_mir.php";		//미르페이 
?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">신고관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 신고 수 </span><span class="ov_num"><?=number_format($totalCnt);?>개 </span>&nbsp;
			<!--
			<span class="btn_ov01"> <span class="ov_txt">탈퇴  </span><span class="ov_num"><?=number_format($leave_count);?>명</span>
			-->
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">

		<div>
			<strong>리스트출력</strong>
			<select id="rows" name="rows" onchange="$('.local_sch').submit();">
				<option value="10" <? if ( $rows == "10") { ?>selected="selected"<? } ?>>10개 씩 보기</option>
				<option value="15" <? if ( $rows == "15" ) { ?>selected="selected"<? } ?>>15개 씩 보기</option>
				<option value="20" <? if ( $rows == "20" ) { ?>selected="selected"<? } ?>>20개 씩 보기</option>
			</select>
		</div>
		
		<div>
    		<strong>분류</strong>
    		<select name="findType" id="findType">
    			<option value="reg_Id" <?if($findType=="reg_Id"){?>selected<?}?>>등록자</option>
    			<!--<option value="mem_Tel" <?if($findType=="mem_Tel"){?>selected<?}?>>연락처</option>-->
    		</select>
    		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>신고관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
		<th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_idx">신고한지점</th>
		<th scope="col" id="mb_list_open">신고사유</th>
        <th scope="col" id="mb_list_mailr">신고자</th>		
        <th scope="col" id="mb_list_mailr">신고일</th>		
        <th scope="col" id="mb_list_mailr">본사처리여부</th>
		<th scope="col" id="mb_list_mng"  class="last_cell">관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
			$from_record++;
			$idx = $row['idx'];
			$place_Idx = $row['place_Idx'];
			$p_name_query = "
				SELECT place_Name
				FROM TB_PLACE
				WHERE idx = :place_Idx
				LIMIT 1;
			";
			$p_name_stmt = $DB_con->prepare($p_name_query);
			$p_name_stmt->bindValue(':place_Idx',$place_Idx);
			$p_name_stmt->execute();
			$p_name_stmt->setFetchMode(PDO::FETCH_ASSOC);
			$p_name_row =$p_name_stmt->fetch();
			$place_Name = $p_name_row['place_Name'];

			$report = $row['report'];
			$admin_Bit = $row['admin_Bit'];
			if($admin_Bit == "N"){
				$adminBit = "처리대기";
			}else{
				$adminBit = "처리완료";
			}
			$reg_Id = $row['reg_Id'];
			$reg_Date = $row['reg_Date'];
			if($reg_Date != ""){
				$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
			}else{
				$regDate = "-";
			}
    ?>
    <tr class="<?=$bg?>">
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_idx"  class="td_name"><a href=""><?=$place_Name?></a></td>
		<td headers="mb_list_open"><?=$report?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$reg_Id?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$regDate?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$adminBit?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<a href="reg_report.php?mode=mod&idx=<?=$idx?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">상세</a>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="8" class="empty_table">자료가 없습니다.</td>
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

</script>

</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;
	$mcntStmt = null;
	$mstmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
