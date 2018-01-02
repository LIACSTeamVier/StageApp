<?php
session_start();
require_once "general_functions.php";

$id = $_SESSION["ID"];
$project = $_POST["prjctname"];
/*if !($_SERVER["REQUEST_METHOD"] == "POST") {
	 header("Location: main_page.php");
}*/
?>
 
 <!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="sortTable.js"></script>
<title>Subscribe to project - LIACS Student Project Manager</title>
</head>
<body>
<h1>Your request is being processed, please wait...</h1>
<?php
	$configs = include("config.php");
	
	$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
	if(!$con){
		echo "error, no connection";
	}
	
	/*$sql = "DELETE FROM Does WHERE StuID= '" . $id . "'"; unneeded and in the way of logging
	if (!$con->query($sql)){
		echo "ERROR: connection time-out";
	}else {*/
		$stmt = $con->prepare("INSERT INTO Does (StuID, ProjectName, DateRequested, ActivationCode, Accepted) VALUES (?,?,?,?,'0')");
		$stmt->bind_param("ssss", $id, $project, date('Y-m-d:h:i:s'), $randstring);
		
		$sql2 = "SELECT * FROM Supervisor INNER JOIN Supervises ON Supervisor.SupID = Supervises.SupID WHERE Supervises.StuID = '" . $_SESSION['ID'] . "' AND Supervises.Accepted = '1'";
		
		$result = $con->query($sql2);
		if(!$result){
			echo "ERROR: Connection time-out";
		}else{
		    $to = $result->fetch_assoc();
            $randstring = random_str(32); // TODO insert this into ActivationCode column of Does! this works already? (cause stmt is executed later maybe?)

            $email_from = $configs["noreply"];
	        $subject = "Subscription request";
	        $boundary = uniqid('np');
	
	        $headers = "MIME-Version: 1.0\r\n";
	        $headers .= "From: $email_from \r\n";
	        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
	
	        // MIME stuff
	        $message = "This is a MIME encoded message.";
	        $message .= "\r\n\r\n--" . $boundary . "\r\n";
	        $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
	
	        // Plain text body
	        $message .= "Dear ".$to["SupName"].",\n\nThe student: ".$_SESSION["username"].", $id, has requested access to the project:\n$project.\nEnter this url 'http://csthesis.liacs.leidenuniv.nl/request_list.php?code=$randstring' in your browser to accept their request.\n\nPlease do not reply to this e-mail.";
	        $message .= "\r\n\r\n--" . $boundary . "\r\n";
	        $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
	
	        // HTML body
	        $message .= "<html lang=\"en-UK\">
				           <body>
				             <p>Dear ".$to["SupName"].",</p>
				             <p>The student: ".$_SESSION["username"].", $id, has requested access to the project:</p>
                             <p>$project.</p>
				             <p><a href=\"http://csthesis.liacs.leidenuniv.nl/project_request_list.php?code=".$randstring."\">Click here</a> to accept their request.<p>
				             $smessage
				             <p>Please do not reply to this e-mail.</p>
                             <p>(notactually)LIACS</p>
				          </body>
				        </html> ";

	        $headers = "MIME-Version: 1.0\r\n";
	        $headers .= "From: $email_from \r\n";
	        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";


			$stmt->execute();
            mail($to["SupEMAIL"],$subject,$message,$headers);

		    header("Location: main_page.php");
		}
	//}
	
	echo "<a href= 'main_page.php'>Return Home</a>";
?>
</body>
</html>
