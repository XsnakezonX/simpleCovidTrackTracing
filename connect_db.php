<?php
// variables for local testing:
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$db = "login_db";
// variables for VM:
//$host_db = "localhost";
//$user_db = "example_user";
//$pass_db = "password";
//$db = "example_database";

$conn = mysqli_connect($host_db,$user_db,$pass_db,$db);
if(!$conn)
{
    die("unable to reach database!");
}

//verify_login(