<?
	$menu = "1";
	$smenu = "3";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE A.b_Disply = 'Y' ";

	if ($fr_date != "" || $to_date != "" ) {
		$sql_search.=" AND (DATE_FORMAT(B.leaved_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(B.leaved_Date,'%Y-%m-%d') <= :to_date)";
	}

	if($findword != "")  {
	    if ($findType == "mem_NickNm") {
	        $sql_search .= " AND A.mem_NickNm LIKE :findword ";
	    } else if ($findType == "mem_Id") {
	        $sql_search .= " AND A.mem_Id LIKE :findword ";
	    }
	}

	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(A.mem_Id) AS cntRow FROM TB_MEMBERS A {$sql_search} " ;
	//echo $cntQuery."<BR>";
	
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}
	
	if($findword != "")  {
	    $cntStmt->bindValue(':findword','%'.$findword.'%');
	}

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];

	$cntStmt = null;

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "A.leaved_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";

	//목록
	$query = "";
	$query = "  SELECT A.idx, A.mem_Id, A.mem_NickNm, A.mem_OS, A.reg_Date, A.mem_SnsChk, A.leaved_Date, A.mem_Tel  FROM TB_MEMBERS A " ;
	$query .= "	{$sql_search} {$sql_order} limit  {$from_record}, {$rows} ";
	
	//echo $query."<BR>";
	//exit;
	$stmt = $DB_con->prepare($query);

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


	$qstr = "fr_date=".urlencode($fr_date)."&amp;o_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">탈퇴회원관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 탈퇴회원수 </span><span class="ov_num"><?=number_format($totalCnt);?>명 </span>&nbsp;
		</div>


	<form class="local_sch03 local_sch"  autocomplete="off">
	<div>
		<strong>분류</strong>
		<select name="findType" id="findType">
			<option value="mem_Id" <?if($findType=="mem_Id"){?>selected<?}?>>아이디</option>
			<option value="mem_NickNm" <?if($findType=="mem_NickNm"){?>selected<?}?>>닉네임</option>
		</select>
		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
	</div>

	<div class="sch_last">
		<strong>탈퇴일검색</strong>
		<input type="text" name="fr_date" id="fr_date" value="<?=$fr_date?>" class="frm_input" size="11" maxlength="10">
		<label for="fr_date" class="sound_only">시작일</label>
		~
		<input type="text" name="to_date" id="to_date" value="<?=$to_date?>"  class="frm_input" size="11" maxlength="10">
		<label for="to_date" class="sound_only">종료일</label>
		<input type="submit" value="검색" class="btn_submit">

		<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
	</div>
	</form>


<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>탈퇴회원관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_id">아이디</th>
        <th scope="col" id="mb_list_name">닉네임</th>
        <th scope="col" id="mb_list_tel">연락처</th>
		<!--
        <th scope="col" id="mb_list_mailc">탈퇴사유</th>
        <th scope="col" id="mb_list_mailc">탈퇴상세설명</th>
		-->
        <th scope="col" id="mb_list_mailc">SNS</th>
        <th scope="col" id="mb_list_mailc">OS</th>
        <th scope="col" id="mb_list_mailr">가입일</th>
        <th scope="col" id="mb_list_mailr">탈퇴일</th>
        <th scope="col" id="mb_list_auth">상태</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
			$from_record++;
			if($row[b_Disply] == "N") { 
				$b_Disply = "정상"; 
			} else { 
				$b_Disply = "탈퇴"; 
			}
			$memOs = $row['mem_Os'];
			if($memOs == 0){
				$memOs = "안드로이드";
			}else if($memOs == 1){
				$memOs = "아이폰";
			}else if($memOs == 2){
				$memOs = "기타(운영진)";
			}
    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_id" class="td_id_200"><?=$row['mem_Id']?></td>
        <td headers="mb_list_name" class="td_id_200"><?=$row['mem_NickNm']?></td>
        <td headers="mb_list_id"><?=$row['mem_Tel']?></td>
		<!--
        <td headers="mb_list_sns"><?=$row['mem_Secede']?></td>
        <td headers="mb_list_sns"><?=$row['mem_SecedeEtc']?></td>
		-->
        <td headers="mb_list_sns"><?=$row['mem_SnsChk']?></td>
        <td headers="mb_list_os" ><?=$memOs?></td>
        <td headers="mb_list_reg" class="td_date"><?=substr($row['reg_Date'],2,8)?></td>
        <td headers="mb_list_leave" class="td_date"><?=substr($row['leaved_Date'],2,8)?></td>
        <td headers="mb_list_auth" class="td_mbstat td_mng_s"><?=$b_Disply?></td>
		</td>
    </tr>
    <? 

		}
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;
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
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	});

</script>

</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>
