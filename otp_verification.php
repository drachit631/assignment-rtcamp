<?php
//-----------------------------------otp verification---------------------------------------------------------------        
	require "config.php";
	if(!empty($_POST['otp']) && !empty($_POST['email'])){
		$otp = trim($_POST['otp']);
		$email = trim($_POST['email']);
		if (!preg_match("/^[0-9]{6,6}$/",$otp)){
			http_response_code(403);
			die();
		}
		$res = pg_prepare($conn,"subscription",'select * FROM subscription where email=$1');
		$res = pg_execute($conn,"subscription", array($email));
		$row= pg_fetch_assoc($res);
		if($row['otp']==$otp){
			$res = pg_prepare($conn,"subscription_update",'UPDATE subscription SET verified= true WHERE email=$1');
			$res = pg_execute($conn,"subscription_update", array($email));
			if($res){
				pg_close($conn);
				http_response_code(200);
				exit;
			}else{
				http_response_code(404);
				die();
			}    
		}else{
			http_response_code(400);
		}
	}		
//------------------------------------------------------------------------------------------------------------------        
?>