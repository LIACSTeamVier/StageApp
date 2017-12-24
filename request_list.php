<?php
	session_start();
	date_default_timezone_set("Europe/Amsterdam");
	$configs = include("config.php");
    require_once "general_functions.php";
    require_once "sidebar_selector.php";
    
	if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if(!(empty($_GET["code"]))){
			$randomstr = test_input($_GET["code"]);
			$dateacp = date("Y-m-d: H:i:s");
			$stmt = mysqli_prepare($con, "UPDATE Supervises SET Accepted='1', DateAccepted='$dateacp' WHERE ActivationCode=?");
			mysqli_stmt_bind_param($stmt,'s', $randomstr);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if(mysqli_affected_rows($con)>0){
				mysqli_stmt_close($stmt);
    
                $stmt3 = mysqli_prepare($con, "SELECT type, SupID, StuID FROM Supervises WHERE ActivationCode=?");
                mysqli_stmt_bind_param($stmt3, 's', $randomstr);
				mysqli_stmt_execute($stmt3);
				$res3 = mysqli_stmt_get_result($stmt3);
				$rowres3 = mysqli_fetch_array($res3);
				$type = $rowres3["type"];
				$studid = $rowres3["StuID"];
				$docid = $rowres3["SupID"];
				mysqli_stmt_close($stmt3);
		
				$stmt2 = mysqli_prepare($con, "UPDATE Supervises SET ActivationCode=NULL WHERE ActivationCode=?");
				mysqli_stmt_bind_param($stmt2,'s', $randomstr);
				$result2 = mysqli_stmt_execute($stmt2);
				if(!$result2){
					die("mysql error");
				}
				mysqli_stmt_close($stmt2);
                sendMailToStudent($con, $configs, $studid, $docid, $type);
				echo "<script>alert(\"Successfully accepted being a supervisor for the student!\");
						location.href='main_page.php';
						exit;
						</script>";
				die();

			}
            ?>
            <link rel="stylesheet" type="text/css" href="style.css">
			<div class="main">
                <table class="form">
                    <tr>
                        <td>Wrong or Expired url.</td>
                    </tr>
                    <tr>
                        <td><a href="index.php">Return to the login page</a></td>
                    </tr>
                </table>
            </div>
            <?php
			die();
		}

	}

	if (($_SESSION["class"] != "Admin") && ($_SESSION["class"] != "Supervisor")){
		// Redirect to main page
		header("Location: main_page.php");
		die();
	}
		$docid = $_SESSION["ID"];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		//TODO set activation codes op null
		$dateacp = date("Y-m-d: H:i:s");
		if(!(empty($_POST["FirstStudent"]))){
			mysqli_query($con, "UPDATE Supervises SET Accepted='1', DateAccepted='$dateacp', ActivationCode=NULL WHERE type='First Supervisor' AND SupID='$docid' AND StuID='".$_POST["FirstStudent"]."' AND Accepted='0'")
			or die('Unable to run query:' . mysqli_error());
			$type = 'First Supervisor';
            sendMailToStudent($con, $configs, $_POST["FirstStudent"], $docid, $type);
		}
		if(!(empty($_POST["SecondStudent"]))){
			mysqli_query($con, "UPDATE Supervises SET Accepted='1', DateAccepted='$dateacp', ActivationCode=NULL WHERE type='Second Supervisor' AND SupID='$docid' AND StuID='".$_POST["SecondStudent"]."' AND Accepted='0'")
			or die('Unable to run query:' . mysqli_error());
			$type = 'Second Supervisor';
			sendMailToStudent($con, $configs, $_POST["SecondStudent"], $docid, $type);
		}
				
		mysqli_close($con);
	}
    
    function sendMailToStudent($con, $configs, $studID, $docID, $type){
		
		$result = mysqli_query($con, "SELECT SupName FROM Supervisor WHERE SupID='$docID'");
		$rowres = mysqli_fetch_array($result);

		$result2 = mysqli_query($con, "SELECT StuName, StuEMAIL FROM Student WHERE StuID='$studID'");
		$rowres2 = mysqli_fetch_array($result2);

		$StudentName = $rowres2["StuName"];
		$email = $rowres2["StuEMAIL"];
		$DocName = $rowres["SupName"];
		$email_from = $configs["noreply"];
		$subject = "A supervisor has accepted you";
		$boundary = uniqid('np');
	
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: $email_from \r\n";
		$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
	
		// MIME stuff
		$message = "This is a MIME encoded message.";
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
		$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
	
		// Plain text body
		$message .= "Dear $StudentName,\n$DocName, has accepted being your $type\n Enter this url'http://csthesis.liacs.leidenuniv.nl' in your browser to go to the LIACS InternshipApp.\nPlease do not reply to this e-mail.";
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
		$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
	
		// HTML body
		$message .= "<html lang=\"en-UK\">
				<body>
				  <p>Dear $StudentName,</p>
				  <p>$DocName has accepted being your $type.</p>
                  <p><a href='http://csthesis.liacs.leidenuniv.nl'>Click here</a> to go to the LIACS InternshipApp.<p>
				  <p>Please do not reply to this e-mail.</p>
				</body>
				</html> ";

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: $email_from \r\n";
		$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        if(!mail($email,$subject,$message,$headers)){
			echo "Mail sending failed";
		}
	}
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Requesting Students" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Requesting students - LIACS Student Project Manager</title>
        <script src="sortTable.js"></script>
    </head>
    <body>

        <div class="main">
            <h1>LIACS Student Project Manager</h1>
            <?php
                $configs = include("config.php");
                $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
                
                // check connection
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                }
                 
                $temp = htmlspecialchars($_SERVER["PHP_SELF"]);
                
                $RoleAllowRes = mysqli_query($con, "SELECT SupID, RoleFirst, RoleSecond FROM Supervisor WHERE SupID='$docid'")or die('Unable to run query:' . mysqli_error());
                $RoleAllow = mysqli_fetch_array($RoleAllowRes);
                if($RoleAllow['RoleFirst'] == "yes"){
		            $project_table = mysqli_query($con, "SELECT * FROM Supervises b WHERE b.SupID='$docid' AND b.type = 'First Supervisor' AND b.Accepted='0'") or die('Unable to run query:' . mysqli_error());
		            echo "These students want you as FIRST SUPERVISOR";
		            echo "<table class=\"list\" id='1strequest_table'>"; // start a table tag in the HTML
		            // column names
		            echo "<tr><th onclick=\"sortTable(0, '1strequest_table')\">Student Id</th>
				              <th onclick=\"sortTable(1, '1strequest_table')\">Student Name</th></tr>";
		
		            // rows of the database
		            while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
			            $student_name_get = mysqli_query($con, "SELECT StuName FROM Student WHERE StuID='".$row['StuID']."'")or die('Unable to run query:' . mysqli_error());
			            $student_name = mysqli_fetch_array($student_name_get);
			            echo "<tr><form action=\"$temp\" method=\"post\">
				              <td>" . $row['StuID'] . "</td>
				              <td>".$student_name['StuName']."</td>
				              <td><input type=\"submit\" name=\"FirstStudentDisp\" value=\"Accept This Student\">
					              <input type=\"hidden\" name=\"FirstStudent\" value=\"".$row['StuID']."\" /></td>
				              </form></tr>";  //$row['index'] the index here is a field name
		            }
		
		            echo "</table>"; //Close the table in HTML
		            echo "</br></br>";
	            }
	            if($RoleAllow['RoleSecond'] == "yes"){
		            $project_table = mysqli_query($con, "SELECT * FROM Supervises b WHERE b.SupID='$docid' AND b.type = 'Second Supervisor' AND b.Accepted='0'") or die('Unable to run query:' . mysqli_error());
		            echo "These students want you as SECOND SUPERVISOR";
		            echo "<table class=\"list\" id='2ndrequest_table'>"; // start a table tag in the HTML
		            // column names
		            echo "<tr><th onclick=\"sortTable(0, '2ndrequest_table')\">Student Id</th>
				              <th onclick=\"sortTable(1, '2ndrequest_table')\">Student Name</th></tr>";
		
		            // rows of the database
		            while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
			            $student_name_get = mysqli_query($con, "SELECT StuName FROM Student WHERE StuID='".$row['StuID']."'")or die('Unable to run query:' . mysqli_error());
			            $student_name = mysqli_fetch_array($student_name_get);
			            echo "<tr><form action=\"$temp\" method=\"post\">
				              <td>" . $row['StuID'] . "</td>
				              <td>".$student_name['StuName']."</td>
				              <td><input type=\"submit\" name=\"SecondStudentDisp\" value=\"Accept This Student\">
					              <input type=\"hidden\" name=\"SecondStudent\" value=\"".$row['StuID']."\" /></td>
				              </form></tr>";  //$row['index'] the index here is a field name
			
		            }
		
		            echo "</table>"; //Close the table in HTML
	            }
	
                mysqli_close($con);
              
              
                
            ?>
        </div>

    </body>
</html>

