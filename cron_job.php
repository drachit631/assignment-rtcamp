<?php
    require "config.php";
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
    }
    mysqli_stmt_close($statement);
    mysqli_close($conn);
//-----------------------------------Sending mail-----------------------------------------------------------------------
    
    function sendMail($recipient,$img,$title){
        $temp = array();
        array_push($temp,array('email' => $recipient));
        $email=json_encode($temp);
    //-----------------------------------Getting attachment id---------------------------------------------
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $_ENV['TRUSTIFI_URL'] . "/api/i/v1/attachment",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($img)),
        CURLOPT_HTTPHEADER => array(
            'x-trustifi-key:'.$_ENV['TRUSTIFI_KEY'].'',
            'x-trustifi-secret:'.$_ENV['TRUSTIFI_SECRET'].'',
        ),
        ));
        $attachmentID = curl_exec($curl);
        curl_close($curl);
    
    //-----------------------------------Sending mail of comic to all users-----------------------------------
        $curl = curl_init();
        $email_body="<center><b>".$title."</b><br/><img src='".$img."'/><br/><a href='https://assignment-rtcamp.herokuapp.com/unsubscribe.php?email=".$recipient."'>Unsuncsribe from mailing list</a></center>";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['TRUSTIFI_URL'] . "/api/i/v1/email",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "recipients": '.$email.',
            "lists": [],
            "contacts": [],
            "attachments": '.$attachmentID.',
            "title": "xkcd Random comic",
            "html": "'.$email_body.'",
            "methods": { 
            "postmark": false,
            "secureSend": false,
            "encryptContent": false,
            "secureReply": false 
            }
        }', 
            CURLOPT_HTTPHEADER => array(
                'x-trustifi-key:'.$_ENV['TRUSTIFI_KEY'].'',
                'x-trustifi-secret:'.$_ENV['TRUSTIFI_SECRET'].'',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;  
        
    }
//--------------------------------------------------------------------------------------------------------------------------------------------
?>