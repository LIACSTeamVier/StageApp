<?php
Session_start();
include "general_functions.php";
include "sidebar_selector.php";

$regErr = $nameErr = $emailErr = $phoneErr = $roleErr = $backErr = $topicErr = "";
$name = $email = $phonenum = $role1 = $role2 = $back1 = $back2 = $background = $topics = "";
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
if (isset($_SESSION["roleErr"]))
{
    $roleErr = $_SESSION["roleErr"];
    unset($_SESSION["roleErr"]);
    $error = True;
}
if (isset($_SESSION["backErr"]))
{
    $backErr = $_SESSION["backErr"];
    unset($_SESSION["backErr"]);
    $error = True;
}
if (isset($_SESSION["topicErr"]))
{
    $topicErr = $_SESSION["topicErr"];
    unset($_SESSION["topicErr"]);
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
    
    if (empty($_POST["phonenum"])) {
        $phoneErr = "Phonenumber is required";
        $error = True;
    } else {
        $phonenum = test_input($_POST["phonenum"]); 
        if(strlen($phonenum) > 10) {
            $phoneErr = "No more than 10 digits";
            $error = True;
        }
    }
    
    if (!isset($_POST["role1"]) && !isset($_POST["role2"])) {
        $roleErr = "Check at least one box.";
        $error = True;
    } else {
        $role1 = "yes";
        $role2 = "yes";
        if (!isset($_POST["role1"]))
            $role1 = "no";
        else if (!isset($_POST["role2"]))
            $role2 = "no";
    }
    
    if (!isset($_POST["back1"]) && !isset($_POST["back2"])) {
        $backErr = "Check at least one box.";
        $error = True;
    } else {
        if (!isset($_POST["back1"]))
            $background = "CS";
        else if (!isset($_POST["back2"]))
            $background = "BUS";
        else
            $background = "BOTH";
    }
    
    $topics = test_input($_POST["topics"]); 
    if(strlen($topics) > 144) {
        $topicErr = "No more than 144 characters";
        $error = True;
    }

    if (checkDuplicates($email, &$emailErr, $email, &$emailErr))
        $error = True;
    
    if (!$error) {
        attemptRegister($name, $email, $password, $phonenum, $role1, $role2, $background, $topics);
    }
}

function attemptRegister($name, $email, $password, $phonenum, $role1, $role2, $background, $topics) {
    $class = "Supervisor";
    $hash = password_hash($password, PASSWORD_BCRYPT);
    insertIntoUsers($email, $class, $name, $hash);
    insertIntoSupervisor($email, $name, $phonenum, $role1, $role2, $background, $topics);
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

function insertIntoSupervisor($email, $name, $phonenum, $role1, $role2, $background, $topics) {
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        if (mysqli_connect_errno()) {
            $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        else {
            $stmt1 = mysqli_prepare($con, "INSERT INTO Supervisor VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt1,'ssssssss', $email, $name, $email, $phonenum, $role1, $role2, $background, $topics);
            $result1 = mysqli_execute($stmt1);
            mysqli_close($stmt1);
            if (!$result1) {
                $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
            }
            mysqli_close($con);
        }
}

function deleteUser($email) {
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else {
        $stmt1 = mysqli_prepare($con, "DELETE FROM Supervisor WHERE Identifier = ?");
        mysqli_stmt_bind_param($stmt1,'s', $email);
        $result1 = mysqli_execute($stmt1);
        mysqli_close($stmt1);
	    if (!$result1) {
            $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
	    }
	    $stmt2 = mysqli_prepare($con, "DELETE FROM InternshipApp_Users WHERE Identifier = ?");
        mysqli_stmt_bind_param($stmt2,'s', $email);
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
        <title>Create supervisor account - LIACS Student Project Manager</title>
    </head>
    <body>
        <div class="main">
            <?php
                if ($_SESSION["class"] != "Admin") {
                    header("Location: main_page.php");
                    exit;
                }
            ?>
            <h1>LIACS Student Project Manager</h1>
            <p>
            Fill in this form to create a new supervisor account.
            </p>
            <p><span class="error">* Required field.</span></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <table class="form">
					<tr>
						<td>Full name:</td>
						<td><input type="text" name="name" value="<?php  echo $name;?>"></td>
						<td><span class="error">* <?php echo $nameErr;?></span></td>
					</tr>
					<tr>
						<td>Email address:</td>
						<td><input type="text" name="email" value="<?php echo $email;?>"></td>
						<td><span class="error">* <?php echo $emailErr;?></span></td>
					</tr>
					<tr>
						<td>Telephone Number:</td>
						<td><input type="text" name="phonenum" value="<?php echo $phonenum;?>"></td>
						<td><span class="error">* <?php echo $phoneErr;?></span></td>
					</tr>
					<tr>
						<td>Role:</td>
						<td><input type="checkbox" name="role1" value="<?php echo $role1;?>">First Supervisor</td>
						<td><span class="error"> <?php echo $roleErr;?></span></td>
					</tr>
					<tr>
						<td></td><td><input type="checkbox" name="role2" value="<?php echo $role2;?>">Second Supervisor
					</tr>
					<tr>
						<td>Background:</td>
						<td><input type="checkbox" name="back1" value="<?php echo $back1;?>">Business</td>
						<td><span class="error"> <?php echo $backErr;?></span></td>
					</tr>
					<tr>
						<td></td><td><input type="checkbox" name="back2" value="<?php echo $back2;?>">Computer Science
					</tr>
				    <tr>
				        <td>Topics:</td>
			        </tr>
		        </table>
		        <textarea name="topics" rows="5" cols="40"><?php echo $topics;?></textarea>
                <span class="error"><?php echo $topicErr;?></span>
                <br><br>
                <input type="hidden" name ="password" value="<?php echo $password;?>">
                <br>
                <span class="error"><?php echo $regErr;?></span>
                <br><br>
                <input type="submit" value="Create account">
            </form>
        </div>

    </body>
</html>
