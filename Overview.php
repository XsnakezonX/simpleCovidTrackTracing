<?php
session_start();

include("connect_db.php");
include("function.php");
$user_data = verify_login($conn);
//check if user is logged in or redirect to login page

?>
<!DOCTYPE html>
<html lang="en">
	<head>
        <script>
            function remove(t,v){
                var i = t.parentNode.parentNode.rowIndex;
                document.getElementById("b").deleteRow(i);//remove a <table> row by position

                var id = v;
                // alert(id); //return data id in database

                var xhttp = new XMLHttpRequest();
                xhttp.open("POST","db_function.php",true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                //request variable
                var param = "id="+id;//id in string
                // xhttp.onreadystatechange = function(){
                //     document.getElementById("test").innerHTML+=this.responseText+"<br>";
                // } //check server's response

                xhttp.send(param);//combine 2 strings e.g. id=3
                // alert(document.getElementById("cross").value); //return row position
            }

        </script>
		<meta charset="UTF-8">
        <link rel="stylesheet" href="Overview.css">
		<title>COVID-CT: Visits Overview</title>
	</head>
	<body>
        <div id="test"></div>
        <div class="appheading">
            <p>COVID - 19 Contact Tracing</p>
        </div>

        <div class="all">
            <div class="sidemenu">
                <ul class="menu">
                    <li><a href="Home.php">Home</a></li>
                    <li><a href="Overview.php" class="selected">Overview</a></li>
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
                <div class="tables">
                    <!-- test table -->
                    <table class="a">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>X</th>
                            <th>Y</th>
                        </tr>
                    </table>
                    <table class="b" id="b">
                        <?php
                        $user = $_SESSION["user_id"];//current user's personal database
                        $query = "SELECT * FROM $db.$user";
                        $result = mysqli_query($conn,$query);

                        while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                            echo "<tr><td>" . $row['date'] . "</td><td>" . $row['time'] . "</td><td>" . $row['duration'] . "</td><td>" . mb_substr($row['x'],0,7) . "</td><td>" . mb_substr($row['y'],0,7) . "</td><td><input type='image' class='button' src='cross.png' alt='cross' id='cross' name='cross' value='$row[0]' onclick='remove(this,value)'></td></tr>";//display content
                        }

                        mysqli_close($conn); //close out the database connection
                        ?>
<!--                        <tr id="1">-->
<!--                            <td>01/04/2021</td>-->
<!--                            <td>10:00</td>-->
<!--                            <td>60</td>-->
<!--                            <td>10</td>-->
<!--                            <td>10</td>-->
<!--                            <td>-->
<!--                                <form action="" method="">-->
<!--                                    <input type='image' class="button" src="cross.PNG" alt="cross">-->
<!--                                </form>-->
<!--                                <button class="cross"><img src="cross.PNG" alt="cross"></button>-->
<!--                                <input type='image' class="button" src="cross.PNG" alt="cross" onclick="add()">-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr id="2">-->
<!--                            <td>01/05/2021</td>-->
<!--                            <td>10:00</td>-->
<!--                            <td>60</td>-->
<!--                            <td>10</td>-->
<!--                            <td>10</td>-->
<!--                            <td>-->
<!--                                <input type='image' class="button" src="cross.PNG" alt="cross" onclick="remove(this)">-->
<!--                            </td>-->
<!--                        </tr>-->
                    </table>
                </div>
                
                
            </div>
        </div>

	</body>
</html>
