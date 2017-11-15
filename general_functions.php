<?php
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
  
    function sendEmail($name, $email, $password) {
        $configs = include("config.php");
        $email_from = $configs["noreply"];
        $subject = "An account has been made for you on the LIACS InternshipApp";
        $boundary = uniqid('np');
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $email_from \r\n";
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        
        // MIME stuff
        $message = "This is a MIME encoded message.";
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
        
        // Plain text body
        $message .= "Dear ".$name.",\nAn account has been made for you on the LIACS InternshipApp. Please follow the following link:\nhttp://csthesis.liacs.leidenuniv.nl\nYour username and password are as follows:\nUsername: ".$email."\nPassword: ".$password."\nPlease do not reply to this e-mail.\n(notactually)LIACS"; // TODO replace with file
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
        
        // HTML body
        $message .= "Dear <td>".$name."</td>,</br> An account has been made for you on the
    <a href='http://csthesis.liacs.leidenuniv.nl'>LIACS InternshipApp</a>.</br> Your username and password are as follows:</br> Username: <td>".$email."</td></br> Password: <td>".$password."</td></br> Please do not reply to this e-mail.</br>(notactually)LIACS"; // TODO replace with file
        $message .= "\r\n\r\n--" . $boundary . "--";
        
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $email_from \r\n";
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        //var_dump($email);
        //die();
        return mail($email,$subject,$message,$headers);
    }
?>
