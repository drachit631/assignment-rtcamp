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
		$statement = mysqli_prepare($conn, "SELECT otp FROM subscription WHERE email= ?");
		mysqli_stmt_bind_param($statement, "s", $email);
		mysqli_stmt_execute($statement);
		mysqli_stmt_bind_result($statement, $result_otp);
    	mysqli_stmt_fetch($statement);
    	mysqli_stmt_close($statement);
		if($result_otp == $otp){
			$statement = mysqli_prepare($conn, "UPDATE subscription SET verified= true WHERE email= ?");
			mysqli_stmt_bind_param($statement, "s", $email);
			mysqli_stmt_execute($statement);
			if(mysqli_stmt_affected_rows($statement) > 0 ){
				mysqli_stmt_close($statement);
				mysqli_close($conn);
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