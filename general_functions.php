<?php
    require_once "random_compat-2.0.11/lib/random.php";
    require_once "password_compat-master/lib/password.php"; 
    
    // Database query
    function query_our_database($query) {
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);

        // check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $result = mysqli_query($con, $query) or die('Unable to run query:' . mysqli_error());

        mysqli_close($con);

        return $result;
    }
    
    //Random string generator
    
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
    
    // Account creation functions
    
    function sendEmail($name, $email, $uname, $password) {
        $configs = include("config.php");
        $email_from = $configs["noreply"];
        $subject = "An account has been made for you on the LIACS Student Project Manager";
        $boundary = uniqid('np');
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $email_from \r\n";
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        
        // MIME stuff
        $message = "This is a MIME encoded message.";
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
        
        // Plain text body
        $message .= "Dear ".$name.",\nAn account has been made for you on the LIACS Student Project Manager. Please follow the following link:\nhttp://csthesis.liacs.leidenuniv.nl\nYour username and password are as follows:\nUsername: ".$uname."\nPassword: ".$password."\nPlease do not reply to this e-mail.\n(notactually)LIACS"; // TODO replace with file
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
        
        // HTML body
        $message .= "Dear <td>".$name."</td>,</br> An account has been made for you on the
    <a href='http://csthesis.liacs.leidenuniv.nl'>LIACS Student Project Manager</a>.</br> Your username and password are as follows:</br> Username: <td>".$uname."</td></br> Password: <td>".$password."</td></br> Please do not reply to this e-mail.</br>(notactually)LIACS"; // TODO replace with file
        $message .= "\r\n\r\n--" . $boundary . "--";
        
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $email_from \r\n";
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        //var_dump($email);
        //die();
        return mail($email,$subject,$message,$headers);
    }
    
    function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
    }
    
    function checkDuplicates($uname, &$unameErr) {
        $error = False;
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        // check connection
        if (mysqli_connect_errno()) {
            $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
            $error = True;
        }
        else {
            $stmt = mysqli_prepare($con, "SELECT * FROM InternshipApp_Users i WHERE i.Identifier=?");
            mysqli_stmt_bind_param($stmt,'s', $uname);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            if (!$result) {
                $_SESSION["regErr"] = "Unable to run query: " . mysqli_error($con);
                $error = True;
            }
            else
                $row = mysqli_fetch_row($result);
            if(!empty($row)) {
                $unameErr = "Username already taken";
                $error = True;
            }
            mysqli_close($con);
        }
        return $error;
    }
    
    function insertIntoUsers($uname, $class, $name, $password) {
        $configs = include("config.php");
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
        if (mysqli_connect_errno()) {
            $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        else {
            $stmt1 = mysqli_prepare($con, "INSERT INTO InternshipApp_Users VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt1,'ssss', $uname, $class, $name, $password);
            $result1 = mysqli_execute($stmt1);
            mysqli_close($stmt1);
            if (!$result1) {
                $_SESSION["regErr"] = "Unable to run query:" . mysqli_error($con);
            }
            mysqli_close($con);
        }
    }
?>
