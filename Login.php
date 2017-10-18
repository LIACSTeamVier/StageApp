<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<body>
  
  <h1>Login pagina</h1>
  
  <form action="attempting_Login.php" method="post">
    username:
    <input type="text" name ="username"><br/>
    password:
    <input type="text" name="password"><br/>
    <input type="submit" value="Login">
  </form>
  
  <?php
  //$username = $_POST["username"];
  //$password = $_POST["password"];
  //$con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "presenteren", "s1551396");
  // Check connection
  //if (mysqli_connect_errno()) {
  //echo "Failed to connect to MySQL: " . mysqli_connect_error();
  //}

  //$result = mysqli_query($con, "SELECT * FROM Deadlines") or die('Unable to run query:' . mysqli_error());
  ?>

</body>
</html>
