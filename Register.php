<?php
session_start();

    $nameErr = $usernameErr = $passwordErr = $inputErr ="";

    include("connect_db.php");
    include("function.php");
    
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if(empty($_POST['name'])){
            $nameErr = "*Please enter a Name.";
        }else{
            $name = valid_input($_POST['name']); //process input
        }
        if(empty($username)){
            $usernameErr = "*Please enter a Username.";
        }else{
            $username = valid_input($username); //process input
        }
        if(empty($password)){
            $passwordErr = "*Please enter a Password.";
        }else{
            $password = valid_input($password); //process input
        }

        //check password requirement

        if (strlen($password)<8){
            $passwordErr = "*Password has to be at least 8 characters long.
                And only contain uppercase, lowercase, and numbers.";
        }
        elseif (!preg_match("/^[0-9a-zA-Z]*$/",$password)) {
            $passwordErr = "*Password has to be at least 8 characters long.
                And only contain uppercase, lowercase, and numbers.";
        }
        elseif(!empty($name) && !is_numeric($name) && !empty($username) && !empty($password)){
            //save into db
            $user_id = random_number(10);
            //encrypt password
            $options = ['cost'=> 10,]; //random salt
//            $pepper = "mixer"; //salt and pepper is then added, pepper is stored in an external file.
            $pwd = password_hash($password . $pepper, PASSWORD_BCRYPT, $options);
            $query = "insert into $db.user (user_id,name,surname,username,password) values ('$user_id','$name','$surname','$username','$pwd')";

            mysqli_query($conn, $query); //new user

            //create personal infected db

            $sql = "CREATE TABLE IF NOT EXISTS $db.$user_id (id BIGINT(20) NOT NULL AUTO_INCREMENT,x VARCHAR(20) NOT NULL,y VARCHAR(20) NOT NULL,date DATE NOT NULL,time TIME NOT NULL,day DATETIME,duration BIGINT(20) NOT NULL,infected BOOLEAN,PRIMARY KEY(id),INDEX(x),INDEX(y),INDEX(date),INDEX(time),INDEX(day),INDEX(duration))";
            mysqli_query($conn, $sql); //new user person db table

            //login
            $_SESSION["user_id"] = $user_id;

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

        }else{
            $inputErr = "*Please enter the correct data for each entry!";
        }


    }
function valid_input($input) {
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
        <link rel="stylesheet" href="Register.css">
		<title>COVID-CT: Registration</title>
	</head>
	<body>
        <div class="appheading">
            <p>COVID - 19 Contact Tracing</p>
        </div>
        <div class="content">
            <form class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <br><br>
                <div class="errormsg"><?php echo $nameErr;?></div>
                <input type="text" id="name" placeholder="Name" name="name"><br>
                <input type="text" id="surname" placeholder="Surname" name="surname"><br>
                <div class="errormsg"><?php echo $usernameErr;?></div>
                <input type="text" id="username" placeholder="Username" name="username"><br>
                <div class="errormsg"><?php echo $passwordErr;?></div>
                <input type="password" id="password" placeholder="Password" name="password"><br><br>
                <div class="errormsg"><?php echo $inputErr;?></div>
                <input type="submit" id="register" value="Register">
            </form>
        </div>
        
	</body>
</html>
