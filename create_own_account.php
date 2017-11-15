<?php
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
        if(strlen($email) > 30) {
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
        if(strlen($phonenum) != 10) {
            $phoneErr = "Needs to be 10 digits";
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
    

    if (checkDuplicates($uname, &$unameErr, $email, &$emailErr, $phonenum, &$phoneErr))
        $error = True;
    
    if (!$error) {
        attemptRegister($name, $uname, $email, $phonenum, $password, $passwordcheck);
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkDuplicates($uname, &$unameErr, $email, &$emailErr, $phonenum, &$phoneErr) {
    $error = False;
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // check connection
    if (mysqli_connect_errno()) {
        $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    else {
        $stmt = mysqli_prepare($con, "SELECT * FROM InternshipApp_Users i WHERE i.Identifier=?");
        mysqli_stmt_bind_param($stmt,'s', $uname);
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
            $unameErr = "Username already taken";
            $error = True;
        }
        
        $stmt = mysqli_prepare($con, "SELECT * FROM Student s WHERE s.StuEMAIL=?");
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
        
        $stmt = mysqli_prepare($con, "SELECT * FROM Supervisor s WHERE s.SupEMAIL=?");
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
        
        $stmt = mysqli_prepare($con, "SELECT * FROM Internship_Contact i WHERE i.IConEMAIL=?");
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
        
        if (!is_null($phonenum)) {
            $stmt = mysqli_prepare($con, "SELECT * FROM Student s WHERE s.StuTel=?");
            mysqli_stmt_bind_param($stmt,'s', $phonenum);
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
                $phoneErr = "Phonenumber already taken";
                $error = True;
            }
            
            $stmt = mysqli_prepare($con, "SELECT * FROM Supervisor s WHERE s.SupTel=?");
            mysqli_stmt_bind_param($stmt,'s', $phonenum);
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
                $phoneErr = "Phonenumber already taken";
                $error = True;
            }
            
            $stmt = mysqli_prepare($con, "SELECT * FROM Internship_Contact i WHERE i.IConTel=?");
            mysqli_stmt_bind_param($stmt,'s', $phonenum);
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
                $phoneErr = "Phonenumber already taken";
                $error = True;
            }
        }
    }
    mysqli_close($con);
    return $error;
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
