<?php
	require_once "random_compat-2.0.11/lib/random.php";

	session_start();
	if( ($_SESSION["class"] != "Student") || empty($_SESSION["ReqDocID"]) || empty($_SESSION["ReqType"]) || empty($_SESSION["ReqStudentID"]) ){
		//header("Location: main_page.php");
		die("Wrong Session Vars");
	}

	$con = mysqli_connect("mysql.liacs.leidenuniv.nl", "csthesis", "-", "csthesis");
	// Check connection
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		die();
	}
	$randstring = random_str(32);
	$res = mysqli_query($con, "SELECT * FROM Begeleid WHERE ActivationCode='$randstring'");
	$numrow = mysqli_num_rows($res);
	while($numrow > 0){//make sure the activation code is unique
		$randstring = random_str(32);
		$res = mysqli_query($con, "SELECT * FROM Begeleid WHERE ActivationCode='$randstring'");
		$numrow = mysqli_num_rows($res);
	}
	
	$stmt = mysqli_prepare($con, "INSERT INTO Begeleid(type, DocentID, StudentID, Accepted, ActivationCode)
          VALUES (?,?,?,'0',?)");
	mysqli_stmt_bind_param($stmt,'ssss', $_SESSION["ReqType"], $_SESSION["ReqDocID"], $_SESSION["ReqStudentID"], $randstring);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	if(!$result)
		die('Unable to run query:' . mysqli_error());
	
	$result2 = mysqli_query($con, "SELECT BegEMAIL, BegeleiderNaam FROM Begeleider WHERE DocentID='".$_SESSION["ReqDocID"]."'");
	$row = mysqli_fetch_array($result2);
	if(!$result2)
		die('Unable to run query:' . mysqli_error());
		
	$result3 = mysqli_query($con, "SELECT StudentNaam FROM Afstudeerder WHERE StudentID='".$_SESSION["ReqStudentID"]."'");
	$rowres3 = mysqli_fetch_array($result3);
	if(!$result3)
		die('Unable to run query:' . mysqli_error());
	
	$StudentName = $rowres3["StudentNaam"];
	$email = $row["BegEMAIL"];
	$DocName = $row["BegeleiderNaam"];
	$StudentID = $_SESSION["ReqStudentID"];
	$type = $_SESSION["ReqType"];
	mysqli_close($con);

	$email_from = 'benstef2015@gmail.com'; //TODO replace with actual LIACS email
	$subject = "A Student Wants You as Masters Supervisor";
	$boundary = uniqid('np');
	
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "From: $email_from \r\n";
	$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
	
	// MIME stuff
	$message = "This is a MIME encoded message.";
	$message .= "\r\n\r\n--" . $boundary . "\r\n";
	$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
	
	// Plain text body
	$message .=  "Hello,\nPlease open this e-mail in HTML-mode to view its contents.\nPlease do not reply to this e-mail.\n\nThanks"; 
	$message .= "\r\n\r\n--" . $boundary . "\r\n";
	$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
	
	// HTML body
	$message .= "<html lang=\"en-UK\">
				<body>
				  <p>Dear $DocName,</p><br/>
				  <p>The student: $StudentName , $StudentID , has requested you to be their $type .</p></br>
				  <p>Click this <a href=\"http://liacs.leidenuniv.nl/~csthesis/request_list.php?code=$randomstr\">LINK</a> to accept their request.<p><br/>
				  <p>Please do not reply to this e-mail.</p><br/>
				</body>
				</html> ";
			
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "From: $email_from \r\n";
	$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
	
	if (!(mail($email,$subject,$message,$headers)))
		$_SESSION["emailErr"] = 1;

	if ($_SESSION["emailErr"] == 1){
		echo "sending email went wrong, either notify your requested supervisor to check their account on the system or </br>
			  delete the request and try again";
		echo "<a href=\"main_page.php\">Go back to the main page</a>";
	}
	else{
			echo "<script>alert(\"Successfully send an email request to your requested supervisor!\");
					location.href='main_page.php';
					exit;
					</script>";
			die();
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
