<?php
Session_start();
include "general_functions.php";
include "sidebar_selector.php";

$regErr = $nameErr = $cNameErr = $emailErr = "";
$name = $cName = $email = "";
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
if (isset($_SESSION["cNameErr"]))
{
    $nameErr = $_SESSION["cNameErr"];
    unset($_SESSION["cNameErr"]);
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
            $nameErr = "Input can be no more than 30 characters";
            $error = True;
        }
    }
    
    if (empty($_POST["cName"])) {
        $cNameErr = "Company name is required";
        $error = True;
    } else {
        $cName = test_input($_POST["cName"]); 
        if(strlen($cName) > 30) {
            $cNameErr = "Input can be no more than 30 characters";
            $error = True;
        }
    }
    
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $error = True;
    } else {
        $email = test_input($_POST["email"]); 
        if(strlen($email) > 50) {
            $emailErr = "Input can be no more than 50 characters";
            $error = True;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format"; // W3Schools
            $error = True;
        }
    }   

    if (checkDuplicates($email, &$emailErr))
        $error = True;
    
    if (!$error) {
        attemptRegister($name, $cName, $email, $password);
    }
}

function attemptRegister($name, $cName, $email, $password) {
    $class = "Internship Contact";
    $uname = $email;
    $hash = password_hash($password, PASSWORD_BCRYPT);
    insertIntoUsers($email, $class, $name, $hash);
    insertIntoIContact($email, $name, $cName);
    if (!isset($_SESSION["regErr"])) {
        $_SESSION["regErr"] = "Account created successfully!";
        if (!sendEmail($name, $email, $uname, $password)) {
            $_SESSION["regErr"] = "E-mail could not be delivered. Account not created.";
            deleteUser($email);
        }
    }
    header("Location: main_page.php");
    exit();
}

function insertIntoIContact($email, $name, $cName) {
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        if (mysqli_connect_errno()) {
            $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        else {
            $stmt1 = mysqli_prepare($con, "INSERT INTO Internship_Contact VALUES (?, ?, ?, ?, NULL)");
            mysqli_stmt_bind_param($stmt1,'ssss', $email, $cName, $name, $email);
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
        $stmt1 = mysqli_prepare($con, "DELETE FROM Internship_Contact WHERE Identifier = ?");
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
        <title>Create internship account - LIACS Student Project Manager</title>
    </head>
    <body>

        <div class="main">
            <h1>LIACS Student Project Manager</h1>
            <p>
            Fill in this form to create a new internship account.
            </p>
            <p><span class="error">* Required field.</span></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
				<table class="form">
					<tr>
						<td>Full name:</td>
						<td><input type="text" name="name" value="<?php  echo $name;?>">
						<span class="error">* <?php echo $nameErr;?></span></td>
					</tr>
					<tr>
						<td>Email address:</td>
						<td><input type="text" name="email" value="<?php echo $email;?>">
						<span class="error">* <?php echo $emailErr;?></span></td>
					</tr>
					<tr>
						<td>Company name:</td>
						<td><input type="text" name="cName" value="<?php  echo $cName;?>">
						<span class="error">* <?php echo $cNameErr;?></span></td>
					</tr>
				</table>
                <input type="hidden" name ="password" value="<?php echo $password;?>">
                <br>
                <span class="error"><?php echo $regErr;?></span>
                <br><br>
                <input type="submit" value="Create account">
            </form>
        </div>

    </body>
</html>
