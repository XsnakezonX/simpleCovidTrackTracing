<?php
session_start();
    $inputErr ="";

    include("connect_db.php");
    include("function.php");

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // use real_escape_string to remove unexpected characters
        $username = real_escape_string($_POST['username']);
        $password = real_escape_string($_POST['password']);

        if(!empty($username) && !empty($password)){
            //read from db
            $query = "select * from $db.user where username = '$username' limit 1";

//            $result = mysqli_query($conn, $query);

            //Use prepare statement to further prevent sql injection
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_bind_result($stmt, $result);
//            mysqli_stmt_bind_result($stmt, $tid, $tuser_id,$tname,$tsurname,$tusername,$tpassword,$tdate);
            mysqli_stmt_fetch($stmt);


            if($result){
                if($result && mysqli_num_rows($result) > 0){

                    $user_data = mysqli_fetch_assoc($result);

                    if(password_verify($password . $pepper, $user_data['password'])){

                        $_SESSION["user_id"] = $user_data['user_id'];

                        //cookie
                        $user_data = verify_login($conn);
                        $cookie_user_window = "{$user_data['id']}w";
                        $cookie_win_value = "1"; //default value for window 1 = one week
                        $cookie_user_distance = "{$user_data['id']}s";
                        $cookie_dis_value = "5"; //default value for distance
                        //detects if the cookies are set
                        if(!isset($_COOKIE[$cookie_user_window])) {
                            //if not, set up a new cookie
                            setcookie($cookie_user_window, $cookie_win_value, time() + (86400 * 30), "/");
                        }
                        if(!isset($_COOKIE[$cookie_user_distance])) {
                            //if not, set up a new cookie
                            setcookie($cookie_user_distance, $cookie_dis_value, time() + (86400 * 30), "/");
                        }
                        header("Location: index.php");
                        die;
                    }

                }
            }


            $inputErr =  "Invalid username or password!";
        }else{
            $inputErr = "Please fill in Username and Password!";
        }
    }

function real_escape_string($input) {
    //remove unexpected characters
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
        <link rel="stylesheet" href="Login.css">
		<title>COVID-CT: Login</title>
	</head>
	<body>
        <div class="appheading">
            <p>COVID - 19 Contact Tracing</p>
        </div>
        <div class="content">
<!--            action="/action_page.php"-->
            <form class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <br><br>
                <input type="text" id="username" placeholder="Username" name="username"><br>
                <input type="password" id="password" placeholder="Password" name="password"><br><br>
                <input type="submit" id="sub" value="Login">
                <input type="reset" id="cancel" value="Cancel"><br><br>

            </form>
            <form action="Register.php" class="form" method="GET">
                <div class="errormsg"><?php echo $inputErr;?></div>
                <input type="submit" id="register" value="Register">
            </form>
        </div>
        
	</body>
</html>
