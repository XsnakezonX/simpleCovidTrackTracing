<?php
session_start();

    include("connect_db.php");
    include("function.php");
    $user_data = verify_login($conn);
    //check if user is logged in or redirect to login page

    $cookie_user_window = "{$user_data['id']}w";
    $cookie_win_value = "1"; //default value for window 1 = one week
    $cookie_user_distance = "{$user_data['id']}s";
    $cookie_dis_value = "5"; //default value for distance

    //detects if the cookies are set
    if(!isset($_COOKIE[$cookie_user_window])) {
        //if not, set up a new cookie
        setcookie($cookie_user_window, $cookie_win_value, time() + (86400 * 30), "/");
//        echo "Cookie named '" . $cookie_user_window . "' is not set!";
    }
//    else {
//        echo "Cookie '" . $cookie_user_window . "' is set!<br>";
//        echo "Cookie '" . $cookie_user_distance . "' is set!<br>";
//        echo "Value is: " . $_COOKIE[$cookie_user_window];
//    }
    if(!isset($_COOKIE[$cookie_user_distance])) {
        //if not, set up a new cookie
        setcookie($cookie_user_distance, $cookie_dis_value, time() + (86400 * 30), "/");
//        echo "Cookie named '" . $cookie_setting_user_distance . "' is not set!";
    }
//    else {
//        echo "Cookie '" . $cookie_user_distance . "' is set!<br>";
//        echo "Value is: " . $_COOKIE[$cookie_user_distance];
//    }

//display map


?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
        <link rel="stylesheet" href="Home.css">
		<title>COVID-CT: Home Page</title>
        <script type='text/javascript'>
            // function PlacePin(event) {
            //     console.log (event);
            //     var mark = document.getElementById('exeter');
            //     mark.style.top =  (event.clientY)+'px';
            //     mark.style.left = (event.clientX) +'px';
            //     alert(event.clientX);
            //     alert(event.clientY);
            // }

        </script>
	</head>
	<body>
        <div class="appheading">
            <p>COVID - 19 Contact Tracing</p>
        </div>
        <div class="all">
            <div class="sidemenu">
                <ul class="menu">
                    <li><a href="Home.php" class="selected">Home</a></li>
                    <li><a href="Overview.php">Overview</a></li>
                    <li><a href="Add.php">Add Visit</a></li>
                    <li><a href="Report.php">Report</a></li>
                    <li><a href="Setting.php">Settings</a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href="Logout.php">Logout</a></li>
                </ul>
            </div>
            
            <div class="content">
                <div class="status"><p >status</p></div>
                <hr>
                <div class="paragraphs">
                    <div id="map" >
                        <img id="exeter" src="exeter.jpg" alt="Exeter Map" onclick="DisplayPin()">
                    </div>
                    <div id="pins">
                        <?php
                        $message = "";
                        $window = intval($_COOKIE[$cookie_user_window]) * 7;//convert weeks to days
