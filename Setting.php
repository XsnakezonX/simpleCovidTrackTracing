<?php
session_start();

    include("connect_db.php");
    include("function.php");
    $user_data = verify_login($conn);
    //check if user is logged in or redirect to login page

    //form respond
    $message = "";
    if($_SERVER['REQUEST_METHOD'] == "POST") {

        $window = $_POST['window'];
        $distance = $_POST['distance'];

        if ($distance >= 0 && $distance <= 500) {
            $cookie_user_window = "{$user_data['id']}w";
            $cookie_win_value = $window; //new value for window
            $cookie_user_distance = "{$user_data['id']}s";
            $cookie_dis_value = $distance; //new value for distance
            setcookie($cookie_user_window, $cookie_win_value, time() + (86400 * 30), "/"); //update window
            setcookie($cookie_user_distance, $cookie_dis_value, time() + (86400 * 30), "/"); //update distance
            $message = "*Update successful!";

        }else{
            $message = "*Please enter a distance between integer 0-500.";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
        <link rel="stylesheet" href="Setting.css">
		<title>COVID-CT: Settings</title>
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
                    <li><a href="Report.php">Report</a></li>
                    <li><a href="Setting.php"class="selected">Settings</a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href="Logout.php">Logout</a></li>
                </ul>
            </div>
            
            <div class="content">
                <div class="subtitle"><p >Alert Settings</p></div>
                <hr>
                <div class="paragraph">
                    <p>Herer you may change the alert distance and the time span for which the contact tracing will be performed.</p>
                    <?php echo $message;?>
                </div>
                <div class="layout">
                    <form class="form"  method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <label for="window">window</label>
                        <select id="window" name="window">
                            <option value="1">one week</option>
                            <option value="2">two week</option>
                            <option value="3">three week</option>
                            <option value="4">four week</option>
                        </select>
                        <br>
                        <label for="window">distance</label>
                        <input type="number" id="distance" name="distance" pattern="[0-9]"><br>
                        <input type="submit" id="report" value="Report">
                        <input type="reset" id="cancel" value="Cancel"><br>
                    </form>
                    <!-- Add cookie -->
                </div>
                
            </div>
        </div>
        
	</body>
</html>