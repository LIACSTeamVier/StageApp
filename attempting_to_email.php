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
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "9sdu8kG09u", "s1551396");
        // Check connection
        if (mysqli_connect_errno())
            $_SESSION["accCreateErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        else {
            $result = mysqli_query($con, "INSERT INTO StageApp_Gebruikers VALUES ('$email','Internship Instructor','$name','$password');");// or die('Unable to run query:' . mysqli_error($con));
        
            if (mysqli_error($con) != "")
                $_SESSION["accCreateErr"] = "Unable to run query:" . mysqli_error($con);
        }
        
        mysqli_close($con);
        
        // Send email
        if (!isset($_SESSION["accCreateErr"])) {
            //$message = fopen("../email.php", "r") or die("Unable to open file!"); //FIXME: https://stackoverflow.com/questions/1846882/open-basedir-restriction-in-effect-file-is-not-within-the-allowed-paths
            
            $email_from = 'benstef2015@gmail.com'; //TODO replace with actual LIACS email
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
            $message .=  "Hello,\nPlease open this e-mail in HTML-mode to view its contents.\nPlease do not reply to this e-mail.\n\nRegards,\n(notactually)LIACS"; // TODO change sender once relevant
            $message .= "\r\n\r\n--" . $boundary . "\r\n";
            $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
            
            // HTML body
            $message .= "Dear <td>".$name."</td>,</br> An account has been made for you on the
        <a href='http://liacs.leidenuniv.nl/~s1551396/InternshipApp/Login.php'>LIACS InternshipApp</a>.</br> Your username and password are as follows:</br> Username: <td>".$email."</td></br> Password: <td>".$password."</td></br> Please do not reply to this e-mail."; // TODO replace with file
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
