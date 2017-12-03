<?php
Session_start();
require_once "sidebar_selector.php";
require_once "general_functions.php";

$loginErr = $regErr = "";
$uname = $password = "";
if (isset($_SESSION["loginErr"]))
{
    $loginErr = $_SESSION["loginErr"];
    unset($_SESSION["loginErr"]);
    session_unset();
    session_destroy();
}

if (isset($_SESSION["regErr"]))
{
    $regErr = $_SESSION["regErr"];
    unset($_SESSION["regErr"]);
    session_unset();
    session_destroy();
}

if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
    header("Location: main_page.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"]) || (empty($_POST["password"]))) {
        $loginErr = "Invalid username and password combination";
    } else {
        $uname = test_input($_POST["username"]);
        $password = test_input($_POST["password"]);
    }
    
    if ($loginErr == "") {
        attemptLogin($uname, $password);
    }
}

function attemptLogin($uname, $password) {
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $result = mysqli_query($con, "SELECT * FROM InternshipApp_Users g WHERE g.Identifier='$uname'") or die('Unable to run query:' . mysqli_error());
    $row = mysqli_fetch_row($result);
    mysqli_close($con);
    if (password_verify($password, $row[3])) {
    //if ($row[3] == $password) {
        // set session vars
        $_SESSION["username"] = "$row[2]";
        $_SESSION["class"] = "$row[1]";


	/*****TEMP*****/
	$_SESSION["ID"] = $uname;


        // redirect to main page
        header("Location: main_page.php");
        exit();
    }
    else {
        $_SESSION["loginErr"] = "Invalid username and password combination";
        header("Location: index.php");
        exit();
    }
    die();
}
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="utf-8" />
    <meta name="Description" content= "InternshipApp login" />
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Login - LIACS Student Project Manager</title>
</head>
<body>

<div class="main">
    <h1>LIACS Student Project Manager</h1>
    <h3>Login</h3>
  
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="login">
		<table class="form">	
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password"></td>
			</tr>
		</table>
        <span class="error"><?php echo $loginErr;?></span><br/>
        <span class="error"><?php echo $regErr;?></span><br/>
        <input type="submit" value="Login">
    </form>
    
    Forgotten password?
    <a href="reset_password.php">Reset password.</a></br>

</div>
</body>
</html>

