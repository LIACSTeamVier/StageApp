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
    $identifierlength = 50; // length of the identifier according to table InternshipApp_Users
    $class = $_SESSION["class"];
    if ($class != "Admin") {
      header("Location: main_page.php");
      exit;
    }
    else {
        $_SESSION["creatingAccount"] = 0;
        // Creating the account
        $configs = include("config.php");
        $name = $_POST["name"];
        $email = $_POST["email"];
        $class = $_POST["role"];
        if ($class == "Student")
            $username = $_POST["studentnumber"];
        else
            $username = substr($_POST["email"], 0, $identifierlength);
        $password = $_POST["password"];
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        // Check connection
        if (mysqli_connect_errno())
            $_SESSION["accCreateErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        else {
            $result = mysqli_query($con, "INSERT INTO InternshipApp_Users VALUES ('$username','$class','$name','$password');");
            if (mysqli_error($con) != "")
                $_SESSION["accCreateErr"] = "Unable to run query:" . mysqli_error($con);
            else if ($class == 'Student') {
                $result = mysqli_query($con, "INSERT INTO Student VALUES ('$username','$name','$email',NULL);");
                if (mysqli_error($con) != "")
                    $_SESSION["accCreateErr"] = "Unable to run query:" . mysqli_error($con);
                mysqli_close($con);
            }
        }
        
        mysqli_close($con);
        
        // Send email
        if (!isset($_SESSION["accCreateErr"])) {
            
            $email_from = $configs["noreply"];
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
            $message .= "Dear ".$name.",\nAn account has been made for you on the LIACS InternshipApp. Please follow the following link:\nhttp://csthesis.liacs.leidenuniv.nl\nYour username and password are as follows:\nUsername: ".$username."\nPassword: ".$password."\nPlease do not reply to this e-mail.\n(notactually)LIACS"; // TODO replace with file
            $message .= "\r\n\r\n--" . $boundary . "\r\n";
            $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
            
            // HTML body
            $message .= "Dear <td>".$name."</td>,</br> An account has been made for you on the
        <a href='http://csthesis.liacs.leidenuniv.nl'>LIACS InternshipApp</a>.</br> Your username and password are as follows:</br> Username: <td>".$username."</td></br> Password: <td>".$password."</td></br> Please do not reply to this e-mail.</br>(notactually)LIACS"; // TODO replace with file
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
