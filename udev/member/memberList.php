<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE b_Disply = 'N' ";

	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	}
		
	if($findword != "")  {
	    if ($findType == "mem_Nm") {
	        $sql_search .= " AND mem_Nm LIKE :findword ";
	    } else if ($findType == "mem_Id") {
	        $sql_search .= " AND mem_Id LIKE :findword ";
	    }else if($findType == "mem_Tel"){
			$sql_search .= " AND mem_Tel LIKE :findword ";
	    }
	}
	

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(mem_Id)  AS cntRow FROM TB_MEMBERS {$sql_search} " ;
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

	$sql_order = "order by $sort1 $sort2";

	//목록
	$query = "";
	$query = " SELECT idx, mem_Id, mem_Nm, mem_NickNm, mem_Tel, login_Date, reg_Date" ;
	$query .= " FROM TB_MEMBERS ";
	$query .= " {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ";
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

	//탈퇴회원수
	$mcntQuery = "";
	$mcntQuery = "SELECT COUNT(idx) AS mCnt FROM TB_MEMBERS  WHERE b_Disply = 'Y' " ;
	$mcntStmt = $DB_con->prepare($mcntQuery);
	$mcntStmt->execute();
	$mcRow = $mcntStmt->fetch(PDO::FETCH_ASSOC);
	$leave_count = $mcRow['mCnt'];

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findMir=".urlencode($findMir)."&amp;rows=".urlencode($rows)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";		//헤더 
	include "../common/inc/inc_menu.php";		//메뉴 
	include "../common/inc/inc_mir.php";		//미르페이 
?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">회원관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총회원수 </span><span class="ov_num"><?=number_format($totalCnt);?>명 </span>&nbsp;
			<span class="btn_ov01"> <span class="ov_txt">탈퇴  </span><span class="ov_num"><?=number_format($leave_count);?>명</span>
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
    			<option value="mem_Nm" <?if($findType=="mem_Nm"){?>selected<?}?>>이름</option>
    			<option value="mem_Id" <?if($findType=="mem_Id"){?>selected<?}?>>아이디</option>
    			<option value="mem_Tel" <?if($findType=="mem_Tel"){?>selected<?}?>>연락처</option>
    		</select>
    		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		</div>
		
		<div class="sch_last">
			<strong>가입일</strong>
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
		상세보기 시 접속가능기간을 연장 할 수 있습니다.
    </p>
</div>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>회원관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll" onclick="check_all(this.form)">
        </th>
		<th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_id">아이디(닉네임)</th>
		<th scope="col" id="mb_list_id">휴대폰</th>
        <th scope="col" id="mb_list_mailr">최근접속일</th>
        <th scope="col" id="mb_list_mailr">가입일</th>
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
			$memId = $row['mem_Id'];

			if($row['b_Disply'] == "N") { 
				$b_Disply = "정상"; 
			} else { 
				$b_Disply = "탈퇴"; 
			}
			$memTel = $row['mem_Tel'];
			$memNm = $row['mem_Nm'];
			$mem_NickNm = $row['mem_NickNm'];
			$memId = $row['mem_Id'];

			$reg_Date = $row['reg_Date'];
			if($reg_Date != ""){
				$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
			}else{
				$regDate = "-";
			}
			$login_Date = $row['login_Date'];
			if($login_Date != ""){
				$last_Login = substr($login_Date,0,10)."<br>(".substr($login_Date,11,5).")";
			}else{
				$last_Login = "-";
			}
			$use_Date = $row['use_Date'];
			if($use_Date != ""){
				$useDate = substr($use_Date,0,10);
			}else{
				$useDate = "-";
			}
    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="mb_id[<?=$idx?>]" id="mb_id_<?=$idx?>" value="<?=$memId?>" >
            <input type="checkbox" id="chk_<?=$idx?>" class="chk" name="chk[]" value="<?=$idx?>">
        </td>
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_id"><a href="memberDetailView.php?id=<?=$memId?>"><?=$memId."<br>(".$mem_NickNm.")"?></a></td>
		<td headers="mb_list_open" class="td_name td_mng_s"><?=$memTel?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$last_Login?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$regDate?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<a href="memberDetailView.php?id=<?=$memId?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_02">상세</a>
			<a href="memberReg.php?mode=mod&id=<?=$memId?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">수정</a>
		</td>
    </tr>
    <? 

		}
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
