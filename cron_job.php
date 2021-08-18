<?php
    require "config.php";
    require "vendor/autoload.php";
//-----------------------------------Getting random comic number----------------------------------------------------
    
    $host = "https://xkcd.com/info.0.json";
    $response = getheadernum($host);
    $random_comic = rand(1,$response['num']);
    function getheadernum($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        if($response === false){
            return false;
        }else{
            return json_decode($body, true);
        }
    }

//-----------------------------------Getting comic image and title---------------------------------------------------------------------
    
    $comic_url = "https://xkcd.com/".$random_comic."/info.0.json";
    $comic_url_response = get_header_from_url($comic_url);
    $comic_img = $comic_url_response['img'];
    $comic_title = $comic_url_response['title'];
    function get_header_from_url($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        if($response === false){
            return false;
        }else{
            return json_decode($body, true);
        }
    }

//------------------------------Getting email address of subscribed users---------------------------------------------------- 
    $statement = mysqli_prepare($conn, "SELECT email FROM subscription WHERE verified=true");
	mysqli_stmt_execute($statement);
	mysqli_stmt_bind_result($statement, $result_email);
    $result = mysqli_stmt_fetch($statement);
    while($result){
        sendMail($result_email,$comic_img,$comic_title);
        $result = mysqli_stmt_fetch($statement);
    }
    mysqli_stmt_close($statement);
    mysqli_close($conn);
//-----------------------------------Sending mail-----------------------------------------------------------------------
    
    function sendMail($recipient,$img,$title){
        $email_body="<center><b>".$title."</b><br/><img src='".$img."'/><br/><a href='https://assignment-rtcamp.herokuapp.com/unsubscribe.php?email=".$recipient."'>Unsubscribe from mailing list</a></center>";
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("hadeskerbecs455@gmail.com", "XKCD");
        $email->setSubject("XKCD Comic");
        $email->addTo($recipient, "User");
        $email->addContent(
            "text/html", $email_body
        );
        $file_encoded = base64_encode(file_get_contents($img));
        $email->addAttachment(
            $file_encoded,
            "image/png",
            "image.png",
            "attachment"
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
//--------------------------------------------------------------------------------------------------------------------------------------------
?>