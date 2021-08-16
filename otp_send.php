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
    $statement = mysqli_prepare($conn, "SELECT email FROM subscription WHERE email= ?");
	mysqli_stmt_bind_param($statement, "s", $email);
	mysqli_stmt_execute($statement);
	mysqli_stmt_bind_result($statement, $result_email);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    echo "hello0";
    if (NULL !== $result_email) {
        http_response_code(400);
        die();
    }
    $otp=rand(100000,999999);
    echo "hello";
    $statement = mysqli_prepare($conn, "INSERT INTO subscription (email,otp,verified) VALUES ( ?, ?, false)");
	mysqli_stmt_bind_param($statement, "si", $email, $otp);
	mysqli_stmt_execute($statement);
    if (mysqli_stmt_affected_rows($statement) > 0 ) {
        mysqli_stmt_close($statement);
        echo "hello1";
        var_dump(sendMail($email,$otp));
        http_response_code(200);
        exit;
    }
}
mysqli_close($conn);
function sendMail($recipient,$otp){
    $temp = array();
    array_push($temp,array('email' => $recipient));
    $email=json_encode($temp);
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
    var_dump($response);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    }
}          
//------------------------------------------------------------------------------------------------------------------
?>
