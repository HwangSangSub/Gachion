<?
	$menu = "1";
	$smenu = "2";

include "../common/inc/inc_header.php";  //헤더

$base_url = $PHP_SELF;

$sql_search = " WHERE A.taxi_MemId = '".$id."' ";

if ($od_status != "" ) {
    $sql_search .= " AND A.taxi_State = :taxi_State ";
}

if ($fr_date != "" || $to_date != "" ) {
    //$sql_search.=" AND (taxi_SDate between ':fr_date' AND ':to_date')";
    $sql_search .= " AND (DATE_FORMAT(taxi_SDate,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(taxi_SDate,'%Y-%m-%d') <= :to_date)";
}

if($findword != "")  {
    if ($findType == "taxi_Idx") {
       $sql_search .= " AND A.idx LIKE :findword "; 
    } else if ($findType == "taxi_OrdNo") {
        $sql_search .= " AND C.taxi_OrdNo LIKE :findword "; 
    } else if ($findType == "mem_Id") { 
        $sql_search .= " AND (A.taxi_MemId LIKE :findword OR A.taxi_SMemId LIKE :findword) "; 
    }
}

$DB_con = db1();

//전체 카운트
$cntQuery = "";
$cntQuery = "SELECT COUNT(A.idx) AS cntRow FROM TB_STAXISHARING A LEFT OUTER JOIN TB_ORDER C ON A.idx = C.taxi_SIdx {$sql_search} " ;
//echo $cntQuery."<BR>";
//exit;
$cntStmt = $DB_con->prepare($cntQuery);

if ($od_status != "" ) {
    $cntStmt->bindValue(":taxi_State",$od_status);
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
$findword = trim($findword);

$cntStmt->execute();

$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
$totalCnt = $row['cntRow'];

//echo $totalCnt."<BR>";
//$cntStmt = null;

$rows = 10;
$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sort1)	{
    $sort1  = "A.idx";
    $sort2 = "DESC";
}

$sql_order = "order by $sort1 $sort2";

// 매칭요청자 닉네임
$mnSql = "  , ( SELECT mem_NickNm FROM TB_MEMBERS WHERE TB_MEMBERS.mem_SId = A.taxi_SMemId AND TB_MEMBERS.b_Disply = 'N' limit 1 ) AS memNickNm  ";
$mnSql2 = "  , ( SELECT mem_NickNm FROM TB_MEMBERS WHERE TB_MEMBERS.mem_SId = A.taxi_SMemId AND TB_MEMBERS.b_Disply = 'Y' limit 1 ) AS memNickNm2  "; //탈퇴회원
$query = "";
$query = "SELECT A.idx, A.taxi_SMemId, A.taxi_MemId, A.taxi_Saddr, A.taxi_Eaddr, A.taxi_SDate, A.taxi_TPrice, A.taxi_Price, A.taxi_Per, A.taxi_State " ;
$query .= " , A.taxi_Os, B.taxi_Type, B.taxi_Mcnt, B.taxi_Distance, B.taxi_Route, B.taxi_Sex, B.taxi_Seat  {$mnSql} {$mnSql2} ";
$query .= " FROM TB_STAXISHARING A ";
$query .= " LEFT OUTER JOIN TB_STAXISHARING_INFO B ON A.idx = B.taxi_Idx ";
$query .= " LEFT OUTER JOIN TB_ORDER C ON A.idx = C.taxi_SIdx ";
$query .= " {$sql_search} {$sql_order} limit  {$from_record}, {$rows}";
//echo $query."<BR>";
//exit;
$stmt = $DB_con->prepare($query);

