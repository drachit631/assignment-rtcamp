<?php
require __DIR__.'/config.php';
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
$statement = mysqli_prepare($conn, 'SELECT email FROM subscription WHERE verified=true');
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
	if(!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
		$protocol = 'https';
	}else{
		$protocol = 'http';
	}
	$url = 'https://api.sendgrid.com/';
	$sendgrid_apikey = getenv("SENDGRID_KEY");
	$fileName = 'image.png';
	$filePath = $img;
	$email_body="<center><b>".$title."</b><br/><img src='".$img."'/><br/><a href='".$protocol."://".$_SERVER['HTTP_HOST']."/unsubscribe.php?email=".$recipient."'>Unsubscribe from mailing list</a></center>";
	$params = array(
		'to'        => $recipient,
		'toname'	=> 'Subscriber',
		'subject'   => 'XKCD Random Comic',
		'html'      => $email_body,
		'from'      => getenv("FROM"),
		'fromname'	=> 'XKCD',
		'files['.$fileName.']' => file_get_contents($filePath),
		'type'		=> 'image/png'
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
//--------------------------------------------------------------------------------------------------------------------------------------------
?>