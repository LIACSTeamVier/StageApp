<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  
<div class="sidepane">
  <a href="#">Overview</a>
  <a href="#">Projects</a>
  <a href="#">Contact</a>
  <a href="#">Help</a></a>
</div>

<div class="main">
  <?php
    $identifierlength = 30; // length of the identifier according to table InternshipApp_Users
    if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
      header("Location: Login.php");
      exit;
      // TODO, send info of expired session to login page.
      // TODO, expire session after period of inactivity.
    }
    
    $class = $_SESSION["class"];
    if ($class != "Admin") {
      echo "Permission Denied<br/>";
      echo "<a href='../main_page.php'>Go back to main page</a>";
    }
    else {
        $_SESSION["creatingAccount"] = 0;
        // Creating the account
        $configs = include("config.php");
        $name = $_POST["name"];
        $email = $_POST["email"];
        $username = substr($_POST["email"], 0, $identifierlength); // FIXME maybe not use email as identifier
        $password = $_POST["password"];
        $class = $_POST["role"];
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        // Check connection
        if (mysqli_connect_errno())
            $_SESSION["accCreateErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        else {
            $result = mysqli_query($con, "INSERT INTO StageApp_Gebruikers VALUES ('$username','$class','$name','$password');");
        
            if (mysqli_error($con) != "")
                $_SESSION["accCreateErr"] = "Unable to run query:" . mysqli_error($con);
        }
        
        mysqli_close($con);
        
        // Send email
        if (!isset($_SESSION["accCreateErr"])) {
            
            $email_from = 'donotreply@yopmail.com'; //TODO replace with actual LIACS email
            $subject = "An account has been made for you on the LIACS InternshipApp";
            $boundary = uniqid('np');
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: $email_from \r\n";
            $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
            
            // MIME stuff
            $message = "This is a MIME encoded message.";
            $message .= "\r\n\r\n--" . $boundary . "\r\n";
            $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
            
            // Plain text body
            $message .= "Dear ".$name.",\nAn account has been made for you on the LIACS InternshipApp. Please follow the following link:\nhttp://csthesis.liacs.leidenuniv.nl/Login.php\nYour username and password are as follows:\nUsername: ".$username."\nPassword: ".$password."\nPlease do not reply to this e-mail.\n(notactually)LIACS"; // TODO replace with file
            $message .= "\r\n\r\n--" . $boundary . "\r\n";
            $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
            
            // HTML body
            $message .= "Dear <td>".$name."</td>,</br> An account has been made for you on the
        <a href='http://csthesis.liacs.leidenuniv.nl/Login.php'>LIACS InternshipApp</a>.</br> Your username and password are as follows:</br> Username: <td>".$username."</td></br> Password: <td>".$password."</td></br> Please do not reply to this e-mail.</br>(notactually)LIACS"; // TODO replace with file
            $message .= "\r\n\r\n--" . $boundary . "--";
            
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: $email_from \r\n";
            $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
            
            if (!(mail($email,$subject,$message,$headers)))
                $_SESSION["emailErr"] = 1;
        }
        header("Location: main_page.php");
    }
    
  ?>
</div>

</body>
</html> 
