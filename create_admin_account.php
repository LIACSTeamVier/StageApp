<?php
Session_start();
require_once "random_compat-2.0.11/lib/random.php";
$regErr = $nameErr = $emailErr = "";
$name = $email = "";
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

    if (checkDuplicates($email, &$emailErr))
        $error = True;
    
    if (!$error) {
        attemptRegister($name, $email, $password);
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkDuplicates($email, &$emailErr) {
    $error = False;
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // check connection
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else {
        $stmt = mysqli_prepare($con, "SELECT * FROM InternshipApp_Users i WHERE i.Identifier=?");
        mysqli_stmt_bind_param($stmt,'s', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (!$result){
            echo "database error!";
            die ('Unable to run query:' . mysqli_error());
        }
        else
            $row = mysqli_fetch_row($result);
        if(!empty($row)) {
            $emailErr = "Email already taken";
            $error = True;
        }
    }
    mysqli_close($con);
    return $error;
}

function attemptRegister($name, $email, $password) {
    include("general_functions.php");
    $class = "Admin";
    insertIntoUsers($email, $class, $name, $password);
    if (!isset($_SESSION["regErr"])) {
        $_SESSION["regErr"] = "Account created successfully!";
        if (!sendEmail($name, $email, $password)) {
            $_SESSION["regErr"] = "Account created successfully. However e-mail could not be sent. Please inform the user manually. They will have to request a new password.";
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

function insertIntoUsers($uname, $class, $name, $password){
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	$stmt1 = mysqli_prepare($con, "INSERT INTO InternshipApp_Users VALUES (?, ?, ?, ?)");
	mysqli_stmt_bind_param($stmt1,'ssss', $uname, $class, $name, $password);
	$result1 = mysqli_execute($stmt1);
	mysqli_close($stmt1);
	if (!$result1){
        $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
	}
	
    mysqli_close($con);
}

/** From StackOverFlow https://stackoverflow.com/a/31107425 
 *  Under Creative Commons Licence Attribution-ShareAlike 3.0 
 * 
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
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
                Admin's Full name: <input type="text" name="name" value="<?php  echo $name;?>">
                <span class="error">* <?php echo $nameErr;?></span>
                <br>
                Admin's Email address: <input type="text" name="email" value="<?php echo $email;?>">
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
