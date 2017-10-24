<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<body>

  <?php
    if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
      header("Location: http://liacs.leidenuniv.nl/~s1551396/StageApp/Login.php");
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
    // Creating the account
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "-", "s1551396");
    // Check connection
    if (mysqli_connect_errno())
      echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $result = mysqli_query($con, "INSERT INTO StageApp_Gebruikers VALUES ('$email','Internship Instructor','$name','$password');") or die('Unable to run query:' . mysqli_error($con));
    
    mysqli_close($con);
    
    // Send email
    //$message = fopen("../email.php", "r") or die("Unable to open file!"); //FIXME: https://stackoverflow.com/questions/1846882/open-basedir-restriction-in-effect-file-is-not-within-the-allowed-paths
    $email_from = 'benstef2015@gmail.com'; //TODO replace with actual LIACS email
    $subject = "An account has been made for you on the LIACS InternshipApp";
    $message = "Dear $name, \n An account has been made for you on the
<a href='http://liacs.leidenuniv.nl/~s1551396/StageApp/Login.php'>LIACS InternshipApp</a>.\n Your username and password are as follows:\n Username: $email \n Password: $password \n Please do not reply to this e-mail."; // TODO replace with file
    $headers = "From: $email_from \r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    
    mail($email,$subject,$message,$headers);
    }
    
  ?>
  
</body>
</html> 
