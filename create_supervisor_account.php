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
      //$password = random_bytes(8);
      $password = 'placeholder'; // FIXME replace with above once possible.
      echo "<h5>Please fill in the form below<h5></br>";
      echo "
        <form action='attempting_to_email.php' method='post'>
          <p>Supervisor's name:</br>
          <input type='text' name ='name'><br/></p>
          <p>Supervisor's e-mail address:</br>
          <input type='text' name ='email'><br/></p>
          <input type='hidden' name ='password' value='$password'>
          <input type='submit' value='Create account'>
        </form>";
      
    
    }
    
  ?>  
</div>

</body>
</html> 
