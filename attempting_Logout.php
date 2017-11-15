<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<body>

  <?php
    session_unset();
    session_destroy();
  
    header("Location: index.php");
    exit;
  ?>
  
</body>
</html>
