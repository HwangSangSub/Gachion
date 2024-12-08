<?
/*======================================================================================================================

* 프로그램			: DB 내용 불러올 함수
* 페이지 설명		: DB 내용 불러올 함수

========================================================================================================================*/


/*회원 주 아이디 가져오기 */
function memSIdInfo($mem_Id) {
    
    $fDB_con = db1();
    
    $memTQuery = "SELECT mem_SId FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' LIMIT 1" ;
    $memTStmt = $fDB_con->prepare($memTQuery);
    $memTStmt->bindparam(":mem_Id",$mem_Id);
    $memTStmt->execute();
    $memTNum = $memTStmt->rowCount();
    
    if($memTNum < 1)  { //주 ID가 없을 경우 회원가입 시작
    } else {  //등록된 회원이 있을 경우
        while($memTRow = $memTStmt->fetch(PDO::FETCH_ASSOC)) {
            $mem_SId = $memTRow['mem_SId'];	       //체크 랜덤아이디
        }
        return $mem_SId;
    }
    
    dbClose($fDB_con);
    $memTStmt = null;
}



/*회원 디바이스 아이디 가져오기 */
function memDeviceIdInfo($mem_Id) {
    
    $fDB_con = db1();
    
    $memDeQuery = "SELECT mem_DeviceId FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' LIMIT 1" ;
    $memDeStmt = $fDB_con->prepare($memDeQuery);
    $memDeStmt->bindparam(":mem_Id",$mem_Id);
    $memDeStmt->execute();
    $memDeNum = $memDeStmt->rowCount();
    
    if($memDeNum < 1)  { //없을 경우
    } else {  //등록된 회원이 있을 경우
        while($memDeRow = $memDeStmt->fetch(PDO::FETCH_ASSOC)) {
            $memDeviceId = $memDeRow['mem_DeviceId'];	       //체크 랜덤아이디
        }
        return $memDeviceId;
    }
    
    dbClose($fDB_con);
    $memDeStmt = null;
}




/* 매칭 회원 토큰 값 가져오기 */
function memMatchTokenInfo($mem_SId) {
    
    $fDB_con = db1();
    
    $memTokQuery = "SELECT mem_Token FROM TB_MEMBERS WHERE mem_SId = :mem_SId AND b_Disply = 'N'" ;
    $memTokStmt = $fDB_con->prepare($memTokQuery);
    $memTokStmt->bindparam(":mem_SId",$mem_SId);
    $memTokStmt->execute();
    $memTokNum = $memTokStmt->rowCount();
    
    $tokens = array();
    if($memTokNum < 1)  { //주 ID가 없을 경우 회원가입 시작
    } else {  //등록된 회원이 있을 경우
        while($memTokRow = $memTokStmt->fetch(PDO::FETCH_ASSOC)) {
            $tokens[] = $memTokRow["mem_Token"];//토큰값
        }
        return $tokens;
    }
    
    
    dbClose($fDB_con);
    $memTokStmt = null;
}



//최종 아이폰 푸시
function send_IosPush($tokens, $data) {
    
    $url = "https://fcm.googleapis.com/fcm/send";
    $registrationIds = array($tokens);
    $serverKey = GOOGLE_API_KEY;
    
    $title = $data["title"];
    $msg = $data["msg"];
    $taxiState = $data["state"];
    
    if ($title == "" && $msg == "") { //제목, 메시지가 없을 때 없앰.
        $notification = array('content_available' => 'true', 'title' => '', 'body' => '', 'icon' => 'fcm_push_icon', 'sound' => '');
    } else {
        $notification = array('title' => $title , 'body' => $msg, 'icon' => 'fcm_push_icon', 'sound' => 'default');
    }
    
    $arrayToSend = array(
        'registration_ids' => $registrationIds,
        'notification'=> $notification,
        "data" => array(
            'chkState'  => $taxiState //상태 리턴
        ),
        'priority'=>'high'
    );
    
    //$json = json_encode($arrayToSend);
    $json =  json_encode($arrayToSend, JSON_UNESCAPED_UNICODE);
    
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    //Send the request
    $result = curl_exec($ch);
    if ($result === FALSE)
    {
        die('FCM Send Error: ' . curl_error($ch));
    }
    
    curl_close( $ch );
    
    sleep(1);
    
    return $result;
}









?>