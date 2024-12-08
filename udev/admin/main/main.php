<?
	//$menu = "2";
	//$smenu = "2";
	//메인화면으로 좌측메뉴 숨김처리

	include "../common/inc/inc_header.php";  //헤더 

	$DB_con = db1();
	$base_url = $PHP_SELF;

	// 통계값 기준
	$sql_search=" WHERE A.mem_Lv NOt IN ('0') AND A.b_Disply = 'N' ";

	// 통계 기준 - 현재날짜 기준 7일
/*
$fr_date = date("Y-m-d",strtotime("-1 weeks"));
$to_date = date("Y-m-d");
*/
$fr_date = '2019-02-15';
$to_date = '2019-02-22';

	$sql_search.=" AND (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	
	$view_data_date = $fr_date." ~ ".$to_date;
	

	// 회원가입수
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(mem_Id)  AS cntRow FROM TB_MEMBERS A {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
	$cntStmt->bindValue(":fr_date",$fr_date);
	$cntStmt->bindValue(":to_date",$to_date);
	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalMemCnt = $row['cntRow'];
/*	
	echo $cntQuery;
	exit;
*/
	//수익통계
	$sql_search_point =" where (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	
	$sumQuery = "";
	$sumQuery = "SELECT SUM(taxi_OrdSPoint) AS sumRow FROM TB_PROFIT_POINT {$sql_search_point}" ;
	$sumStmt = $DB_con->prepare($sumQuery);
	$sumStmt->bindparam(":fr_date",$fr_date);
	$sumStmt->bindparam(":to_date",$to_date);
	$sumStmt->execute();
	$srow = $sumStmt->fetch(PDO::FETCH_ASSOC);
	$totalPrice = $srow['sumRow'];
/*
	echo $sumQuery;
	exit;
*/
	// 주문합계 -  결제/양도완료된 주문 합계
	$sql_search_order = " where taxi_OrdState in ('1', '2') ";
	$sql_search_order .=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";

	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx) AS cntRow FROM TB_ORDER  {$sql_search_order} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
    $cntStmt->bindValue(":fr_date",$fr_date);
    $cntStmt->bindValue(":to_date",$to_date);
	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalOrderCnt = $row['cntRow'];


	//매칭합계
	$sql_search_taxi = " where (DATE_FORMAT(taxi_SDate,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(taxi_SDate,'%Y-%m-%d') <= :to_date)";
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(A.idx) AS cntRow FROM TB_STAXISHARING A LEFT OUTER JOIN TB_ORDER C ON A.idx = C.taxi_SIdx {$sql_search_taxi} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
	$cntStmt->bindValue(":fr_date",$fr_date);
	$cntStmt->bindValue(":to_date",$to_date);
	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalTaxiCnt = $row['cntRow'];


	//수익통계 - 그래프
	$sql_search_point =" where (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	$sql_group_point =" GROUP BY left(A.reg_Date,10)";
	$sql_order_point =" ORDER BY left(A.reg_Date,10)";

	// 양도캐시				SUM(CASE WHEN A.taxi_Sign = '1' THEN A.taxi_OrdPoint ELSE 0 END) AS subt_Money,
	$sumQuery = "";
	$sumQuery = "
			SELECT 
				left(A.reg_Date,10) as DATE,
				SUM(CASE WHEN A.taxi_Sign = '0' THEN A.taxi_OrdPoint ELSE 0 END) AS plus_Money,
				SUM(CASE WHEN A.taxi_Sign = '0' THEN B.taxi_OrdSPoint ELSE 0 END) AS profit_Money 
			FROM TB_POINT_HISTORY A 
				INNER JOIN TB_PROFIT_POINT B ON A.taxi_SIdx = B.taxi_SIdx {$sql_search_point} {$sql_group_point} {$sql_order_point}" ;
	$sumStmt = $DB_con->prepare($sumQuery);
	$sumStmt->bindparam(":fr_date",$fr_date);
	$sumStmt->bindparam(":to_date",$to_date);
	$sumStmt->execute();

	$numCnt = $sumStmt->rowCount();
	if($numCnt > 0)   {

		$sumStmt->setFetchMode(PDO::FETCH_ASSOC);
		$label = [];
		$data1 = [];
		$data2 = [];
		while($row = $sumStmt->fetch()) {

			$date = $row['DATE'];
			$plus_Money = $row['plus_Money'];
			$profit_Money = $row['profit_Money'];

			array_push($label, $date);
			array_push($data1, $plus_Money);
			array_push($data2, $profit_Money);
		}

	}
