<?php
//-----------------------------------Unsubscribe user------------------------------------------------------------------------------------------------
    require "config.php";
    if(!empty($_GET['email'])){
        $email = $_GET['email'];
        $statement = mysqli_prepare($conn, "DELETE FROM subscription WHERE email= ?");
		mysqli_stmt_bind_param($statement, "s", $email);
		$result = mysqli_stmt_execute($statement);
        if($result){
            echo "<center>";
            echo "<h2><b>Now you won't receive any random comics from XKCD</b></h2></br>";
            echo '<b><a href="https://assignment-rtcamp.herokuapp.com" id="index">Click here for getting Subscription</a></b>';
            echo "</center>";
        }else{
            echo "<center>";
            echo "<h2><b>You are not subscribed user</b></h2>";
            echo '<b><a href="https://assignment-rtcamp.herokuapp.com" id="index">Click here for getting Subscription</a></b>';
            echo "</center>";
        }
        mysqli_stmt_close($statement);
        mysqli_close($conn);
        die();
    }
//--------------------------------------------------------------------------------------------------------------------------------------------
?>