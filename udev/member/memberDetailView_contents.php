<?
	$menu = "1";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE reg_Id='".$id."' ";


	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	}

	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_CONTENTS   {$sql_search} " ;
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
		$sort1  = "reg_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";
	
	$query = "";
	$query = "SELECT * FROM TB_CONTENTS {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
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
	$sql = "SELECT * FROM TB_CONTENTS WHERE reg_id='".$id."' ";
	$sqltmt = $DB_con->prepare($sql);
	$sqltmt->execute();
	$sqlRow =$sqltmt->fetch();

	// 회원 닉네임
	$name_Sql = "SELECT * FROM TB_MEMBERS WHERE mem_Id = '".$id."'; ";
	$name_sqltmt = $DB_con->prepare($name_Sql);
	$name_sqltmt->execute();
	$name_sqlRow =$name_sqltmt->fetch();



	$qstr = "fr_date=".urlencode($fr_date)."&amp;o_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;id=".urlencode($id);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/coupon/js/coupon.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?= $id?>(<?=$name_sqlRow['mem_NickNm']?>)&nbsp;회원상세보기 - 등록한 지도 내역</h1>

		<style>
		.ov_num{border-right:1px solid #fff;}
		.ov_txt a{color:#fff;}
		</style>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01">
				<span class="ov_num"><a href="memberDetailView.php?id=<?=$id?>">기본정보</a> </span>
				<span class="ov_txt"><a href="memberDetailView_contents.php?id=<?=$id?>">등록한 지도 내역</a></span>
				<span class="ov_num"><a href="memberDetailView_report.php?id=<?=$id?>">신고내역</a></span>
			</span>
		</div>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">
		<input type="hidden" name="id" id="id" value="<?=$id?>">

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
	<a href="memberList.php" id="bt_m_a_add" class="btn btn_01">지도목록</a>
</div>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>지도관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
		<th scope="col">순번</th>	
		<th scope="col">지도명</th>	
		<th scope="col">카테고리</th>		 
		<th scope="col">공개여부</th>
		<th scope="col">최근수정일</th>
		<th scope="col">등록일</th>
		<!--
		<th scope="col" class="last_cell">관리</th>
		-->
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);
			$from_record++;
    	    $con_Name = $row['con_Name']; 
    	    $category = $row['category']; 
			if($row['category'] == "1") { 
				$c_Disply = "음식"; 
			} else if($row['category'] == "2") { 
				$c_Disply = "음료"; 
			} else if($row['category'] == "3") { 
				$c_Disply = "디저트"; 
			} else if($row['category'] == "4") { 
				$c_Disply = "여행"; 
			} else if($row['category'] == "5") { 
				$c_Disply = "오락"; 
			} else if($row['category'] == "6") { 
				$c_Disply = "풍경"; 
			} else if($row['category'] == "7") { 
				$c_Disply = "병원/약국"; 
			} else if($row['category'] == "8") { 
				$c_Disply = "기타"; 
			}
			if($row['open_Bit'] == "0") { 
				$o_Disply = "전체공개"; 
			} else if($row['open_Bit'] == "1") { 
				$o_Disply = "비공개"; 
			}
			$admin_Bit = $row['admin_Bit'];
			$mod_Date = $row['mod_Date'];
			if($mod_Date != ""){
				$modDate = substr($mod_Date,0,10)."<br>(".substr($mod_Date,11,5).")";
			}else{
				$modDate = "-";
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
		<td><?=$con_Name?></td>
        <td><?=$c_Disply?></td>
		<td><?=$o_Disply?></td>
		<td><?=$modDate?></td>
		<td><?=$regDate?></td>
		<!--
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<?if($cou_Use == 'Y'){?>
			<a href="/udev/coupon/couponUseView.php?cou_UIdx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_04">상세</a>
			<?}?>
		</td>
		-->
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="5" class="empty_table">자료가 없습니다.</td>
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
