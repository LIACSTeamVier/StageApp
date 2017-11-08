<?php
  require_once "random_compat-2.0.11/lib/random.php";
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
      $password = random_str(8);
      //$password = 'placeholder'; // FIXME replace with above once possible.
      $role = 'Internship Contact';
      echo "<h5>Please fill in the form below<h5></br>";
      echo "
        <form action='attempting_to_email.php' method='post'>
          <p>Contact's name:</br>
          <input type='text' name ='name'><br/></p>
          <p>Contact's e-mail address:</br>
          <input type='text' name ='email'><br/></p>
          <input type='hidden' name ='password' value='$password'>
          <input type='hidden' name ='role' value='$role'>
          <input type='submit' value='Create account'>
        </form>";
    }
    
/** From StackOverFlow https://stackoverflow.com/a/31107425 
 *  Under Creative Commons Licence Attribution-ShareAlike 3.0 
 * 
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}   
  ?>  
</div>

</body>
</html> 