if ($od_status != "" ) {
    $stmt->bindValue(":taxi_State",$od_status);
}
/*
if ($taxiSMemId != "" ) {  //고유회원 아이디
    $stmt->bindValue(":taxi_SMemId",$taxiSMemId);
}
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
$findword = trim($findword);

$stmt->execute();
$numCnt = $stmt->rowCount();



// 페이지 타이틀	
$sql = "select mem_NickNm from TB_MEMBERS where mem_id='".$id."' ";
$sqltmt = $DB_con->prepare($sql);
$sqltmt->execute();
$sqlRow =$sqltmt->fetch();


$DB_con = null;

$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword)."&amp;od_status=".urlencode($od_status)."&amp;id=".urlencode($id);

include "../common/inc/inc_gnb.php";  //헤더
include "../common/inc/inc_menu.php";  //메뉴

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/memberManager.js"></script>

<script type="text/javascript">
	function chkDel(idx){
		var con_test = confirm("해당노선을 취소상태로 변경하시겠습니까?\n(결제완료인 경우 결제취소)");
		if(con_test == true){
			var allData = { "idx": idx};
			$.ajax({
			url:"/udev/taxiSharing/taxiSharingCancleProc.php",
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
		<h1 id="container_title"><?= $id?>(<?=$sqlRow['mem_NickNm']?>)&nbsp;회원상세보기 - 매칭관리</h1>
		

		<style>
		.ov_num{border-right:1px solid #fff;}
		.ov_txt a{color:#fff;}
		</style>
		<div class="local_ov01 local_ov">
			<span class="btn_ov01">
				<span class="ov_num"><a href="memberDetailView.php?id=<?=$id?>">기본정보</a> </span>
				<span class="ov_num"><a href="memberDetailView_point.php?id=<?=$id?>">캐시내역</a></span>
				<span class="ov_num"><a href="memberDetailView_order.php?id=<?=$id?>">주문내역</a></span>
				<span class="ov_txt"><a href="memberDetailView_taxiSharingList.php?id=<?=$id?>">매칭내역</a></span>
				<span class="ov_num"><a href="memberDetailView_coupon.php?id=<?=$id?>">쿠폰내역</a></span>
				<span class="ov_num"><a href="memberDetailView_inquiryList.php?id=<?=$id?>">문의리스트</a></span>
			</span>
		</div>


		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 건수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span></span>
		</div>

        <form class="local_sch03 local_sch"  autocomplete="off">
		<input type="hidden" name="id" id="id" value="<?=$id?>">
        <div>
            <strong>분류</strong>
        	<label for="findType" class="sound_only">검색대상</label>
        	<select name="findType" id="findType">
        		<option value="taxi_Idx" <?if($findType=="taxi_Idx"){?>selected<?}?>>노선번호</option>
        		<option value="taxi_OrdNo" <?if($findType=="taxi_OrdNo"){?>selected<?}?>>거래번호</option>
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
        	<span class="bg <? if ($od_status == "1") { ?>c01_on<? } ?>">
            <input type="radio" name="od_status" value="1" id="od_status_matchS" <?php echo get_checked($od_status, '1'); ?>>
            <label for="od_status_matchS">매칭중</label>
        	</span>
        	<span class="bg <? if ($od_status == "2") { ?>c02_on<? } ?>">
            <input type="radio" name="od_status" value="2" id="od_status_matchR" <?php echo get_checked($od_status, '2'); ?>>
            <label for="od_status_matchR">매칭요청</label>
        	</span>
        	<span class="bg <? if ($od_status == "3") { ?>c03_on<? } ?>">
            <input type="radio" name="od_status" value="3" id="od_status_meetS" <?php echo get_checked($od_status, '3'); ?>>
            <label for="od_status_meetS">예약요청</label>
        	</span>
        	<span class="bg <? if ($od_status == "4") { ?>c04_on<? } ?>">
            <input type="radio" name="od_status" value="4" id="od_status_meetC" <?php echo get_checked($od_status, '4'); ?>>
            <label for="od_status_meetC">예약요청완료</label>
        	</span>
        	<span class="bg <? if ($od_status == "5") { ?>c05_on<? } ?>">
            <input type="radio" name="od_status" value="5" id="od_status_meet" <?php echo get_checked($od_status, '5'); ?>>
            <label for="od_status_meet">만남중</label>
        	</span>
        	<span class="bg <? if ($od_status == "6") { ?>c06_on<? } ?>">
            <input type="radio" name="od_status" value="6" id="od_status_move" <?php echo get_checked($od_status, '6'); ?>>
            <label for="od_status_move">이동중</label>
        	</span>
        	<span class="bg <? if ($od_status == "7") { ?>c07_on<? } ?>">
            <input type="radio" name="od_status" value="7" id="od_status_complte" <?php echo get_checked($od_status, '7'); ?>>
            <label for="od_status_complte">완료</label>
        	</span>
        	<span class="bg <? if ($od_status == "8") { ?>c08_on<? } ?>">
            <input type="radio" name="od_status" value="8" id="od_status_cancel" <?php echo get_checked($od_status, '8'); ?>>
            <label for="od_status_cancel">취소</label>
        	</span>
        	<span class="bg <? if ($od_status == "9") { ?>c09_on<? } ?>">
            <input type="radio" name="od_status" value="9" id="od_status_cancel_Rcheck" <?php echo get_checked($od_status, '9'); ?>>
            <label for="od_status_cancel_Rcheck">취소사유확인</label>  
        	</span>
        	<span class="bg <? if ($od_status == "10") { ?>c10_on<? } ?>">  
            <input type="radio" name="od_status" value="10" id="od_status_orderC" <?php echo get_checked($od_status, '10'); ?>>
            <label for="od_status_orderC">거래완료확인</label>  
        	</span>  
        </div>
        
        <div class="sch_last">
            <strong>생성일자</strong>
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
<!--
<div class="local_desc01 local_desc">
    <p>
        노선취소기능은 현재 작업중입니다.<br>
    </p>
</div>
-->
        <nav class="pg_wrap">
        	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
        </nav>
        
        <form name="fmlist" id="fmlist"  method="post" autocomplete="off">
        <input type="hidden" name="id" id="id" value="<?=$id?>">
        <div class="tbl_head01 tbl_wrap">
            <table>
            <caption>쉐어링 매칭 목록</caption>
            <thead>
            <tr>
			<!--
                <th scope="col" id="mb_list_chk" >
                    <label for="chkall" class="sound_only">전체</label>
                    <input type="checkbox" name="chkall" class="chkc" id="chkAll">
                </th>
			-->
                <th scope="col" id="mb_list_idx">순번</th>
                <th scope="col" id="mb_list_sidx">매칭노선번호</th>
                <th scope="col" id="mb_list_id">매칭생성자</th>
                <th scope="col" id="mb_list_mailc">출발지/도착지</th>
                <th scope="col" id="mb_list_mailc">출발일/시간</th>
                <th scope="col" id="mb_list_mailc">경유여부</th>   
                <th scope="col" id="mb_list_mailc">OS</th>   
                <th scope="col" id="mb_list_mailc">요청자</th>  
                <th scope="col" id="mb_list_mailc">상태</th>
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
	        $from_record++;
	        $memNickNm1 = $row['memNickNm'];
	        $memNickNm2 = $row['memNickNm2'];
	        
	        if ($memNickNm1 != "" ) {
	            $memNickNm = $memNickNm1;
	        } else if ($memNickNm2 != "" ) {
	            $memNickNm = $memNickNm2;
	        } else {
	            $memNickNm = "비회원";
	        }
	        
	        if($row['taxi_Type'] == "0") {
	            $taxiType = "바로출발";
	        } else {
	            $taxiType = "예약출발";
	        }
	        
	        $taxiSDate =  $row['taxi_SDate'];
	        
	        $taxi_Sex = $row['taxi_Sex'] ;
	        
	        if($taxi_Sex == 0) {
	            $taxiSex = "남자";
	        } else if($taxi_Sex == 1) {
	            $taxiSex = "여자";
	        }
	        
	        $taxi_Seat = $row['taxi_Seat'] ;
	        if($taxi_Seat == 0) {
	            $taxiSeat = "앞좌석";
	        } else if($taxi_Seat == 1) {
	            $taxiSeat = "뒷좌석";
	        }
	        
	        $lineDistance = $row['taxi_Distance'];
	        if ($lineDistance <= "1000") {
	            $lineTDistance = $lineDistance."m";    // 미터
	        } else {
	            $taxiDistance = $lineDistance / 1000.0;
	            $lineTDistance = round($taxiDistance, 2)."km";    // 미터를 km로 변환
	        }
	        
	        
	        $taxi_Saddr = $row['taxi_Saddr'];
	        $taxi_Saddr = str_replace("null","",$taxi_Saddr);
	        
	        $taxi_Eaddr = $row['taxi_Eaddr'];
	        $taxi_Eaddr = str_replace("null","",$taxi_Eaddr);
	        
	        $taxi_Route = $row['taxi_Route'] ;
	        if($taxi_Route == 0) {
	            $taxiRoute = "경유가능";
	        } else if($taxi_Route == 1) {
	            $taxiRoute = "경유불가";
	        }
	        
	        $taxiOs = $row['taxi_Os'];
	        if($taxiOs == "") {
	            $taxiOsNm = "-";
	        } else if($taxiOs == 0) {
	            $taxiOsNm = "안드로이드";
	        } else if($taxiOs == 1) {
	            $taxiOsNm = "아이폰";
	        }
	        
	        
	        $taxi_State = $row['taxi_State'] ;
	        
	        if($taxi_State == 1) {
	            $taxiState = "매칭중";
	        } else if($taxi_State == 2) {
	            $taxiState = "매칭요청";
	        } else if($taxi_State == 3) {
	            $taxiState = "예약요청";
	        } else if($taxi_State == 4) {
	            $taxiState = "예약요청완료";
	        } else if($taxi_State == 5) {
	            $taxiState = "만남중";
	        } else if($taxi_State == 6) {
	            $taxiState = "이동중";
	        } else if($taxi_State == 7) {
	            $taxiState = "완료";
	        } else if($taxi_State == 8) {
	            $taxiState = "취소";
	        } else if($taxi_State == 9) {
	            $taxiState = "취소사유확인";
	        } else if($taxi_State == 10) {
	            $taxiState = "거래완료확인";
			}/* else if($taxi_State == 11) {
				$taxiState = "취소 요청 건 승인(본사)";
			} else if($taxi_State == 12) {
				$taxiState = "취소 요청 건 거절(본사)";
			} else if($taxi_State == 13) {
				$taxiState = "본사 확인 후 완료 처리(본사)";
			} else if($taxi_State == 14) {
				$taxiState = "본사 확인 후 취소 처리(본사)";
			}*/

    ?>


    <tr class="<?=$bg?>">
	<!--
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="mb_id[<?=$row['idx']?>]" id="mb_id_<?=$row['idx']?>" value="<?=$row['mem_Id'] ?>" >
            <? if($taxi_State != "7" && $taxi_State != "8" ) { ?>
              <input type="checkbox"  id="chk" class="chk" name="chk" value="<?=$row['idx']?>">
            <? } else { ?>
           	  -
            <? } ?>
        </td>
	-->
		<td headers="mb_list_id"><?=$from_record?></td>
        <td headers="mb_list_id"><a href="/udev/taxiSharing/taxiSharingReg.php?mode=mod&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" ><?=$row['idx']?></a></td>
        <td headers="mb_list_id"><a href="/udev/member/memberReg.php?mode=mod&id=<?=$row['taxi_MemId']?>"><?=$row['taxi_MemId']?> <br/> (<?=$memNickNm?>)</a></td>
        <td headers="mb_list_id">출발지: <?=$taxi_Saddr?> <br/> 도착지: <?=$taxi_Eaddr?></td>
        <td headers="mb_list_id"><?=$taxiType?> (<?=$taxiSDate?>)</td>
        <td headers="mb_list_id"><?=$taxiRoute?></td>
        <td headers="mb_list_id"><?=$taxiOsNm?></td>
        <td headers="mb_list_id"><a href="/udev/taxiSharing/taxiSharingSList.php?taxiSIdx=<?=$row['idx']?>" class="btn btn_05">요청자</a></td>
        <td headers="mb_list_id"><a href="#" class="btn btn_a<?=$taxi_State?>"><?=$taxiState?></a></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
		  <a href="/udev/taxiSharing/taxiSharingReg.php?mode=mod&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">상세</a>
		  
		  <? if($taxi_State != "7" && $taxi_State != "8" ) { ?>
<? if($_COOKIE['du_udev']['id'] != 'admin2'){?>
		  <a href="javascript:chkDel('<?=$row['idx']?>')" class="btn btn_02">취소</a>
<? } ?>
		  <? } ?>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="9" class="empty_table">자료가 없습니다.</td>
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


	/*
    var seconds = 5;
    var id = setInterval(function()
    {
       	location.reload();
    }, 1000*seconds);
	*/
	
</script>



</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>

