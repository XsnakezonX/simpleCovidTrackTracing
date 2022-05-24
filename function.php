<?php
$pepper = "mixer"; //pepper for password security

function verify_login($conn){
    include("connect_db.php");
    if(isset($_SESSION["user_id"])){
        $id = $_SESSION["user_id"];
        $query = "select * from $db.user where user_id = $id limit 1";

        $result = mysqli_query($conn, $query);
        if($result && mysqli_num_rows($result) > 0){

            $user_data = mysqli_fetch_assoc($result);
            return $user_data;

        }
    }

    //else send user to login
    header("Location: Login.php");
    die;
}

function random_number($length){
    $num = "";
    if($length < 5){
        $length = 5;
    }

    $actual_len = rand(4, $length);
    for($i=0; $i < $actual_len; $i++){

        $num .= rand(0,9);

    }
    return $num;
}
// unique user id with different length

function within_distance($x1,$y1,$x2,$y2,$set_distance){
    $l = $x1 - $x2;
    $r = $y1 - $y2;
    $distance = sqrt(($l*$l)+($r*$r)); //euclidean distance between two points
    if($distance <= $set_distance){ //two location is within distance
        return true;
    }else {
        return false;}

}

function met($c_date,$c_time,$c_dur,$d_date,$d_time,$d_dur){
    $c_1 = strval($c_date . " " . $c_time);
    $c2 = date_create($c_1);
    date_add($c2,date_interval_create_from_date_string( "$c_dur" . " minutes"));
    $c_2 = strval(date_format($c2, 'Y-m-d H:i:s'));;
    $d_1 = strval($d_date . " " . $d_time);
    $d2 = date_create($d_1);
    date_add($d2,date_interval_create_from_date_string( "$d_dur" . " minutes"));
    $d_2 = strval(date_format($d2, 'Y-m-d H:i:s'));;

    $StartA = $c_1;
    $EndA = $c_2;
    $StartB = $d_1;
    $EndB = $d_2;
    //if user data time duration is overlapped with other's, return true
    return (($StartA <= $EndB) and ($EndA >= $StartB));
//    if(($StartA <= $EndB) and ($EndA >= $StartB)){
//        return true;
//    }else {
//        return false;
//    }
}

//test if two date time overlapped
//$c_date = '2021-04-13';
//$c_time = '00:10:00';
//$c_dur = '2';
//$d_date = '2021-04-13';
//$d_time = '00:05:00';
//$d_dur = '10';
//if (met($c_date,$c_time,$c_dur,$d_date,$d_time,$d_dur)){
//    echo "met";
//}else{
//    echo "not met";
//}
;

//Ajax



?>

