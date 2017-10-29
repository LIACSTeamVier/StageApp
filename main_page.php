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
    $username = $_SESSION["username"];
    $class = $_SESSION["class"];
    echo "<h1>Welcome " . "$username" . "." ."</h1>";
    echo "<p>You are a(n) " . "$class" . "." ."<p>"; //TODO temp, remove line.
    
    // After sending an e-mail
    if (isset($_SESSION["creatingAccount"])) {
        if (isset($_SESSION["accCreateErr"]))
            echo "ERROR: " . $_SESSION["accCreateErr"] . "</br>Could not create account. No e-mail was sent.</br>";
        else {
            echo "Account created successfully.</br>";
            if (isset($_SESSION["emailErr"]))
                echo "ERROR: e-mail could not be delivered. Please manually inform the recipient. Their username should be their e-mail address. Their password can be found in the 'name' table."; //TODO vervang 'name'
        }
    }
    unset($_SESSION["emailErr"]);
    unset($_SESSION["accCreateErr"]);
    unset($_SESSION["creatingAccount"]);
    
    if ($class == "Admin") {
      // Creating an account
        echo "
          <p> Create an account </p>
          <select name='createAcc' onchange='location = this.value;'>
            <option disabled selected value> -- Select an option -- </option>
            <option value='create_admin_account.php'>Admin</option>
            <option value='create_intern_contact_account.php'>Internship Contact</option>
            <option value='create_supervisor_account.php'>Supervisor</option>
            <option value='create_Student_account.php'>Student</option>
          </select>";
    
    
    
    
    }
    
    
  ?>

  <form action="attempting_Logout.php" method="post">
    <input type="submit" value="Logout">
  </form>
</div>

</body>
</html> 