/*
	echo $sumQuery;
	exit;
*/

	//주문통계 - 그래프
	$sql_search_order =" where (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";

	$sumQuery = "";
	$sumQuery = "
			SELECT 
				SUM(CASE WHEN A.taxi_OrdState = '0' THEN 1 ELSE 0 END) AS '0_CNT'
				,SUM(CASE WHEN A.taxi_OrdState in ('1', '2') THEN 1 ELSE 0 END) AS '12_CNT'
				,SUM(CASE WHEN A.taxi_OrdState = '3' THEN 1 ELSE 0 END) AS '3_CNT'
			FROM TB_ORDER A	{$sql_search_order}" ;
	$sumStmt = $DB_con->prepare($sumQuery);
	$sumStmt->bindparam(":fr_date",$fr_date);
	$sumStmt->bindparam(":to_date",$to_date);
	$sumStmt->execute();

	$numCnt = $sumStmt->rowCount();
	if($numCnt > 0)   {

		$sumStmt->setFetchMode(PDO::FETCH_ASSOC);
		while($row = $sumStmt->fetch()) {

			if($row['0_CNT'] == 0){
				$CNT1 = 0;
			}else{
				$CNT1 = $row['0_CNT'];
			}
			if($row['12_CNT'] == 0){
				$CNT2 = 0;
			}else{
				$CNT2 = $row['12_CNT'];
			}
			if($row['12_CNT'] == 0){
				$CNT3 = 0;
			}else{			
				$CNT3 = $row['3_CNT'];
			}

			$tot_CNT = (int)$CNT1 + (int)$CNT2 + (int)$CNT3;

			$P_CNT1 = round((int)$CNT1 / (int)$tot_CNT * 100);
			$P_CNT2 = round((int)$CNT2 / (int)$tot_CNT * 100);
			$P_CNT3 = round((int)$CNT3 / (int)$tot_CNT * 100);

			$p_cnt = [$P_CNT1, $P_CNT2, $P_CNT3];
		}

	}

	//매칭통계 - 그래프
	$sql_search_sharing =" where (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";

	$sumQuery = "";
	$sumQuery = "
			SELECT 
				SUM(CASE WHEN A.taxi_State = '1' THEN 1 ELSE 0 END) AS 'CNT1'
				,SUM(CASE WHEN A.taxi_State = '2' THEN 1 ELSE 0 END) AS 'CNT2'
				,SUM(CASE WHEN A.taxi_State = '3' THEN 1 ELSE 0 END) AS 'CNT3'
				,SUM(CASE WHEN A.taxi_State = '5' THEN 1 ELSE 0 END) AS 'CNT4'
				,SUM(CASE WHEN A.taxi_State = '6' THEN 1 ELSE 0 END) AS 'CNT5'
				,SUM(CASE WHEN A.taxi_State = '7' THEN 1 ELSE 0 END) AS 'CNT6'
				,SUM(CASE WHEN A.taxi_State = '8' THEN 1 ELSE 0 END) AS 'CNT7'
			FROM TB_STAXISHARING A	{$sql_search_sharing}" ;
	$sumStmt = $DB_con->prepare($sumQuery);
	$sumStmt->bindparam(":fr_date",$fr_date);
	$sumStmt->bindparam(":to_date",$to_date);
	$sumStmt->execute();

	$numCnt = $sumStmt->rowCount();
	if($numCnt > 0)   {

		$sumStmt->setFetchMode(PDO::FETCH_ASSOC);
		while($row = $sumStmt->fetch()) {

			if($row['CNT1'] == 0){
				$CNT1 = 0;
			}else{
				$CNT1 = $row['CNT1'];
			}
			if($row['CNT2'] == 0){
				$CNT2 = 0;
			}else{
				$CNT2 = $row['CNT2'];
			}
			if($row['CNT3'] == 0){
				$CNT3 = 0;
			}else{			
				$CNT3 = $row['CNT3'];
			}
			if($row['CNT4'] == 0){
				$CNT4 = 0;
			}else{			
				$CNT4 = $row['CNT4'];
			}
			if($row['CNT5'] == 0){
				$CNT5 = 0;
			}else{			
				$CNT5 = $row['CNT5'];
			}
			if($row['CNT6'] == 0){
				$CNT6 = 0;
			}else{			
				$CNT6 = $row['CNT6'];
			}
			if($row['CNT7'] == 0){
				$CNT7 = 0;
			}else{			
				$CNT7 = $row['CNT7'];
			}

			$tot_CNT = (int)$CNT1 + (int)$CNT2 + (int)$CNT3 + (int)$CNT4 + (int)$CNT5 + (int)$CNT6 + (int)$CNT7;

			$P_CNT1 = round((int)$CNT1 / (int)$tot_CNT * 100);
			$P_CNT2 = round((int)$CNT2 / (int)$tot_CNT * 100);
			$P_CNT3 = round((int)$CNT3 / (int)$tot_CNT * 100);
			$P_CNT4 = round((int)$CNT4 / (int)$tot_CNT * 100);
			$P_CNT5 = round((int)$CNT5 / (int)$tot_CNT * 100);
			$P_CNT6 = round((int)$CNT6 / (int)$tot_CNT * 100);
			$P_CNT7 = round((int)$CNT7 / (int)$tot_CNT * 100);
		}

	}


	//문의리스트
	$sql_search_online =" where (DATE_FORMAT(A.reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(A.reg_Date,'%Y-%m-%d') <= :to_date)";
	$sql_order_online =" ORDER BY A.reg_Date DESC limit 5;";

	$sumQuery = "";
	$sumQuery = "
			SELECT 
				A.idx
				,A.b_Part
				,A.b_Name
				,A.b_MemId
				,A.b_Title
				,A.b_State
				,A.reg_Date
			FROM TB_ONLINE A {$sql_search_online} {$sql_order_online}" ;
	$sumStmt = $DB_con->prepare($sumQuery);
	$sumStmt->bindparam(":fr_date",$fr_date);
	$sumStmt->bindparam(":to_date",$to_date);
	$sumStmt->execute();

	$numCnt = $sumStmt->rowCount();