//                        $current_day = "2021-04-25 00:00:00"; //change to any date
                        $current_day = date("Y-m-d H:i:s"); //current datetime
                        $user = $_SESSION["user_id"];//current user's personal database
                        $query = "SELECT * FROM $db.$user where (day >= DATE('$current_day' - INTERVAL $window day) and date <= DATE('$current_day')) and (infected = true)";
                        $result = mysqli_query($conn,$query);

                        //display user's "infected" location, within window
                        while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results

                            $x = strval((floatval($row["x"])) * 0.6 + 430.9666748046875 - 5);
                            $y = strval((floatval($row["y"])) * 0.6 + 191.73333740234375 - 15);

                            //check distance
                            //within given window, display other infected location, from database and webservice, within distance
                            //from db
                            $c_x = floatval($row["x"]); //current row coordinates
                            $c_y = floatval($row["y"]);
                            $c_date = $row["date"];
                            $c_time = $row["time"];
                            $c_dur = $row["duration"];
                            $set_distance = floatval($_COOKIE[$cookie_user_distance]); //cookie distance
                            $query2 = "SELECT * FROM $db.infected where (day >= DATE('$current_day' - INTERVAL $window day) and date <= DATE('$current_day'))";
                            $result2 = mysqli_query($conn,$query2);
                            while($row2 = mysqli_fetch_array($result2)){   //Creates a loop to loop through results
                                $d_x = floatval($row2["x"]); //infected row coordinates
                                $d_y = floatval($row2["y"]);
                                $d_date = $row2["date"];
                                $d_time = $row2["time"];
                                $d_dur = $row2["duration"];
                                $reached = within_distance($c_x,$c_y,$d_x,$d_y,$set_distance); //check distance
                                $met = met($c_date,$c_time,$c_dur,$d_date,$d_time,$d_dur);//check if time overlapped in their duration

                                if($reached and $met){ //place a red pin if a matched record is found
                                    $x2 = strval((floatval($row2["x"])) * 0.6 + 430.9666748046875 - 5);
                                    $y2 = strval((floatval($row2["y"])) * 0.6 + 191.73333740234375 - 15);

                                    $style2 = 'width: 20px;height: 20px;position: absolute;top:' . "$y2" . 'px;left: ' . "$x2" . 'px;';
                                    echo "<img src='marker_red.png' alt='red_marker' style='$style2'>";//display content
                                }
                            }

                            //from api
                            if (!($handle = curl_init())===false){
                                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($handle, CURLOPT_FAILONERROR, true);
                            }else{
                                echo "Curl-error:" . curl_error($handle);
                            }
                            $infections = [];
                            $url ="http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/ctracker/infections.php?ts=5";
//                            $url = "http://localhost/main/sub/infections_mock.php?ts=5";
                            curl_setopt($handle, CURLOPT_URL, $url);
                            curl_setopt($handle,CURLOPT_HTTPGET, false);
                            curl_setopt($handle,CURLOPT_HEADER, false);
                            if(($output = curl_exec($handle))!==false){
                                $infections = json_decode($output,true);
                                foreach($infections as $in){
                                    //pull data from json
                                    $j_x = floatval($in["x"]); //infected row coordinates
                                    $j_y = floatval($in["y"]);
                                    $j_date = $in["date"];
                                    $j_time = $in["time"];
                                    $j_dur = $in["duration"];

                                    $reached = within_distance($c_x,$c_y,$j_x,$j_y,$set_distance); //check distance
                                    $met = met($c_date,$c_time,$c_dur,$j_date,$j_time,$j_dur);//check if time overlapped in their duration

                                    if($reached and $met){ //place a red pin if a matched record is found
                                        $x3 = strval((floatval($in["x"])) * 0.6 + 430.9666748046875 - 5);
                                        $y3 = strval((floatval($in["y"])) * 0.6 + 191.73333740234375 - 15);

                                        $style3 = 'width: 20px;height: 20px;position: absolute;top:' . "$y3" . 'px;left: ' . "$x3" . 'px;';
                                        echo "<img src='marker_red.png' alt='red_marker' style='$style3'>";//display content
                                    }
                                }
                            }else{
                                $message = "A Curl-error: " . curl_error($handle);
                            }

                            $style = 'width: 20px;height: 20px;position: absolute;top:' . "$y" . 'px;left: ' . "$x" . 'px;';
                            echo "<img src='marker_black.png' alt='marker' style='$style'>";//display content

                        }

                        mysqli_close($conn); //close out the database connection
                        ?>
                    </div>
                    <div>
                        <p>Hi <?php echo $user_data['name'];?>, you might have had a connection to an infected person at the location shown in red.</p>
                        <br><br><br><br><br><br><br><br>
                        <p>Click on the marker to see details about the infection.</p>
                        <p><?php echo $message;?></p>
                    </div>

                </div>
                
            </div>
        </div>
        
	</body>
</html>
