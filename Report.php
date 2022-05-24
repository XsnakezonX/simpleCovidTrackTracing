<?php
session_start();

include("connect_db.php");
include("function.php");
$user_data = verify_login($conn);
//check if user is logged in or redirect to login page
$message = '';

if ($_SERVER['REQUEST_METHOD']=="POST") {
    //date time of positive
    $p_date = $_POST['date'];
    $p_time = $_POST['time'];
    $d = $p_date . ' ' . $p_time;

    $sql = "CREATE TABLE IF NOT EXISTS $db.infected (id BIGINT(20) NOT NULL AUTO_INCREMENT,x VARCHAR(20) NOT NULL,y VARCHAR(20) NOT NULL,date DATE NOT NULL,time TIME NOT NULL,day DATETIME NOT NULL,duration BIGINT(20) NOT NULL,PRIMARY KEY(id),INDEX(x),INDEX(y),INDEX(date),INDEX(time),INDEX(day),INDEX(duration))";
    mysqli_query($conn, $sql); //setup new database if not exist

    //update user's visited location as infected (to TRUE) within a datetime range:
    //the last and next 7 days
    $user = $_SESSION["user_id"];//current user's personal database for visited place.

    $sql_q = "update $db.$user set infected=true where day >= DATE('$d' - INTERVAL 7 day) and date <= DATE('$d' + INTERVAL 7 day)";
//    $sql_q = "update login_db.$user set infected=false where day between '$x' and '$y'";
    mysqli_query($conn, $sql_q);
    $result = mysqli_query($conn, $sql_q);

    //add them to a bigger data base. avoid adding repeated data
    //select all user's infected location
    $query = "select * from $db.$user where infected = true";
    $res = mysqli_query($conn, $query);
    //if record(s) is found
    if(mysqli_num_rows($res) > 0){

        while($row = $res->fetch_assoc()) {
            //data of each row
            $x = floatval($row["x"]);
            $y = floatval($row["y"]);
            $date = $row["date"];
            $time = $row["time"];
            $day = $row["day"];
            $duration = floatval($row["duration"]);
            //add each to db if no repeated
            $check_q = "select * from $db.infected where x='$x' and y='$y' and day='$day' and duration='$duration'";
            $check = mysqli_query($conn, $check_q);
//            echo mysqli_num_rows($check);
            if(mysqli_num_rows($check) == 0){
                $query2 = "insert into $db.infected (x,y,date,time,day,duration) values ('$x','$y','$date','$time','$day','$duration')";
                mysqli_query($conn, $query2); //add new report data
                //report to server
                if (!($handle = curl_init())===false){
                    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($handle, CURLOPT_FAILONERROR, true);
                }else{
                    echo "Curl-error:" . curl_error($handle);
                }

//                $url = "http://localhost/main/sub/report_mock.php";
                $url ="http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/ctracker/report.php";
                curl_setopt($handle, CURLOPT_URL, $url);
                curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
                $post = array("x"=>$x,"y"=>$y,"date"=>$date,"time"=>$time,"duration"=>$duration);
                curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($post));//encode array to json, embed json in post body



            }
            //next row
        }
        $message = "*Updated!";
    }else{
        $message = "*No visited matches!";}

    //report to server


}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
        <link rel="stylesheet" href="Report.css">
		<title>COVID-CT: Report</title>
	</head>
	<body>
        <div class="appheading">
            <p>COVID - 19 Contact Tracing</p>
        </div>

        <div class="all">
            <div class="sidemenu">
                <ul class="menu">
                    <li><a href="Home.php">Home</a></li>
                    <li><a href="Overview.php">Overview</a></li>
                    <li><a href="Add.php">Add Visit</a></li>
                    <li><a href="Report.php"class="selected">Report</a></li>
                    <li><a href="Setting.php">Settings</a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href="Logout.php">Logout</a></li>
                </ul>
            </div>
            
            <div class="content">
                <div class="subtitle"><p >Report an Infection</p></div>
                <hr>
                <div class="paragraph">
                    <p>Please report date and time when you were tested positive for COVID-19</p>
                </div>
                <div class="layout">
                    <div class="errormsg"><?php echo $message;?></div>
                    <form class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <!-- forced inputs only for data & time -->
                        <input type="date" id="date" placeholder="Date" name="date"><br>
                        <input type="time" id="time" placeholder="Time" name="time" step="1"><br>
                        <input type="submit" id="report" value="Report">
                        <input type="reset" id="cancel" value="Cancel"><br>
                    </form>

                </div>
                
            </div>
        </div>
        
	</body>
</html>