/*
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
	*/
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>



<!-- Custom fonts for this template-->
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

<!-- Custom styles for this template-->
<link href="css/sb-admin-2.min.css" rel="stylesheet">

<!-- Page level plugins -->
<script src="vendor/chart.js/Chart.min.js"></script>


<div id="wrapper">
    <div id="container" class="container-small">
        <div class="container_wr">
        <h1 id="container_title">Dashboard</h1>
		</div>

		<div>
		<!-- Main Content -->
		  <div id="content">

			
			<!-- Begin Page Content -->
			<div class="container-fluid">

			  <!-- Content Row -->
			  <div class="row">

				<!-- 회원가입수  -->
				<div class="col-xl-3 col-md-6 mb-4">
				  <div class="card border-left-primary shadow h-100 py-2">
					<div class="card-body">
					  <div class="row no-gutters align-items-center">
						<div class="col mr-2">
						  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">회원가입수 (<?php echo $view_data_date?>)</div>
						  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalMemCnt)?></div>
						</div>
						<div class="col-auto">
						  <i class="fas fa-calendar fa-2x text-gray-300"></i>
						</div>
					  </div>
					</div>
				  </div>
				</div>

				<!-- 수입합계  -->
				<div class="col-xl-3 col-md-6 mb-4">
				  <div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
					  <div class="row no-gutters align-items-center">
						<div class="col mr-2">
						  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">수입합계 (<?php echo $view_data_date?>)</div>
						  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalPrice) ?></div>
						</div>
						<div class="col-auto">
						  <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
						</div>
					  </div>
					</div>
				  </div>
				</div>

				<!-- 주문합계-결제/양도완료 -->
				<div class="col-xl-3 col-md-6 mb-4">
				  <div class="card border-left-info shadow h-100 py-2">
					<div class="card-body">
					  <div class="row no-gutters align-items-center">
						<div class="col mr-2">
						  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">주문합계-결제/양도완료 (<?php echo $view_data_date?>)</div>
						  <div class="row no-gutters align-items-center">
							<div class="col-auto">
							  <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo number_format($totalOrderCnt) ?></div>
							</div>
							<!--
							<div class="col">
							  <div class="progress progress-sm mr-2">
								<div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							  </div>
							</div>
							-->
						  </div>
						</div>
						<div class="col-auto">
						  <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
						</div>
					  </div>
					</div>
				  </div>
				</div>

				<!-- 매칭합계 -->
				<div class="col-xl-3 col-md-6 mb-4">
				  <div class="card border-left-warning shadow h-100 py-2">
					<div class="card-body">
					  <div class="row no-gutters align-items-center">
						<div class="col mr-2">
						  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">매칭합계 (<?php echo $view_data_date?>)</div>
						  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalTaxiCnt);?></div>
						</div>
						<div class="col-auto">
						  <i class="fas fa-comments fa-2x text-gray-300"></i>
						</div>
					  </div>
					</div>
				  </div>
				</div>
			  </div>



			  <!--   // -->
			  <div class="row">

				<!-- 수익통계 -->
				<div class="col-xl-8 col-lg-7">
				  <div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h6 class="m-0 font-weight-bold text-primary">수익통계</h6>					   
					</div>
					<!-- Card Body -->
					<div class="card-body">
					  <div class="chart-area" style="height:100%;">
						<script type="text/javascript" src="js/demo/chart-bar-stack-demo.js"></script>
							<canvas id="stack_bar"></canvas>
							<script>	
									var arrlabel = new Array("<?=implode("\",\"" , $label);?>");
									var arrdata1 = new Array("<?=implode("\",\"" , $data1);?>");
									var arrdata2 = new Array("<?=implode("\",\"" , $data2);?>");
									var label = '';
									var data1 = '';
									var data2 = '';

									for(var i=0;i<arrlabel.length;i++){
										if(i==0){
											label += arrlabel[i];
										}else{
											label += ','+arrlabel[i];
										}
									}
									var split_label = label.split(",");
									for(var i=0;i<arrdata1.length;i++){
										if(i==0){
											data1 += parseInt(arrdata1[i]);
										}else{
											data1 += ','+parseInt(arrdata1[i]);
										}
									}
									var split_data1 = data1.split(",");
									for(var i=0;i<arrdata2.length;i++){
										if(i==0){
											data2 += parseInt(arrdata2[i]);
										}else{
											data2 += ','+parseInt(arrdata2[i]);
										}
									}
									var split_data2 = data2.split(",");
									var barChartData = {
										labels: split_label,
										datasets: [{
											label: '적립캐시',
											backgroundColor: window.chartColors.red,
											data: split_data1
										/*}, {
											label: '양도캐시',
											backgroundColor: window.chartColors.blue,
											data: [
												randomScalingFactor(),
												randomScalingFactor(),
												randomScalingFactor(),
												randomScalingFactor(),
												randomScalingFactor(),
												randomScalingFactor(),
												randomScalingFactor()
											]*/
										}, {
											label: '총수익금',
											backgroundColor: window.chartColors.green,
											data: split_data2
										}]

									};
									window.onload = function() {
										var ctx = document.getElementById('stack_bar').getContext('2d');
										window.myBar = new Chart(ctx, {
											type: 'bar',
											data: barChartData,
											options: {
												title: {
													display: false,
													text: '적립캐시 + 총수익금 = 양도캐시'
												},
												tooltips: {
													mode: 'index',
													intersect: false
												},
												responsive: true,
												scales: {
													xAxes: [{
														stacked: true,
													}],
													yAxes: [{
														stacked: true
													}]
												}
											}
										});
									};

									document.getElementById('randomizeData').addEventListener('click', function() {
										barChartData.datasets.forEach(function(dataset) {
											dataset.data = dataset.data.map(function() {
												return randomScalingFactor();
											});
										});
										window.myBar.update();
									});
							</script>
					  </div>
					</div>
				  </div>
				</div>

				<!-- 주문통계 -->
				<div class="col-xl-4 col-lg-5">
				  <div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h6 class="m-0 font-weight-bold text-primary">주문통계</h6>
					</div>
					<!-- Card Body -->
					<div class="card-body">
					  <div class="chart-pie pt-4 pb-2">
						<canvas id="myPieChart"></canvas>
					  </div>
					  <div class="mt-4 text-center small">
						<span class="mr-2">
						  <i class="fas fa-circle text-primary"></i> 접수
						</span>
						<span class="mr-2">
						  <i class="fas fa-circle text-success"></i> 결제/양도완료
						</span>
						<span class="mr-2">
						  <i class="fas fa-circle text-info"></i> 취소
						</span>
					  </div>
					  <script>
							var arrp_data = new Array("<?=implode("\",\"" , $p_cnt);?>");
							var p_data = '';
							for(var i=0;i<arrp_data.length;i++){
								if(i==0){
									p_data += arrp_data[i];
								}else{
									p_data += ','+arrp_data[i];
								}
							}
							var split_p_data = p_data.split(",");
							// Set new default font family and font color to mimic Bootstrap's default styling
							Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
							Chart.defaults.global.defaultFontColor = '#858796';

							// Pie Chart Example
							var ctx = document.getElementById("myPieChart");
							var myPieChart = new Chart(ctx, {
							  type: 'doughnut',
							  data: {
								labels: ["접수", "결제/양도완료", "취소"],
								datasets: [{
								  data: split_p_data,
								  backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
								  hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
								  hoverBorderColor: "rgba(234, 236, 244, 1)",
								}],
							  },
							  options: {
								maintainAspectRatio: false,
								tooltips: {
								  backgroundColor: "rgb(255,255,255)",
								  bodyFontColor: "#858796",
								  borderColor: '#dddfeb',
								  borderWidth: 1,
								  xPadding: 15,
								  yPadding: 15,
								  displayColors: false,
								  caretPadding: 10,
								},
								legend: {
								  display: false
								},
								cutoutPercentage: 80,
							  },
							});
					  </script>
					</div>
				  </div>
				</div>
			  </div>

			  <!-- Content Row -->
			  <div class="row">

				<!--매칭통계 -->
				<div class="col-xl-6 col-lg-7">
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">매칭통계</h6>
					</div>
					<div class="card-body">
					  <h4 class="small font-weight-bold">매칭중(<?= $CNT1?> 건)  <span class="float-right"><?= $P_CNT1; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-danger" role="progressbar" style="width: <?= $P_CNT1; ?>%" aria-valuenow="<?= $P_CNT1; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">매칭요청(<?= $CNT2?> 건)  <span class="float-right"><?= $P_CNT2; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-warning" role="progressbar" style="width: <?= $P_CNT2; ?>%" aria-valuenow="<?= $P_CNT2; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">예약요청(<?= $CNT3?> 건)  <span class="float-right"><?= $P_CNT3; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar" role="progressbar" style="width: <?= $P_CNT3; ?>%" aria-valuenow="<?= $P_CNT3; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">만남중(<?= $CNT4?> 건)  <span class="float-right"><?= $P_CNT4; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-info" role="progressbar" style="width: <?= $P_CNT4; ?>%" aria-valuenow="<?= $P_CNT4; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">이동중(<?= $CNT5?> 건)  <span class="float-right"><?= $P_CNT5; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-success" role="progressbar" style="width: <?= $P_CNT5; ?>%" aria-valuenow="<?= $P_CNT5; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">완료(<?= $CNT6?> 건)  <span class="float-right"><?= $P_CNT6; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-danger" role="progressbar" style="width: <?= $P_CNT6; ?>%" aria-valuenow="<?= $P_CNT6; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">취소(<?= $CNT7?> 건) <span class="float-right"><?= $P_CNT7; ?>%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-warning" role="progressbar" style="width: <?= $P_CNT7; ?>%" aria-valuenow="<?= $P_CNT7; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					</div>
				  </div>
				</div>

				<!-- Pie Chart -->
				<div class="col-xl-6 col-lg-5">
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">Bar Chart</h6>
					</div>
					<div class="card-body">
					  <div class="chart-bar">
						<canvas id="myBarChart"></canvas>
					  </div>
					</div>
				  </div>
				</div>

			  </div>

			  
			  <!-- Content Row -->
			  <div class="row"  style="border:1px solid red">

			    <div class="col-xl-12 col-lg-5" style="width:100%;">
			  	  <!-- 문의리스트  -->
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">문의리스트</h6>
					</div>
					<div class="card-body">
					  <div class="table-responsive">
						<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
						  <thead>
							<tr>
							  <th>NO</th>
							  <th>문의분류</th>
							  <th>문의자ID</th>
							  <th>제목</th>
							  <th>문의일자</th>
							  <th>답변여부</th>
							</tr>
						  </thead>
							<?
								$num = 1;
								if($numCnt > 0)   {
							?>
									<tbody>
							<?

									$sumStmt->setFetchMode(PDO::FETCH_ASSOC);
									while($row = $sumStmt->fetch()) {
										$idx = $row['idx'];
										$part = $row['b_Part'];
										if($part == '1'){
											$part_name = '매칭생성';
										}else if($part == '2'){
											$part_name = '매칭신청';
										}else if($part == '3'){
											$part_name = '게시판';
										}
										$name = $row['b_Name'];
										$memid = $row['b_MemId'];
										$title = $row['b_Title'];
										$state = $row['b_State'];
										if($state == '0'){
											$state_name = '답변대기';
										}else{
											$state_name = '답변완료';
										}
										$regdate = $row['reg_Date'];
							?>
									<tr>
									  <td><?= $num; ?></td>
									  <td><?= $part_name; ?></td>
									  <td><?= $memid."<br>(".$name.")"; ?></td>
									  <td><?= $title; ?></td>
									  <td><?= $regdate; ?></td>
									  <td><?= $state_name; ?></td>
									</tr>
							<?
										$num++;
									}	
							?>
								  </tbody>
							<?
								}else{
							?>
								<tbody>
									<tr>
									  <td colspan='6'>내역이 없습니다.</td>
									</tr>				
								</tbody>
							<?
								}
							?>
						</table>
					  </div>
					</div>
				  </div>
				</div>

			  </div>


			  <!-- Content Row -->
			  <div class="row" style="border:1px solid red">

				<!-- Content Column -->
				<div class="col-lg-6 mb-4">

				  <!-- Project Card Example -->
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
					</div>
					<div class="card-body">
					  <h4 class="small font-weight-bold">Server Migration <span class="float-right">20%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">Sales Tracking <span class="float-right">40%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">Customer Database <span class="float-right">60%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">Payout Details <span class="float-right">80%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">Account Setup <span class="float-right">Complete!</span></h4>
					  <div class="progress">
						<div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					</div>
				  </div>

				  

				</div>

				<div class="col-lg-6 mb-4">

				  <!-- Illustrations -->
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">Illustrations</h6>
					</div>
					<div class="card-body">
					  <div class="text-center">
						<img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="img/undraw_posting_photo.svg" alt="">
					  </div>
					  <p>Add some quality, svg illustrations to your project courtesy of <a target="_blank" rel="nofollow" href="https://undraw.co/">unDraw</a>, a constantly updated collection of beautiful svg images that you can use completely free and without attribution!</p>
					  <a target="_blank" rel="nofollow" href="https://undraw.co/">Browse Illustrations on unDraw &rarr;</a>
					</div>
				  </div>

				 
				</div>
			  </div>

			</div>
			<!-- /.container-fluid -->

		  </div>
		</div>
	</div>
</div>





  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>


  <!-- Page level custom scripts -->
  <!--<script src="js/demo/chart-pie-demo.js"></script>-->
  <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-bar-demo.js"></script>