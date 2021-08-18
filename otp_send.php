<?php 
//-----------------------------------Send mail for email verification----------------------------------------------------
require "config.php";
require "vendor/autoload.php";
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
    if (NULL !== $result_email) {
        http_response_code(400);
        die();
    }
    $otp=rand(100000,999999);
    $statement = mysqli_prepare($conn, "INSERT INTO subscription (email,otp,verified) VALUES ( ?, ?, false)");
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
    $email_body="This is One Time Password for verifing your email address: <b>".$otp."</b>";
    $email = new \SendGrid\Mail\Mail(); 
    $email->setFrom("hadeskerbecs455@gmail.com", "XKCD");
    $email->setSubject("OTP for XKCD verification");
    $email->addTo($recipient, "User");
    $email->addContent(
        "text/html", $email_body
    );
    $sendgrid = new \SendGrid(getenv('SENDGRID_KEY'));
    try {
        $response = $sendgrid->send($email);
        print $response->statusCode() . "\n";
        print($response->headers());
        print $response->body() . "\n";
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}          
//------------------------------------------------------------------------------------------------------------------
?>
