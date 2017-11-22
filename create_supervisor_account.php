<?php
    require_once "random_compat-2.0.11/lib/random.php";
    session_start();
	include 'sidebar_selector.php';
  
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

        <meta name="Description" content= "Create Supervisor Account" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Create supervisor account - LIACS Student Project Manager</title>
    </head>
    <body>
  
		<!-- TODO: change form to match other create_..._account -->
  
        <div class="main">
            <?php
                $class = $_SESSION["class"];
                if ($class != "Admin") {
                    header("Location: main_page.php");
                    exit;
                }
                else {
                    $password = random_str(8);
                    $role = 'Supervisor';
                    echo "<p>Fill in this form to create a new supervisor account.</p>";
                    echo "
                    <form action='attempting_to_email.php' method='post'>
						<table class=\"form\">
							<tr>
								<td>Full name:</td>
								<td><input type='text' name ='name'></td>
							</tr>
							<tr>
								<td>Email address:</td>
								<td><input type='text' name ='email'></td>
							</tr>
						</table>
                        <input type='hidden' name ='password' value='$password'>
                        <input type='hidden' name ='role' value='$role'>
						<br><br>
                        <input type='submit' value='Create account'>
                    </form>";
                }
            ?>  
        </div>
    </body>
</html> 
