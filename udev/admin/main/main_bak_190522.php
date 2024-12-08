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

				<!-- Earnings (Monthly) Card Example -->
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

				<!-- Earnings (Monthly) Card Example -->
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

				<!-- Earnings (Monthly) Card Example -->
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

				<!-- Pending Requests Card Example -->
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

				<!-- Area Chart -->
				<div class="col-xl-8 col-lg-7">
				  <div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h6 class="m-0 font-weight-bold text-primary">수익통계</h6>					   
					</div>
					<!-- Card Body -->
					<div class="card-body">
					  <div class="chart-area">
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

				<!-- Pie Chart -->
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
			  <!-- //  -->

			  <!-- Content Row -->
			  <div class="row">

				<!-- Area Chart -->
				<div class="col-xl-8 col-lg-7">
				  <div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h6 class="m-0 font-weight-bold text-primary">수익통계</h6>
					  <!--
					  <div class="dropdown no-arrow">
						<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						  <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
						  <div class="dropdown-header">Dropdown Header:</div>
						  <a class="dropdown-item" href="#">Action</a>
						  <a class="dropdown-item" href="#">Another action</a>
						  <div class="dropdown-divider"></div>
						  <a class="dropdown-item" href="#">Something else here</a>
						</div>
					  </div>
					  -->					  
					</div>
					<!-- Card Body -->
					<div class="card-body">
					  <div class="chart-area">
						<canvas id="myAreaChart"></canvas>
					  </div>
					</div>
				  </div>
				</div>

				<!-- Pie Chart -->
				<div class="col-xl-4 col-lg-5">
				  <div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					  <h6 class="m-0 font-weight-bold text-primary">주문통계</h6>
					</div>
					<!-- Card Body -->
					<div class="card-body">
					  <div class="chart-pie pt-4 pb-2">
						<canvas id="myPieChart1"></canvas>
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
					</div>
				  </div>
				</div>
			  </div>


			  <!-- Content Row -->
			  <div class="row">

				<!-- Area Chart -->
				<div class="col-xl-6 col-lg-7">
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">매칭통계</h6>
					</div>
					<div class="card-body">
					  <h4 class="small font-weight-bold">매칭중 <span class="float-right">20%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">매칭요청 <span class="float-right">40%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">예약요청 <span class="float-right">60%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">만남중 <span class="float-right">80%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">이동중 <span class="float-right">Complete!</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">완료 <span class="float-right">20%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					  <h4 class="small font-weight-bold">취소 <span class="float-right">20%</span></h4>
					  <div class="progress mb-4">
						<div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
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
			  <div class="row">

			    <div class="col-xl-12 col-lg-5">
			  	  <!-- DataTales Example -->
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
						  <tbody>
							<tr>
							  <td>1</td>
							  <td>System </td>
							  <td>Edinburgh</td>
							  <td>SystemSystem61</td>
							  <td>2011/04/25</td>
							  <td>$320,800</td>
							</tr>
							<tr>
							  <td>2</td>
							  <td>Accountant</td>
							  <td>Tokyo</td>
							  <td>SystemSystem63</td>
							  <td>2011/07/25</td>
							  <td>$170,750</td>
							</tr>
							<tr>
							  <td>3</td>
							  <td>Junior Author</td>
							  <td>San Francisco</td>
							  <td>SystemSystem66</td>
							  <td>2009/01/12</td>
							  <td>$86,000</td>
							</tr>
							<tr>
							  <td>4</td>
							  <td>Senior Javascript </td>
							  <td>Edinburgh</td>
							  <td>2SystemSystem2</td>
							  <td>2012/03/29</td>
							  <td>$433,060</td>
							</tr>							
						  </tbody>
						</table>
					  </div>
					</div>
				  </div>
				</div>

			  </div>


			  <!-- Content Row -->
			  <div class="row">

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

				  <!-- Color System -->
				  <div class="row">
					<div class="col-lg-6 mb-4">
					  <div class="card bg-primary text-white shadow">
						<div class="card-body">
						  Primary
						  <div class="text-white-50 small">#4e73df</div>
						</div>
					  </div>
					</div>
					<div class="col-lg-6 mb-4">
					  <div class="card bg-success text-white shadow">
						<div class="card-body">
						  Success
						  <div class="text-white-50 small">#1cc88a</div>
						</div>
					  </div>
					</div>
					<div class="col-lg-6 mb-4">
					  <div class="card bg-info text-white shadow">
						<div class="card-body">
						  Info
						  <div class="text-white-50 small">#36b9cc</div>
						</div>
					  </div>
					</div>
					<div class="col-lg-6 mb-4">
					  <div class="card bg-warning text-white shadow">
						<div class="card-body">
						  Warning
						  <div class="text-white-50 small">#f6c23e</div>
						</div>
					  </div>
					</div>
					<div class="col-lg-6 mb-4">
					  <div class="card bg-danger text-white shadow">
						<div class="card-body">
						  Danger
						  <div class="text-white-50 small">#e74a3b</div>
						</div>
					  </div>
					</div>
					<div class="col-lg-6 mb-4">
					  <div class="card bg-secondary text-white shadow">
						<div class="card-body">
						  Secondary
						  <div class="text-white-50 small">#858796</div>
						</div>
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

				  <!-- Approach -->
				  <div class="card shadow mb-4">
					<div class="card-header py-3">
					  <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
					</div>
					<div class="card-body">
					  <p>SB Admin 2 makes extensive use of Bootstrap 4 utility classes in order to reduce CSS bloat and poor page performance. Custom CSS classes are used to create custom components and custom utility classes.</p>
					  <p class="mb-0">Before working with this theme, you should become familiar with the Bootstrap framework, especially the utility classes.</p>
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