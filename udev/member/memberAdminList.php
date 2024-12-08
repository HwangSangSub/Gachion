<?
	$menu = "1";
	$smenu = "6";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	
	$sql_search=" WHERE A.mem_Lv ='0' AND A.b_Disply = 'N' ";

	if ($fr_date != "" || $to_date != "" ) {
		//$sql_search.=" AND (reg_Date between ':fr_date' AND ':to_date')";
		$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	}

	if($mem_Lv != "")  {
		$sql_search .= " AND A.mem_Lv = :mem_Lv";
	}
	
	if($findOs != "")  {
	    $sql_search .= " AND A.mem_Os = :mem_Os";
	}

	if($findword != "")  {
	    if ($findType == "mem_NickNm") {
	        $sql_search .= " AND A.mem_NickNm LIKE :findword ";
	    } else if ($findType == "mem_Id") {
	        $sql_search .= " AND A.mem_Id LIKE :findword ";
	    }else if($findType == "mem_Tel"){
			$sql_search .= " AND A.mem_Tel LIKE :findword ";
		}
	}
	

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(mem_Id)  AS cntRow FROM TB_MEMBERS A {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
echo $cntQuery;
	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($mem_Lv != "")  {
	    $cntStmt->bindValue(":mem_Lv",$mem_Lv);
	}
	
	if($findOs != "")  {
	    $cntStmt->bindValue(":mem_Os",$findOs);
	}
	
	if($findword != "")  {
	    $cntStmt->bindValue(':findword','%'.trim($findword).'%');
	}

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];


	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "A.reg_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";

	//목록
	$query = "";
	$query = "SELECT A.idx, A.mem_Id, A.mem_NickNm, A.mem_Tel, A.reg_Date, A.mem_Os, A.b_Disply, A.mem_Code, A.mem_NPush ";
	$query .= "FROM TB_MEMBERS A {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ";
	//echo $query."<BR>";
	//exit;
echo $query;
	$stmt = $DB_con->prepare($query);

	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}

	if($mem_Lv != "")  {
	    $stmt->bindValue(":mem_Lv",$mem_Lv);
	}

	if($findOs != "")  {
	    $stmt->bindValue(":mem_Os",$findOs);
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

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

	/*
	// mem_NPush : 회원일 경우, 이벤트 공지 알림유무 ( 0 : ON, 1 : OFF )
	// mem_NPush : 관리자일 경우, 중요처리건 알림 ( 0 : ON, 1 : OFF )
	// (중요처리건 : 취소처리필요건, 완료확인필요건, 신규문의, 환전신청)
	*/

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">관리자 리스트</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">관리자 총회원수 </span><span class="ov_num"><?=number_format($totalCnt);?>명 </span>&nbsp;
			<!--<span class="btn_ov01"><span class="ov_txt">탈퇴  </span><span class="ov_num"><?=number_format($leave_count);?>명</span>-->
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">
		<input type="hidden" id="mem_Lv" name="mem_Lv" value="1">
		<div>
    		<strong>분류</strong>
    		<select name="findType" id="findType">
    			<option value="mem_NickNm" <?if($findType=="mem_NickNm"){?>selected<?}?>>닉네임</option>
    			<option value="mem_Id" <?if($findType=="mem_Id"){?>selected<?}?>>아이디</option>
    			<option value="mem_Tel" <?if($findType=="mem_Tel"){?>selected<?}?>>연락처</option>
    		</select>
    		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		</div>
		</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>회원관리 목록</caption>
    <thead>

    <tr>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll" onclick="check_all(this.form)">
        </th>
		<th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_id">아이디</th>
		<th scope="col" id="mb_list_id">닉네임</th>
		<th scope="col" id="mb_list_open">연락처</th>		
		<th scope="col" id="mb_list_card">알림유무</th>	
		<th scope="col" id="mb_list_mng"  class="last_cell">관리</th>
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
			$mem_NickNm = $row['mem_NickNm'];
			$memOs = $row['mem_Os'];
			if(isset($memOs)){
				if($memOs == 0){
					$memOs = "안드로이드";
				}else if($memOs == 1){
					$memOs = "아이폰";
				}else if($memOs == 2){
					$memOs = "기타(운영진)";
				}
			}else{
				$memOs = "-";
			}
			if($row['b_Disply'] == "N") { 
				$b_Disply = "정상"; 
			} else { 
				$b_Disply = "탈퇴"; 
			}
			if(isset($mem_Sex)) {
			    if($mem_Sex == 0) { 
					$mem_Sex = "남자"; 
			    } else if($mem_Sex == 1) { 
					$mem_Sex = "여자"; 
				}
			} else {
				$mem_Sex = "-"; 
			}
			if($login_Date != ""){
				$last_Login = substr($login_Date,2,8)."<br>(".substr($login_Date,11,5).")";
			}else{
				$last_Login = "-";
			}
			if(strtoupper($mem_SnsChk) == 'KAKAO') {
				$memSnsChk = '카카오톡';
			}else if(strtoupper($mem_SnsChk) == 'GOOGLE') {
				$memSnsChk = '구글';
			}else if(strtoupper($mem_SnsChk) == 'email') {
				$memSnsChk = '리얼플레이스';
			}else{
				$memSnsChk = '-';
			}
			if($row['mem_NPush'] == "1") $mem_NPush_chk = "";
			else $mem_NPush_chk ="checked";
    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="mb_id[<?=$row['idx']?>]" id="mb_id_<?=$row['idx']?>" value="<?=$memId?>" >
            <input type="checkbox" id="chk_<?=$row['idx']?>" class="chk" name="chk[]" value="<?=$row['idx']?>">
        </td>
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_id"><?=$memId ?></td>
		<td headers="mb_list_id"><?=$row['mem_NickNm']?></td>
		<td headers="mb_list_open" class="td_name td_mng_s"><?=$row['mem_Tel']?></td>
		<td headers="mb_list_open" class="td_name td_mng_s"><input type="checkbox" id="mem_push_chk" name="mem_push_chk" value="Y" <? echo $mem_NPush_chk?> onchange="javascript:mem_push_chk('<?=$row['idx']?>')"></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<a href="memberAdminReg.php?mode=mod&id=<?=$memId?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">상세</a>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="13" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">	
	<a href="#ALDel" id="bt_m_a_del" class="btn btn_02">선택삭제</a>
	<a href="memberAdminReg.php?mode=reg" id="bt_m_a_add" class="btn btn_01">관리자 추가</a>
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
	$mcntStmt2 = null;
	$mcntStmt3 = null;
	$mstmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
