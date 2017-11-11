<?php
Session_start();
$loginErr = "";
$uname = $password = "";
if (isset($_SESSION["loginErr"]))
{
    $loginErr = $_SESSION["loginErr"];
    unset($_SESSION["LoginErr"]);
    session_unset();
    session_destroy();
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
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
    if ($row[3] == $password) {
        // set session vars
        $_SESSION["username"] = "$row[2]";
        $_SESSION["class"] = "$row[1]";


	/*****TEMP*****/
	$_SESSION["ID"] = $uname;


        // redirect to main page
        header("Location: main_page.php");
    }
    else {
        $_SESSION["loginErr"] = "Invalid username and password combination";
        header("Location: Login.php");
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
    <title>InternshipApp login</title>
</head>
<body>

<div class="main_nopane">
    <h1>InternshipApp</h1>
    <h3>Login page</h3>
  
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="login">
        username: <input type="text" name="username"><br/>
        password: <input type="password" name="password"><br/>
        <span class="error"><?php echo $loginErr;?></span><br/>
        <input type="submit" value="Login">
    </form>
</div>
</body>
</html>

