<?php
//-----------------------------------Unsubscribe user------------------------------------------------------------------------------------------------
    require "config.php";
    if(!empty($_GET['email'])){
        $email = $_GET['email'];
        $result = pg_prepare($conn,"subscription",'delete from subscription where email=$1');
        $result = pg_execute($conn,"subscription", array($email));
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
        pg_close($conn);
        die();
    }
//--------------------------------------------------------------------------------------------------------------------------------------------
?>