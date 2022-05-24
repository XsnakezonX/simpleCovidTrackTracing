<?php
session_start();

include("connect_db.php");
include("function.php");
$user_data = verify_login($conn);
//check if user is logged in or redirect to login page

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = $_POST['id'];
    $table = $user_data['user_id'];
//    echo $table;
    //remove a chosen row in the current user's data base
    if(!empty($id) && !empty($table)) {
        $query = "delete from $db.$table where id = $id";
//        echo $query;
        $sql = mysqli_query($conn, $query); //remove selected row of data
//        if($sql === TRUE){
//            echo "Record deleted successfully";
//        }else {
//            echo "Error deleting record: " . $conn->error;
//            die("text: " . $conn->connect_error);
//        }
    }
}
//echo "test";