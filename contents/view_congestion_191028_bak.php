<?
/*
* 프로그램				: 핀 통계보기 (지도에 표현하기 위해 geojson 방식으로 처리)
* 페이지 설명			: 각 좌표의 총인구, 혼잡정도, 성별비율, 나이비율 조회
* 파일명					: view_congestion.php
* 관련DB					: TB_CONGESTION, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($con_Idx);						//콘텐츠고유번호 
$area_Code = trim($area_Code);				//지역코드

$DB_con = db1();
//총 인구수
	if($area_Code != ""){
		$chk_query = "
			SELECT *
			FROM TB_CONGESTION A
				INNER JOIN TB_PLACE B
					ON A.place_Idx = B.idx
			WHERE B.con_Idx = :con_Idx
				AND A.area_Code = :area_Code;
		";
		$chk_stmt = $DB_con->prepare($chk_query);
		$chk_stmt->bindParam(":con_Idx", $con_Idx);
		$chk_stmt->bindParam(":area_Code", $area_Code);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
		if($chk_num < 1){
			$result = array("result" => "error", "errorMsg" => "내역없음.");
			echo json_encode($result);
		}else{
			$query ="
				SELECT A.idx, A.lng, A.lat, A.cong_Rate, A.tot_Cnt, female_Cnt, female10, female20, female30 ,female40, female50, male_Cnt, male10, male20, male30 ,male40, male50
				FROM TB_CONGESTION A
					INNER JOIN TB_PLACE B
						ON A.place_Idx = B.idx
				WHERE B.con_Idx = :con_Idx
					AND A.area_Code = :area_Code;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->bindParam(":area_Code", $area_Code);
			$stmt->execute();
			$data = [];
			$geojson = array(
				"geometry" => array(
					"coordinates" => array(),
					"type" => "point"
				),
				"properties" => array()
			);
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$geojson = array(
					"geometry" => array(
						"coordinates" => array(),
						"type" => "point"
					),
					"properties" => array()
				);
				$idx = $row['idx'];													// 경도
				$lng = $row['lng'];												// 경도
				$lat = $row['lat'];													// 위도
				$cong_Rate = $row['cong_Rate'];								// 혼잡정도
				$tot_Cnt = $row['tot_Cnt'];										// 총인구수
				$fCnt = $row['female_Cnt'];										// 여자인구수
				$mCnt = $row['male_Cnt'];										// 남자인구수
				$female10 = $row['female10'];									// 10대 여자
				$female20 = $row['female20'];									// 20대 여자
				$female30 = $row['female30'];									// 30대 여자
				$female40 = $row['female40'];									// 40대 여자
				$female50 = $row['female50'];									// 50대 여자
				$male10 = $row['male10'];										// 10대 남자
				$male20 = $row['male20'];										// 10대 남자
				$male30 = $row['male30'];										// 10대 남자
				$male40 = $row['male40'];										// 10대 남자
				$male50 = $row['male50'];										// 10대 남자
				$fRate =  (($fCnt / $tot_Cnt) * 100);							// 여성비율
				$mRate = (($mCnt / $tot_Cnt) * 100);						// 남성비율
				$poiresult = [(double)$lng, (double)$lat];					//좌표를 숫자형으로
				$age10 = (double)$female10 + (double)$male10;		// 10대 비율
				$age20 = (double)$female20 + (double)$male20;		// 20대 비율
				$age30 = (double)$female30 + (double)$male30;		// 30대 비율
				$age40 = (double)$female40 + (double)$male40;		// 40대 비율
				$age50 = (double)$female50 + (double)$male50;		// 50대 비율
				$rate_10 = (($age10 / $tot_Cnt) * 100);						// 10대 비율
				$rate_20 = (($age20 / $tot_Cnt) * 100);						// 20대 비율
				$rate_30 = (($age30 / $tot_Cnt) * 100);						// 30대 비율
				$rate_40 = (($age40 / $tot_Cnt) * 100);						// 40대 비율
				$rate_50 = (($age50 / $tot_Cnt) * 100);						// 50대 비율
				$mresult = ["idx" => $idx, "cong_Rate" => (string)$cong_Rate, "tot_Cnt" => (string)$tot_Cnt, "fRate" => (string)(round($fRate)), "mRate" => (string)(round($mRate)), "age10" => (string)$age10, "age20" => (string)$age20, "age30" => (string)$age30, "age40" => (string)$age40, "age50" => (string)$age50, "rate_10" => (string)(round($rate_10)), "rate_20" => (string)(round($rate_20)), "rate_30" => (string)(round($rate_30)), "rate_40" => (string)(round($rate_40)), "rate_50" => (string)(round($rate_50))];
				array_push($geojson['geometry']['coordinates'], $poiresult);
				array_push($geojson['properties'], $mresult);
				array_push($data, $geojson);
			}
			$chkData = [];
			$chkData['features'] = $data;
			$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
			echo  urldecode($output);
		}
	}else{
		$chk_query = "
			SELECT *
			FROM TB_CONGESTION A
				INNER JOIN TB_PLACE B
					ON A.place_Idx = B.idx
			WHERE B.con_Idx = :con_Idx;
		";
		$chk_stmt = $DB_con->prepare($chk_query);
		$chk_stmt->bindParam(":con_Idx", $con_Idx);
		$chk_stmt->execute();
		$chk_num = $chk_stmt->rowCount();
		if($chk_num < 1){
			$result = array("result" => "error", "errorMsg" => "내역없음.");
			echo json_encode($result);
		}else{
			$geojson = array(
				"geometry" => array(
					"coordinates" => array(),
					"type" => "point"
				),
				"properties" => array()
			);
			$query ="
				SELECT A.idx, A.lng, A.lat, A.cong_Rate, A.tot_Cnt, female_Cnt, female10, female20, female30 ,female40, female50, male_Cnt, male10, male20, male30 ,male40, male50
				FROM TB_CONGESTION A
					INNER JOIN TB_PLACE B
						ON A.place_Idx = B.idx
				WHERE B.con_Idx = :con_Idx;
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->execute();
			$data = [];
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];													// 경도
				$lng = $row['lng'];												// 경도
				$lat = $row['lat'];													// 위도
				$cong_Rate = $row['cong_Rate'];								// 혼잡정도
				$tot_Cnt = $row['tot_Cnt'];										// 총인구수
				$fCnt = $row['female_Cnt'];										// 여자인구수
				$mCnt = $row['male_Cnt'];										// 남자인구수
				$female10 = $row['female10'];									// 10대 여자
				$female20 = $row['female20'];									// 20대 여자
				$female30 = $row['female30'];									// 30대 여자
				$female40 = $row['female40'];									// 40대 여자
				$female50 = $row['female50'];									// 50대 여자
				$male10 = $row['male10'];										// 10대 남자
				$male20 = $row['male20'];										// 10대 남자
				$male30 = $row['male30'];										// 10대 남자
				$male40 = $row['male40'];										// 10대 남자
				$male50 = $row['male50'];										// 10대 남자
				$fRate =  (($fCnt / $tot_Cnt) * 100);							// 여성비율
				$mRate = (($mCnt / $tot_Cnt) * 100);						// 남성비율
				$poiresult = [(double)$lng, (double)$lat];					//좌표를 숫자형으로
				$age10 = (double)$female10 + (double)$male10;		// 10대 비율
				$age20 = (double)$female20 + (double)$male20;		// 20대 비율
				$age30 = (double)$female30 + (double)$male30;		// 30대 비율
				$age40 = (double)$female40 + (double)$male40;		// 40대 비율
				$age50 = (double)$female50 + (double)$male50;		// 50대 비율
				$rate_10 = (($age10 / $tot_Cnt) * 100);						// 10대 비율
				$rate_20 = (($age20 / $tot_Cnt) * 100);						// 20대 비율
				$rate_30 = (($age30 / $tot_Cnt) * 100);						// 30대 비율
				$rate_40 = (($age40 / $tot_Cnt) * 100);						// 40대 비율
				$rate_50 = (($age50 / $tot_Cnt) * 100);						// 50대 비율
				$mresult = ["idx" => $idx, "cong_Rate" => (string)$cong_Rate, "tot_Cnt" => (string)$tot_Cnt, "fRate" => (string)(round($fRate)), "mRate" => (string)(round($mRate)), "age10" => (string)$age10, "age20" => (string)$age20, "age30" => (string)$age30, "age40" => (string)$age40, "age50" => (string)$age50, "rate_10" => (string)(round($rate_10)), "rate_20" => (string)(round($rate_20)), "rate_30" => (string)(round($rate_30)), "rate_40" => (string)(round($rate_40)), "rate_50" => (string)(round($rate_50))];
				array_push($geojson['geometry']['coordinates'], $poiresult);
				array_push($geojson['properties'], $mresult);
				array_push($data, $geojson);
			}
			$chkData = [];
			$chkData['features'] = $data;
			$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
			echo  urldecode($output);
		}
	}

    dbClose($DB_con);
    $stmt = null;
	$sum_stmt = null;
?>



