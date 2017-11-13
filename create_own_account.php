<?php
Session_start();
$nameErr = $unameErr = $emailErr = $passErr = "";
$name = $uname = $email = $phonenum = $password = "";
$error = False;
if (isset($_SESSION["nameErr"]))
{
    $nameErr = $_SESSION["nameErr"];
    unset($_SESSION["nameErr"]);
    $error = True;
}
if (isset($_SESSION["unameErr"]))
{
    $unameErr = $_SESSION["unameErr"];
    unset($_SESSION["unameErr"]);
    $error = True;
}
if (isset($_SESSION["emailErr"]))
{
    $emailErr = $_SESSION["emailErr"];
    unset($_SESSION["emailErr"]);
    $error = True;
}
if (isset($_SESSION["passErr"]))
{
    $passErr = $_SESSION["passErr"];
    unset($_SESSION["passErr"]);
    $error = True;
}
if ($error) {
    session_unset();
    session_destroy();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        $error = True;
    } else {
        $name = test_input($_POST["name"]); 
        if(strlen($name) > 30) {
            $nameErr = "Input too big";
            $error = True;
        }
    }
    
    if (empty($_POST["uname"])) {
        $unameErr = "Student number is required";
        $error = True;
    } else {
        $uname = test_input($_POST["uname"]); 
        if(strlen($uname) > 50) {
            $unameErr = "Input too big";
            $error = True;
        }
    }
    
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $error = True;
    } else {
        $email = test_input($_POST["email"]); 
        if(strlen($email) > 50) {
            $emailErr = "Input too big";
            $error = True;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format"; // W3Schools
            $error = True;
        }
    }
    
    if (empty($_POST["password"])) {
        $passErr = "Password is required";
        $error = True;
    } else {
        $password = test_input($_POST["password"]);
        $passwordcheck = test_input($_POST["passwordcheck"]);  
        if(strlen($password) > 30) {
            $passErr = "Input too big";
            $error = True;
        }
        if($password != $passwordcheck) {
            $passErr = "Password doesn't match the one entered below.";
            $error = True;
        }
    }
    
    if (!$error) {
        attemptRegister($name, $uname, $email, $password, $passwordcheck);
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function attemptRegister($name, $uname, $email, $password, $passwordcheck) {
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // check connection
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else {
        $result = mysqli_query($con, "INSERT INTO InternshipApp_Users VALUES ('$uname','Student','$name','$password');");
        if (mysqli_error($con) != "")
            $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
        else {
            $result = mysqli_query($con, "INSERT INTO Student VALUES ('$uname','$name','$email','$phonenum');");
            if (mysqli_error($con) != "")
                $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
            mysqli_close($con);
        }
    }
    // redirect to login page
    header("Location: index.php");
    exit();
}
?>

<!doctype html>

<html>
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Page To Register Accounts" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Page To Register Accounts</title>
    </head>
    <body>

        <div class="sidepane">
            <a href="main_page.php">Overview</a>
            <a href="#">Projects</a>
            <a href="#">Contact</a>
            <a href="#">Help</a></a>
        </div>

        <div class="main">
            <p>
            Fill in the forms to register a new account.
            </p>
            <p><span class="error">* required field.</span></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                Full name: <input type="text" name="name" value="<?php  echo $name;?>">
                <span class="error">* <?php echo $nameErr;?></span>
                <br>
                Student number: <input type="text" name="uname" value="<?php  echo $uname;?>">
				<span class="error">* <?php echo $unameErr;?></span>
				<br>
                Email address: <input type="text" name="email" value="<?php echo $email;?>">
                <span class="error">* <?php echo $emailErr;?></span>
                <br>
                Phone number: <input type="text" name="phonenum" value="<?php echo $phonenum;?>">
                <br>
                Password: <input type="password" name="password" value="">
                <span class="error">* <?php echo $passErr;?></span>
                <br>
                Re-type password: <input type="password" name="passwordcheck" value="">
                <span class="error">*</span>
                <br><br>
                <input type="submit" value="Create account">
            </form>
        </div>

    </body>
</html>
