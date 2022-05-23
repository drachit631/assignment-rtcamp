<?php 
//-----------------------------------Send mail for email verification----------------------------------------------------
require __DIR__.'/config.php';
if(!empty($_POST['email']))
{
    $email= trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        http_response_code(403);
        die();
    }
    $statement = mysqli_prepare($conn, 'SELECT email FROM subscription WHERE email= ?');
	mysqli_stmt_bind_param($statement, "s", $email);
	mysqli_stmt_execute($statement);
	mysqli_stmt_bind_result($statement, $result_email);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    if (NULL !== $result_email) {
        http_response_code(400);
        die();
    }
    $otp=rand(100000,999999);
    $statement = mysqli_prepare($conn, 'INSERT INTO subscription (email,otp,verified) VALUES ( ?, ?, false)');
	mysqli_stmt_bind_param($statement, "si", $email, $otp);
	mysqli_stmt_execute($statement);
    if (mysqli_stmt_affected_rows($statement) > 0 ) {
        mysqli_stmt_close($statement);
        sendMail($email,$otp);
        http_response_code(200);
        exit;
    }
}
mysqli_close($conn);
function sendMail($recipient,$otp){
    $url = 'https://api.sendgrid.com/';
    $sendgrid_apikey = getenv("SENDGRID_KEY");
    $email_body="This is One Time Password for verifing your email address: <b>".$otp."</b>";
    $params = array(
        'to'        => $recipient,
        'toname'	=> 'User',
        'subject'   => 'OTP for XKCD verification',
        'html'      => $email_body,
        'from'      => getenv("FROM"),
        'fromname'	=> 'XKCD'
    );
    print_r($params);
    $request =  $url.'api/mail.send.json';

    $session = curl_init($request);
    curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $sendgrid_apikey));
    curl_setopt ($session, CURLOPT_POST, true);
    curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($session);
    curl_close($session);
    print_r($response);       
}          
//------------------------------------------------------------------------------------------------------------------
?>
