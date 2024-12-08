#!/usr/bin/php -q
<?php
/*======================================================================================================================

* 프로그램			: SKT open API를 통한 유동인구 DATA
* 페이지 설명		: SKT open API를 통해 폴리곤 좌표를 보내면 좌표별로 유동인구 DATA를 보내준다.
* 파일명				: skt_foot_traffic.php

========================================================================================================================*/

// register_globals off 처리
@extract($_GET);
@extract($_POST);
@extract($_SERVER);
@extract($_ENV);
@extract($_SESSION);
@extract($_COOKIE);
@extract($_REQUEST);
@extract($_FILES);

ob_start();

header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

//구글 fcm키
define("GOOGLE_API_KEY", "AAAAMcrX0Us:APA91bGxkQmw8lrvxlB2w1sLp-zwr-LxiId1u1DSIRKMygN041qutEOxH2oMI5_q_QwGLXx4ve6r1fU47B59T9C-Fx6H438D9V-BW2RzT0WpLhtkiVXgsJsmf-x7j6DUfUB30Li-wcOs");
	include 'inc/dbcon.php';


	$DB_con = db1();

    $now_Time = date('Y-m-d');	 //등록일

	// kml_File 불러오기
	$Query = "";
	$Query .= "SELECT kml_File FROM TB_CONTENTS";
	$Query .= " WHERE kml_File <> '' AND open_Bit = '0' AND con_Lv = '0' AND delete_Bit = '0'";
	$Query .= " GROUP BY kml_File;";
	//echo $Query."<BR>";
	//exit;
	$Stmt = $DB_con->prepare($Query);
	//$Stmt->bindparam(":now_Time",$now_Time);
	$Stmt->execute();
	$row=$Stmt->fetch(PDO::FETCH_ASSOC);
	//$cnt =  $row['cnt'];						// 유효만료기간지난 쿠폰 조회
	$num = $Stmt->rowCount();
	//echo $num."<BR>";
	if($num == ''){$num = 0;}
	if($num < 1)  { //아닐경우
		$result = array("result" => "error", "Msg" => "kml파일을 가진 지도가 없습니다.");
	} else {
		while($Row=$Stmt->fetch(PDO::FETCH_ASSOC)){
			$kml_File = $Row['kml_File'];
			if($kml_File != ""){
				$xml = file_get_contents("../contents/kmlfile/".$kml_File);
				$result_xml = simplexml_load_string($xml);
				//echo $result_xml->Document->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates; // John
				$Placemark = $result_xml->Document->Placemark;
				$pm_cnt = count($Placemark);
				//echo $pm_cnt;
				$name = [];
				for($pm = 0; $pm < $pm_cnt; $pm++){
					$name_chk = $result_xml->Document->Placemark[$pm]->name;
					array_push($name, $name_chk);
				}
				//print_r($name);
				$name_cnt = count($name);
				//echo $name_cnt;
				//echo "<br>";
				$res_data = [];
				$like_Bit = [];
				for($nm = 0; $nm < $name_cnt; $nm++){
					$locat = $result_xml->Document->Placemark[$nm]->Polygon->outerBoundaryIs->LinearRing->coordinates; 
					print_r($locat);
					$polygon = "POLYGON((".print_r($locat)."))";
					echo $polygon;
					exit;
					function search_Addr($url, $param=array()){
						$url = $url.'?'.http_build_query($param, '', '&');
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
						//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
						$contents = curl_exec($ch); 
						$contents_json = json_decode($contents, true); // 결과값을 파싱
						curl_close($ch);
						return $contents_json;
					}
					$res = search_Addr('https://apis.openapi.sk.com/mbp/v1/poing/polygon/real/serviceTotal',array("WKT" => $polygon, "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
					print_r($res);
					if($res['status'] == "200"){
						echo "성공이다";
					}else{
						echo "실패다ㅠㅠ";
					}
					$areaName = $areacode[$nm];
					$locat_poi = explode( ',', $locat);
					$poi_cnt = count($locat_poi);
					$lat = [];  //위도
					$lng = []; //경도
					for($i = 0; $i < $poi_cnt; $i++){
						if($i % 2 != 0){
							//위도
							array_push($lat, (double)$locat_poi[$i]);
						}else{
							//경도
							array_push($lng, (double)str_replace(" ","",str_replace("0 ", "", $locat_poi[$i])));
						}
					}
					$lng_chk = array_pop($lng); 
					$lat_min = min($lat);
					$lat_max = max($lat);
					$lng_min = min($lng);
					$lng_max = max($lng);
					// 좋아요가 많은 장소
					$like_query = "
						SELECT idx, place_Name, place_Icon, lng, lat, category, like_Cnt, like_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit
						FROM TB_PLACE 
						WHERE (".$lng_min." < lng AND lng < ".$lng_max.")
							AND (".$lat_min." < lat AND lat < ".$lng_max.")
							AND like_Cnt > 9
						ORDER BY like_Cnt DESC, mod_Date DESC, reg_Date DESC;
						";
					$like_stmt = $DB_con->prepare($like_query);
					$like_stmt->execute();
					$like_num = $like_stmt->rowCount();
					$like_data[$nm] = [];
					if($like_num < 1){
						 array_push($like_Bit, "0");
					}else{
						 array_push($like_Bit, "1");
						while($like_row=$like_stmt->fetch(PDO::FETCH_ASSOC)) {
							$idx = $like_row['idx'];								// 지점고유번호
							$place_Name = $like_row['place_Name'];		// 지점명
							$place_Icon = $like_row['place_Icon'];			// 지점아이콘
							if($place_Icon == ""){
								$place_Icon = "0";
							}
							$color_query = "
								SELECT code_on_Img, code_Color
								FROM TB_CONFIG_CODE
								WHERE code_Div = 'placeicon'
									AND code = :code;
								";
							$color_stmt = $DB_con->prepare($color_query);
							$color_stmt->bindParam(":code", $place_Icon);
							$color_stmt->execute();
							$color_row=$color_stmt->fetch(PDO::FETCH_ASSOC);
							$code_Color = $color_row['code_Color'];
							$lng = $like_row['lng'];								// 경도
							$lat = $like_row['lat'];								// 위도
							$category = $like_row['category'];					// 카테고리
							$tLike_Idx = $like_row['tLike_Idx'];				// 통합좋아요그룹번호
							$total_query = "
								SELECT like_Idx
								FROM TB_TOTAL_LIKE
								WHERE place_Idx = :place_Idx;
							";
							$total_stmt = $DB_con->prepare($total_query);
							$total_stmt->bindParam(":place_Idx", $idx);
							$total_stmt->execute();
							$total_row=$total_stmt->fetch(PDO::FETCH_ASSOC);
							$like_Idx = $total_row['like_Idx'];
							if($like_Idx != ""){
								$total_S_query = "
									SELECT SUM(like_Cnt) as cnt
									FROM TB_TOTAL_LIKE
									WHERE like_Idx = :like_Idx;
								";
								$total_S_stmt = $DB_con->prepare($total_S_query);
								$total_S_stmt->bindParam(":like_Idx", $like_Idx);
								$total_S_stmt->execute();
								$like_S_row=$total_S_stmt->fetch(PDO::FETCH_ASSOC);
								$like_Cnt = $like_S_row['cnt'];					// 좋아요 수
							}else{
								$like_Cnt = $like_row['like_Cnt'];					// 좋아요 수
							}
							$coupon_Idx = $like_row['coupon_Idx'];			// 쿠폰보유시 쿠폰고유번호
							$coupon_Bit  = $like_row['coupon_Bit'];			// 쿠폰사용여부
							$reserv_Bit = $like_row['reserv_Bit'];				// 예약가능여부

							$lresult = ["place_Idx" => $idx, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "icon_Color" => $code_Color, "category" => $category, "lng" => $lng, "lat" => $lat, "like_Cnt" => (string)$like_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit];
							 array_push($like_data[$nm], $lresult);
						}
					}
				}
			}
		}
		$chkData = [];
		$chkData["result"] = "success";
		$chkData['lists'] = $lresult;
	}

	dbClose($DB_con);
	$Stmt = null;
	$upStmt1 = null;
	$upStmt2 = null;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);  
?>