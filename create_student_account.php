<?php
include "general_functions.php";
Session_start();
$regErr = $nameErr = $unameErr = $emailErr = "";
$name = $uname = $email = "";
$password = random_str(8);
$error = False;
if (isset($_SESSION["regErr"]))
{
    $regErr = $_SESSION["regErr"];
    unset($_SESSION["regErr"]);
    $error = True;
}
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
        if(strlen($uname) > 30) {
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

    if (checkDuplicates($uname, &$unameErr))
        $error = True;
    
    if (!$error) {
        attemptRegister($name, $uname, $email, $password);
    }
}

function attemptRegister($name, $uname, $email, $password) {
    $class = "Student";
    insertIntoUsers($uname, $class, $name, $password);
    insertIntoStudent($email, $name, $uname);
    if (!isset($_SESSION["regErr"])) {
        $_SESSION["regErr"] = "Account created successfully!";
        if (!sendEmail($name, $email, $uname, $password)) {
            $_SESSION["regErr"] = "E-mail could not be delivered. Account not created.";
            deleteUser($uname);
        }
    }
    header("Location: main_page.php");
    exit();
}

function insertIntoStudent($email, $name, $uname) {
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        if (mysqli_connect_errno()) {
            $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        else {
            $stmt1 = mysqli_prepare($con, "INSERT INTO Student VALUES (?, ?, ?, NULL)");
            mysqli_stmt_bind_param($stmt1,'sss', $uname, $name, $email);
            $result1 = mysqli_execute($stmt1);
            mysqli_close($stmt1);
            if (!$result1) {
                $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
            }
            mysqli_close($con);
        }
}

function deleteUser($uname) {
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else {
        $stmt1 = mysqli_prepare($con, "DELETE FROM Student WHERE Identifier = ?");
        mysqli_stmt_bind_param($stmt1,'s', $uname);
        $result1 = mysqli_execute($stmt1);
        mysqli_close($stmt1);
	    if (!$result1) {
            $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
	    }
	    $stmt2 = mysqli_prepare($con, "DELETE FROM InternshipApp_Users WHERE Identifier = ?");
        mysqli_stmt_bind_param($stmt2,'s', $uname);
        $result2 = mysqli_execute($stmt2);
        mysqli_close($stmt2);
	    if (!$result2) {
            $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
	    }
	    mysqli_close($con);
	}
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
            Fill in the forms to create a new Admin account.
            </p>
            <p><span class="error">* required field.</span></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                Student's Full name: <input type="text" name="name" value="<?php  echo $name;?>">
                <span class="error">* <?php echo $nameErr;?></span>
                <br>
                Student's Student number: <input type="text" name="uname" value="<?php  echo $uname;?>">
                <span class="error">* <?php echo $unameErr;?></span>
                <br>
                Student's Email address: <input type="text" name="email" value="<?php echo $email;?>">
                <span class="error">* <?php echo $emailErr;?></span>
                <input type="hidden" name ="password" value="<?php echo $password;?>">
                <br>
                <span class="error"><?php echo $regErr;?></span>
                <br><br>
                <input type="submit" value="Create account">
            </form>
        </div>

    </body>
</html>
