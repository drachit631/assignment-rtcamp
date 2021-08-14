<?php 
//-----------------------------------Send mail for email verification----------------------------------------------------
require "config.php";

if(!empty($_POST['email']))
{
    $email= trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        http_response_code(403);
        die();
    }
    $result = pg_prepare($conn,"subscription",'select * FROM subscription where email=$1;');
    $result = pg_execute($conn,"subscription", array($email));
    $row=pg_fetch_assoc($result);
    if($row){
        http_response_code(400);
        die();
    }
    $otp=rand(100000,999999);
    $assoc_array=[
        'email'=>$email,
        'otp'=>$otp,
        'verified'=> false
    ];
    $res = pg_insert($conn,'subscription',$assoc_array); 
    if($res){
        $temp = array();
        array_push($temp,array('email' => $email));
        sendMail(json_encode($temp),$otp);
        http_response_code(200);
        exit;
    }
}
pg_close($conn);
function sendMail($email,$otp){
    $curl = curl_init();
    $email_body="This is One Time Password for verifing your email address: <b>".$otp."</b>";
    curl_setopt_array($curl, array(
        CURLOPT_URL => $_ENV['TRUSTIFI_URL'] . "/api/i/v1/email",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>'{
            "recipients": '.$email.',
            "lists": [],
            "contacts": [],
            "attachments": [],
            "title": "Email verification",
            "html": "'.$email_body.'",
            "methods": { 
            "postmark": false,
            "secureSend": false,
            "encryptContent": false,
            "secureReply": false 
            }
        }',
        CURLOPT_HTTPHEADER => array(
            "x-trustifi-key: " . $_ENV['TRUSTIFI_KEY'],
            "x-trustifi-secret: " . $_ENV['TRUSTIFI_SECRET'],
            "content-type: application/json"
        )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    }
}          
//------------------------------------------------------------------------------------------------------------------
?>
