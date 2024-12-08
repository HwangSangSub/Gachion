<?
	//$menu = "2";
	//$smenu = "2";
	//메인화면으로 좌측메뉴 숨김처리

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE A.mem_Lv NOt IN ('0') AND A.b_Disply = 'N' ";

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

	// 회원등급명
	$memLvSql = "  , ( SELECT memLv_Name FROM TB_MEMBER_LEVEL C WHERE C.memLv = A.mem_Lv limit 1 ) AS memLvNm  ";

	//목록
	$query = "";
	$query = "SELECT A.idx, A.mem_SId, A.mem_Id, A.mem_NickNm, A.mem_Tel, A.reg_Date, A.mem_Os, A.b_Disply, A.mem_Code, B.mem_Point, B.mem_MatCnt, " ;
	$query .= " B.mem_Coupon, B.mem_ChNum, B.mem_McCnt, B.mem_Card";
	$query .= " {$memLvSql} FROM TB_MEMBERS A ";
	$query .= " LEFT OUTER JOIN TB_MEMBERS_ETC B ON B.mem_SId = A.mem_SId  {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ";
	//echo $query."<BR>";
	//exit;

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

	//회원등급
	$mquery = "";
	$mquery = "SELECT memLv, memLv_Name FROM TB_MEMBER_LEVEL WHERE 1 = 1 ORDER BY memLv ASC" ;
	$mstmt = $DB_con->prepare($mquery);
	$mstmt->execute();

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findword=".urlencode($findword);
	
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="container-small">
        <div class="container_wr">
        <h1 id="container_title">Dashboard</h1>
		</div>

		<div>
		내용 출력
		</div>
	</div>
</div>