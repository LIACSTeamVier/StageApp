<?php
    require_once "random_compat-2.0.11/lib/random.php";
    session_start();
  
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

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Create Student Account" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Create Student Account</title>
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
                $class = $_SESSION["class"];
                if ($class != "Admin") {
                    header("Location: main_page.php");
                    exit;
                }
                else {
                    $password = random_str(8);
                    $role = 'Student';
                    echo "<h5>Please fill in the form below<h5></br>";
                    echo "
                    <form action='attempting_to_email.php' method='post'>
                        <p>Student's name:</br>
                        <input type='text' name ='name'><br/></p>
                        <p>Student's Studentnumber:</br>
                        <input type='text' name ='studentnumber'><br/></p>
                        <p>Student's e-mail address:</br>
                        <input type='text' name ='email'><br/></p>
                        <input type='hidden' name ='password' value='$password'>
                        <input type='hidden' name ='role' value='$role'>
                        <input type='submit' value='Create account'>
                    </form>";
                }
            ?>  
        </div>
    </body>
</html> 
