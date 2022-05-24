<?php

session_start();

if (isset($_SESSION['user_id'])){
    unset($_SESSION['user_id']);
    session_destroy($_SESSION['user_id']);
}

header("Location: Login.php" );
die;