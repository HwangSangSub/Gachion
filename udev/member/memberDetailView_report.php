<?
	$menu = "1";
	$smenu = "3";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE reg_Id = '".$id."'";

	if($findword != "")  {
		$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
	}

	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx) AS cntRow ";
	$cntQuery .= ",SUM(CASE WHEN admin_Bit = 'Y' THEN 1 ELSE 0 END) AS AY_CNT ";
	$cntQuery .= ",SUM(CASE WHEN admin_Bit = 'N' THEN 1 ELSE 0 END) AS AN_CNT ";
	$cntQuery .= ",SUM(CASE WHEN penalty_Bit = 'N' THEN 1 ELSE 0 END) AS N_CNT ";
	$cntQuery .= ",SUM(CASE WHEN penalty_Bit = 'Y' THEN 1 ELSE 0 END) AS Y_CNT ";
	$cntQuery .= ",SUM(CASE WHEN penalty_Bit = 'A' THEN 1 ELSE 0 END) AS A_CNT ";
	$cntQuery .= ",SUM(CASE WHEN penalty_Bit = 'B' THEN 1 ELSE 0 END) AS B_CNT ";
	$cntQuery .= "FROM TB_MEMBERS_REPORT  {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $cntStmt->bindValue(":findType",$findType);		
	    $cntStmt->bindValue(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];
	$AN_CNT = $row['AN_CNT'];
	$AY_CNT = $row['AY_CNT'];
	$Y_CNT = $row['Y_CNT'];
	$N_CNT = $row['N_CNT'];
	$A_CNT = $row['A_CNT'];
	$B_CNT = $row['B_CNT'];

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
	$query = "SELECT * FROM TB_MEMBERS_REPORT {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	$stmt = $DB_con->prepare($query);

	if($findword != "")  {
	    $stmt->bindValue(":findType",$findType);		
	    $stmt->bindValue(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$stmt->execute();
	$numCnt = $stmt->rowCount();


	// 페이지 타이틀	
	$sql = "select mem_NickNm from TB_MEMBERS where mem_id='".$id."' ";
	$sqltmt = $DB_con->prepare($sql);
	$sqltmt->execute();
	$sqlRow =$sqltmt->fetch();

	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;id=".urlencode($id);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/etc/js/event.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?= $id?>(<?=$sqlRow['mem_NickNm']?>)&nbsp;회원상세보기 - 신고내역</h1>

		<style>
		.ov_num{border-right:1px solid #fff;}
		.ov_txt a{color:#fff;}
		</style>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01">
				<a href="memberDetailView.php?id=<?=$id?>"><span class="ov_num">기본정보</span></a>
				<a href="memberDetailView_contents.php?id=<?=$id?>"><span class="ov_num">등록한 지도 내역</span></a>
				<a href="memberDetailView_report.php?id=<?=$id?>"><span class="ov_txt">신고내역</span></a>
			</span>
		</div>


		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 신고건수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 답변대기 </span><span class="ov_num"><?=number_format($AN_CNT);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 답변완료 </span><span class="ov_num"><?=number_format($AY_CNT);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 신고처리대기 </span><span class="ov_num"><?=number_format($N_CNT);?>건 </span>
			<span class="btn_ov01"><span class="ov_txt">총 신고부적합 </span><span class="ov_num"><?=number_format($Y_CNT);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 페널티A </span><span class="ov_num"><?=number_format($A_CNT);?>건 </span>&nbsp;
			<span class="btn_ov01"><span class="ov_txt">총 페널티B </span><span class="ov_num"><?=number_format($B_CNT);?>건 </span>
		</div>
		<form class="local_sch03 local_sch"  autocomplete="off">
		<input type="hidden" name="id" id="id" value="<?=$id?>">
		<div>
			<strong>분류</strong>
			<select name="findType" id="findType">
				<option value="b_MemId" <?if($findType=="b_MemId"){?>selected<?}?>>제목</option>
			</select>
			<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
			<input type="text" name="findword" id="findword" value="<?=$findword?>" size="30"  class=" frm_input">

			<input type="submit" value="검색" class="btn_submit">
			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>


<div class="btn_fixed_top">	
	<a href="memberList.php" id="bt_m_a_add" class="btn btn_01">회원목록</a>
</div>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>이벤트 배너 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col">순번</th>
        <th scope="col">신고한지점</th>
        <th scope="col">신고사유</th>
		<th scope="col">신고자</a></th>		 
		<th scope="col">신고일</th>
		<th scope="col">본사처리여부</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);
	    $from_record++;
		$p_idx = $row['place_Idx'];
		$pname_query = "
			SELECT place_Name 
			FROM TB_PLACE
			WHERE idx = :idx
			LIMIT 1;
		";
		$pname_stmt = $DB_con->prepare($pname_query);
	    $pname_stmt->bindValue(":idx",$p_idx);		
		$pname_stmt->execute();
		$pname_Row =$pname_stmt->fetch();

		$place_Name = $pname_Row['place_Name'];

		$b_Part = $row['b_Part'];
		if($b_Part == 1){
			$bPart = "매칭생성";
		}else if($b_Part == 2){
			$bPart = "매칭신청";
		}else if($b_Part == 3){
			$bPart = "게시판";
		}
		$reg_Id = $row['reg_Id'];
		$name_query = "
			SELECT mem_NickNm 
			FROM TB_MEMBERS
			WHERE mem_Id = :mem_Id
			LIMIT 1;
		";
		$name_stmt = $DB_con->prepare($name_query);
	    $name_stmt->bindValue(":mem_Id",$reg_Id);		
		$name_stmt->execute();
		$name_Row =$name_stmt->fetch();

		$mem_NickNm = $name_Row['mem_NickNm'];
		$a_State = $row['admin_Bit'];
		if($a_State == 'Y'){
			$aState = '조치완료';
		}else if($a_State == 'N'){
			$aState = '조치대기중';
		}
		$reg_Date = $row['reg_Date'];
		if($reg_Date != ""){
			$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
		}else{
			$regDate = "-";
		}
    ?>
    <tr class="<?=$bg?>">
		<td><?=$from_record?></td>
		<td><?=$place_Name?></td>
		<td><?=$row['report']?></td>
		<td><?=$row['reg_Id']."<br>(".$mem_NickNm.")"?></td>
		<td><?=$regDate?></td>
		<td><?=$aState?></td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="6" class="empty_table">자료가 없습니다.</td>
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
