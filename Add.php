<?php
session_start();

include("connect_db.php");
include("function.php");
$user_data = verify_login($conn);
//check if user is logged in or redirect to login page
$message = '';
if($_SERVER['REQUEST_METHOD'] == "POST") {

    //form input
    $x = $_POST['x'];
    $y = $_POST['y'];
    $date = valid_input($_POST['date']);
    $time = valid_input($_POST['time']);
    $duration = valid_input($_POST['duration']);
    $day = $date . ' ' . $time;

    $user = $_SESSION["user_id"];//current user's personal database for visited place.

    if(!empty($date) && !empty($time) && !empty($duration) && !empty($x) && !empty($y)) {
        $query = "insert into $db.$user (x,y,date,time,day,duration,infected) values ('$x','$y','$date','$time','$day','$duration',false)";
        mysqli_query($conn, $query); //new user
        $message = "*Added successfully!";
    }else{
        $message = "*Please enter all entries correctly!!";
    }
}

function valid_input($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
        <link rel="stylesheet" href="Add.css">
		<title>COVID-CT: Add Visit</title>
        <script type='text/javascript'>

            function MapPos(event) {
                // coordinates on map
                var xImage = event.offsetX;
                var yImage = event.offsetY;
                DisplayPin(xImage,yImage)
                document.getElementById("x").value = xImage / 0.6;
                document.getElementById("y").value = yImage / 0.6;
                // alert(xImage);
                // alert(yImage);
            };

            function DisplayPin(x,y){
                //marker displays with adjusted map coordinate in relation to browser coordinates & image border
                var xAdj = -10 + 5;//-5
                var yAdj = -20 + 5;//-15
                var div = document.getElementById("exeter");
                var rect = div.getBoundingClientRect();// top-left--corner coordinates of map element
                xWeb = rect.left; //430.9666748046875
                yWeb = rect.top; //191.73333740234375
                // alert(xWeb);
                // alert(yWeb);
                var mark = document.getElementById('marker');
                mark.style.top =  y + yWeb + yAdj+ 'px';
                mark.style.left = x + xWeb + xAdj+'px';
                mark.style.display = "block";
                return;
            }

        </script>
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
                    <li><a href="Add.php" class="selected">Add Visit</a></li>
                    <li><a href="Report.php">Report</a></li>
                    <li><a href="Setting.php">Settings</a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href=""> </a></li>
                    <li><a href="Logout.php">Logout</a></li>
                </ul>
            </div>
            
            <div class="content">
                <div class="subtitle"><p >Add a new Visit</p></div>
                <hr>
                <div class="layout">
                    <div id="image" >
                        <img id="exeter" src="exeter.jpg" alt="Exeter Map" onclick='MapPos(event);'>
                    </div>
                    <div>
                        <img id="marker" src="marker_black.png" alt="marker">
                    </div>
                    <div style="font-size: 20px; font-family: 'Times New Roman';">
                        <p></p>
                    </div>
                    <div class="errormsg"><?php echo $message;?></div>
                    <form class="visit" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <input type="date" id="date" placeholder="Date" name="date"><br>
                        <input type="time" id="time" placeholder="Time" name="time" step="1"><br>
                        <input type="number" id="duration" placeholder="Duration" name="duration" min="1">
                        <input type="text" id="x" name="x" value="" style="display: none;">
                        <input type="text" id="y" name="y" value="" style="display: none;">
                        <br><br><br><br><br><br><br><br>
                        <input type="submit" id="add" value="Add"><br>
                        <input type="reset" id="cancel" value="Cancel"><br>
                    </form>
                    <!-- Add JavaScript map coord -->
                </div>
                
            </div>
        </div>
        
	</body>
</html>
