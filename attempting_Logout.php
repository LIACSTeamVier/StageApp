<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<body>

  <?php
    session_unset();
    session_destroy();
  
    header("Location: Login.php");
    exit;
  ?>
  
</body>
</html>
