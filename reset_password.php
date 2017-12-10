<?php
Session_start();
require_once "general_functions.php";
require_once "sidebar_selector.php";

$emailErr = "";
$name = $uname = $email = $password = "";
$error = False;
if (isset($_SESSION["emailErr"]))
{
    $emailErr = $_SESSION["emailErr"];
    unset($_SESSION["emailErr"]);
    $error = True;
}
if ($error) {
    session_unset();
    session_destroy();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
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
    
    if (!$error) {
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        // check connection
        if (mysqli_connect_errno()) {
            $_SESSION["resetErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
            $error = True;
        }
        else {
            $stmt1 = mysqli_prepare($con, "SELECT * FROM Student s WHERE s.StuEMAIL=?");
            $stmt2 = mysqli_prepare($con, "SELECT * FROM Supervisor s WHERE s.SupEMAIL=?");
            $stmt3 = mysqli_prepare($con, "SELECT * FROM Internship_Contact i WHERE i.IConEMAIL=?");
            if (find_user($stmt1))
                password_reset();
            else if (find_user($stmt2))
                password_reset();
            else if (find_user($stmt3))
                password_reset();
            else {
                $_SESSION["resetErr"] = "E-mail not registered.";
                $error = True;
            }
            if (!$error && !forgot_password_email($name, $email, $uname, $password)) {
                $_SESSION["resetErr"] = "E-mail could not be delivered.";
                $error = True;
            }
            mysqli_close($con);
        }
    }
    
    if (!$error) {
        $_SESSION["regErr"] = "Password reset successfully!";
        header("Location: index.php");
    } 
}

function find_user($stmt) {
    mysqli_stmt_bind_param($stmt,'s', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (!$result){
        $_SESSION["resetErr"] = "Unable to run query:" . mysqli_error();
        $error = True;
        return False;
    }
    else {
        $row = mysqli_fetch_row($result);
        if(!empty($row)) {
            $uname = "$row[0]";
            $name = "$row[2]";
        }
        else
            return False;
    }
    return True;
}

function password_reset() {
    $password = random_str(8);
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = mysqli_prepare($con, "UPDATE InternshipApp_Users SET password=? WHERE Identifier=?");
    mysqli_stmt_bind_param($stmt,'ss', $hash, $uname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (!$result){
        $_SESSION["resetErr"] = "Unable to run query:" . mysqli_error();
        $error = True;
    }
}
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Reset Password" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Reset your Password - LIACS Student Project Manager</title>
    </head>
    <body>
        <div class="main">
            <?php
                if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
					header("Location: main_page.php");
					exit;
				}
            ?>
            <h1>LIACS Student Project Manager</h1>
            <p>
            Fill in your e-mail address, and a new password will be emailed to you.
            </p>
            <p><span class="error">* Required field.</span></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
				<table class="form">
					<tr>
						<td>Email address:</td>
						<td><input type="text" name="email" value="<?php echo $email;?>"></td>
						<td><span class="error">* <?php echo $emailErr;?></span></td>
					</tr>
				</table>
                <input type="submit" value="Reset password">
            </form>
        </div>

    </body>
</html>
