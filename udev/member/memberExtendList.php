<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE 1=1";

	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	}
		
	if($findword != "")  {
	    if($findType == "mem_Id") {
	        $sql_search .= " AND mem_Id LIKE :findword ";
	    }
	}
	

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_EXTEND_LIST {$sql_search} " ;
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
	$query = " SELECT idx, mem_Id, exte_Date, exte_Bit, reg_Date" ;
	$query .= " FROM TB_EXTEND_LIST ";
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

	//연장대기총인원
	$mcntQuery = "";
	$mcntQuery = "SELECT COUNT(idx) AS mCnt FROM TB_EXTEND_LIST  WHERE exte_Bit = 'N' " ;
	$mcntStmt = $DB_con->prepare($mcntQuery);
	$mcntStmt->execute();
	$mcRow = $mcntStmt->fetch(PDO::FETCH_ASSOC);
	$n_count = $mcRow['mCnt'];

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findMir=".urlencode($findMir)."&amp;rows=".urlencode($rows)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";		//헤더 
	include "../common/inc/inc_menu.php";		//메뉴 
	include "../common/inc/inc_mir.php";		//미르페이 
?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<script type="text/javascript">
	function approve_extend(idx){
		var con_test = confirm("해당 요청을 승인하시겠습니까?\n(승인완료 시 요청기간 만큼 연장됩니다.)");
		if(con_test == true){
			var allData = { "idx": idx};
			$.ajax({
			url:"/udev/member/memberExtendProc.php",
				type:'POST',
				dataType : 'json',
				data: allData,
				success:function(data){
					alert(data.Msg);
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
        <div class="container_wr">
        <h1 id="container_title">회원관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 신청 건 수 </span><span class="ov_num"><?=number_format($totalCnt);?> 건</span>&nbsp;
			<span class="btn_ov01"> <span class="ov_txt">연장대기 건 수</span><span class="ov_num"><?=number_format($n_count);?> 건</span>
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">

		<div>
    		<strong>분류</strong>
    		<select name="findType" id="findType">
    			<option value="mem_Id" <?if($findType=="mem_Id"){?>selected<?}?>>아이디</option>
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
        <th scope="col" id="mb_list_id">아이디(이름)</th>
		<th scope="col" id="mb_list_mailr">연장신청기간</th>	
		<th scope="col" id="mb_list_mailr">연장승인여부</th>	
        <th scope="col" id="mb_list_mailr">접속가능일</th>	
        <th scope="col" id="mb_list_mailr">등록일</th>	
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
			$exte_Date = $row['exte_Date'];
			if($exte_Date == '1'){
				$exteDate = '30일';
			}else if($exte_Date == '2'){
				$exteDate = '60일';
			}else if($exte_Date == '3'){
				$exteDate = '90일';
			}
			$exte_Bit = $row['exte_Bit'];
			if($exte_Bit == 'Y'){
				$exteBit = '연장완료';
			}else{
				$exteBit = '승인대기';
			}

			$reg_Date = $row['reg_Date'];
			if($reg_Date != ""){
				$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
			}else{
				$regDate = "-";
			}
			$nmQuery = "
				SELECT mem_Nm, use_Date
				FROM TB_MEMBERS 
				WHERE mem_Id = :mem_Id AND b_Disply = 'N' " ;
			$nmstmt = $DB_con->prepare($nmQuery);
			$nmstmt->bindparam(":mem_Id",$memId);
			$nmstmt->execute();
			$nmrow =$nmstmt->fetch();
			$memNm = $nmrow['mem_Nm'];
			$use_Date = $nmrow['use_Date'];
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
        <td headers="mb_list_id"><?=$memId."<br>(".$memNm.")"?></td>
        <td headers="mb_list_lastcall" class="td_exteDate"><?=$exteDate?></td>
        <td headers="mb_list_lastcall" class="td_exteBit"><?=$exteBit?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$useDate?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$regDate?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
		<?
			if($exte_Bit == 'Y'){
		?>
					-
		<?
			}else{
		?>
				<a href="javascript:approve_extend('<?=$idx?>')" class="btn btn_02">승인</a>
		<?
			}
		?>
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
