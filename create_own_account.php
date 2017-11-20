<?php
include "general_functions.php";
Session_start();
$nameErr = $unameErr = $emailErr = $phoneErr = $passErr = "";
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
if (isset($_SESSION["phoneErr"]))
{
    $phoneErr = $_SESSION["phoneErr"];
    unset($_SESSION["phoneErr"]);
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
        if(strlen($uname) > 10) {
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
    
    if (empty($_POST["phonenum"]))
        $phonenum = NULL;
    else {
        $phonenum = test_input($_POST["phonenum"]); 
        if(strlen($phonenum) > 10) {
            $phoneErr = "Needs to be 10 or fewer digits";
            $error = True;
        }
        if (!ctype_digit($_POST["phonenum"])) {
            $phoneErr = "Digits only";
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
    

    if (checkDuplicates($uname, &$unameErr))
        $error = True;
    
    if (!$error) {
        attemptRegister($name, $uname, $email, $phonenum, $password, $passwordcheck);
    }
}

function attemptRegister($name, $uname, $email, $phonenum, $password, $passwordcheck) {
    $class = "Student";
    insertIntoUsers($uname, $class, $name, $password);
    insertIntoStudent($uname, $name, $email, $phonenum);
    if (!isset($_SESSION["regErr"]))
        $_SESSION["regErr"] = "Account created successfully!";
    // redirect to login page
    header("Location: index.php");
    exit();
}

function insertIntoStudent($uname, $name, $email, $phonenum){
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	$stmt2 = mysqli_prepare($con, "INSERT INTO Student VALUES (?, ?, ?, ?)");
	mysqli_stmt_bind_param($stmt2,'ssss', $uname, $name, $email, $phonenum);
	$result2 = mysqli_execute($stmt2);
	mysqli_close($stmt2);
	if (!$result2){
        $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
	}
	
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Register Account" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Register Account</title>
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
                <span class="error"><?php echo $phoneErr;?></span>
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
