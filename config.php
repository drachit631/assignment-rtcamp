<?php
$dbhost = getenv("DATABASE_HOSTNAME");
$dbuser = getenv("DATABASE_USERNAME");
$dbpass = getenv("DATABASE_PASSWORD");
$dbname = getenv("DATABASE_NAME");
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);         
?>