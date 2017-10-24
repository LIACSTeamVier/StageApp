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
      echo "<h5>Please fill in the form below<h5></br>";
      echo "
        <form action='attempting_to_email.php' method='post'>
          <p>Instructor's name:</br>
          <input type='text' name ='name'><br/></p>
          <p>Instructor's e-mail address:</br>
          <input type='text' name ='email'><br/></p>
          <p>Instructor's temporary login password:</br>
          <input type='text' name='password'><br/></p>
          <input type='submit' value='Create account'>
        </form>";
      
    
    }

    
  ?>  
  
</body>
</html> 
