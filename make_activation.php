<?php
	require_once "random_compat-2.0.11/lib/random.php";
	require_once "sidebar_student.php";
	date_default_timezone_set("Europe/Amsterdam");
	session_start();
	if( ($_SESSION["class"] != "Student") || empty($_SESSION["ReqDocID"]) || empty($_SESSION["ReqType"]) || empty($_SESSION["ReqStudentID"]) ){
		//header("Location: main_page.php");
		die("Wrong Session Vars");
	}
	$smessage = "";
	if($_POST["confirmed"] == "Confirm Request"){
		if(!empty($_POST["studentmessage"])){
			$smes =  test_input($_POST["studentmessage"]);
			$smessage = "The student said: $smes";
		}
				
		$configs = include("config.php");
		$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			die();
		}

		$randstring = random_str(32); 
		$res = mysqli_query($con, "SELECT * FROM Supervises WHERE ActivationCode='$randstring'");
		$numrow = mysqli_num_rows($res);
		while($numrow > 0){//make sure the activation code is unique
			$randstring = random_str(32);
			$res = mysqli_query($con, "SELECT * FROM Supervises WHERE ActivationCode='$randstring'");
			$numrow = mysqli_num_rows($res);
		}
		$datereq = date("Y-m-d: H:i:s");
		$stmt = mysqli_prepare($con, "INSERT INTO Supervises(type, SupID, StuID, Accepted, ActivationCode, DateRequested)
			  VALUES (?,?,?,'0',?,?)");
		mysqli_stmt_bind_param($stmt,'sssss', $_SESSION["ReqType"], $_SESSION["ReqDocID"], $_SESSION["ReqStudentID"], $randstring, $datereq);
		$result = mysqli_stmt_execute($stmt);
		//$result = mysqli_stmt_get_result($stmt);
		mysqli_stmt_close($stmt);
		if(!$result){echo "query 1";
			die('Unable to run query1:' . mysqli_error());}
		
		$result2 = mysqli_query($con, "SELECT SupEMAIL, SupName FROM Supervisor WHERE SupID='".$_SESSION["ReqDocID"]."'");
		$row = mysqli_fetch_array($result2);
		if(!$result2){echo "query 2";
			die('Unable to run query2:' . mysqli_error());}

		$result3 = mysqli_query($con, "SELECT StuName FROM Student WHERE StuID='".$_SESSION["ReqStudentID"]."'");
		$rowres3 = mysqli_fetch_array($result3);
		if(!$result3)
			die('Unable to run query3:' . mysqli_error());
		
		$StudentName = $rowres3["StuName"];
		$email = $row["SupEMAIL"];
		$DocName = $row["SupName"];
		$StudentID = $_SESSION["ReqStudentID"];
		$type = $_SESSION["ReqType"];
		mysqli_close($con);

		$email_from = $configs["noreply"];
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
		$message .= "Dear $DocName,\nThe student: $StudentName, $StudentID, has requested you to be their $type.\nEnter this url 'http://liacs.leidenuniv.nl/~csthesis/request_list.php?code=$randstring' in your browser to accept their request.\n$smessage\nPlease do not reply to this e-mail."; //"Hello,\nPlease open this e-mail in HTML-mode to view its contents.\nPlease do not reply to this e-mail.\n\nThanks"; 
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
		$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
		
		// HTML body
		$message .= "<html lang=\"en-UK\">
					<body>
					  <p>Dear $DocName,</p><br/>
					  <p>The student: $StudentName, $StudentID, has requested you to be their $type.</p>
					  "//<p>Click this <a href=\"http://liacs.leidenuniv.nl/~csthesis/request_list.php?code=$randstring\">LINK</a> to accept their request.<p><br/>
					 ."<p>Enter this url 'http://liacs.leidenuniv.nl/~csthesis/request_list.php?code=$randstring' in your browser to accept their request</p></br>
					 $smessage
					  <p>Please do not reply to this e-mail.</p><br/>
					</body>
					</html> ";
		//$message .= "<p>Dear $DocName,</p><br/>";
		//$message .="<p>The student: $StudentName , $StudentID , has requested you to";
		//$message .=" be their $type .</p></br>";
		//$message .="<p>Click this ";
		//$message .="<a href=\"http://liacs.leidenuniv.nl/~csthesis/request_list.php?code=$randomstr\">LINK</a>";
		//$message .="to accept their request.<p><br/>";
		//$message .=" <p>Please do not reply to this e-mail.</p><br/>";
		//$message .= "\r\n\r\n--" . $boundary . "--";
			

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: $email_from \r\n";
		$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

		if (!(mail($email,$subject,$message,$headers)))
			$_SESSION["emailErr"] = 1;

		if ($_SESSION["emailErr"] == 1){
			echo "sending email went wrong, either notify your requested supervisor to check their account on the system or</br>
				  delete the request and try again";

			echo "<a href=\"main_page.php\">Go back to the main page</a>";
		}
		else{
				echo "<script>alert(\"Successfully sent an email request to your requested supervisor!\");
						location.href='main_page.php';
						exit;
						</script>";
				die();
		}
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

function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" /> 
        <link rel="stylesheet" type="text/css" href="style.css">
		<title>Confirm supervisor - LIACS Student Project Manager</title>
	</head>
	<body>
		<div class="main">
			<h1>LIACS Student Project Manager</h1>
			<p>The request you have made should have been already been confirmed informally with the supervisor</p>
			<p>Click confirm to enter the request into the system if you have already communicated with the supervisor</p></br>
			<p>If you have not discussed the request with the supervisor click cancel</p></br>
			<p>Feel free to enter some custom info in the text field for the email 
			(like a reminder of what project you have discussed with the supervisor for instance)</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
				<textarea name="studentmessage" rows="5" cols="40"><?php echo $smessage;?></textarea></br>
				<input type="submit" name="confirmed" value="Confirm Request">
			</form>
			<form action="supervisor_list.php" method="post">
				<input type="submit" name="cancel" value="Cancel Request">
			</form>
		</div>
	</body>
</html>

