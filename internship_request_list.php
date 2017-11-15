
<?php
	session_start();
	$configs = include("config.php");
	if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if(!(empty($_GET["code"]))){
			$randomstr = test_input($_GET["code"]);
			$stmt = mysqli_prepare($con, "UPDATE Supervises SET Accepted='1' WHERE ActivationCode=?");
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
				$contactid = $rowres3["SupID"];
				mysqli_stmt_close($stmt3);
		
				$stmt2 = mysqli_prepare($con, "UPDATE Supervises SET ActivationCode=NULL WHERE ActivationCode=?");
				mysqli_stmt_bind_param($stmt2,'s', $randomstr);
				$result2 = mysqli_stmt_execute($stmt2);
				//$result2 = mysqli_stmt_get_result($stmt2);
				if(!$result2){
					die("mysql error");
				}
				mysqli_stmt_close($stmt2);
				sendMailToStudent($con, $configs, $studid, $contactid, $type);
				echo "<script>alert(\"Successfully accepted student for your internship!\");
						location.href='main_page.php';
						exit;
						</script>";
				die();
			}
			header("Location: main_page.php");
			die("Wrong Code");
		}
		
	}
	if (($_SESSION["class"] != "Admin") && ($_SESSION["class"] != "Internship Contact")){
		//redirect to main page
		header("Location: main_page.php");
		die();
	}
	$contactid = $_SESSION["ID"];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		//TODO set activation codes op null
		/*if(!(empty($_POST["FirstStudent"]))){
			mysqli_query($con, "UPDATE Supervises SET Accepted='1' WHERE type='First Supervisor' AND SupID='$contactid' AND StuID='".$_POST["FirstStudent"]."'")
			or die('Unable to run query:' . mysqli_error());
			mysqli_query($con, "UPDATE Supervises SET ActivationCode=NULL WHERE type='First Supervisor' AND SupID='$contactid' AND StuID='".$_POST["FirstStudent"]."'")
                        or die('Unable to run query:' . mysqli_error());
			$type = 'First Supervisor';
                        sendMailToStudent($con, $configs, $_POST["FirstStudent"], $contactid, $type);
		}
		if(!(empty($_POST["SecondStudent"]))){
			mysqli_query($con, "UPDATE Supervises SET Accepted='1' WHERE type='Second Supervisor' AND SupID='$contactid' AND StuID='".$_POST["SecondStudent"]."'")
			or die('Unable to run query:' . mysqli_error());
			mysqli_query($con, "UPDATE Supervises SET ActivationCode=NULL WHERE type='Second Supervisor' AND SupID='$contactid' AND StuID='".$_POST["SecondStudent"]."'")
                        or die('Unable to run query:' . mysqli_error());
			$type = 'Second Supervisor';
			sendMailToStudent($con, $configs, $_POST["SecondStudent"], $contactid, $type);
		}*/
		if(!(empty($_POST["InternshipStudent"]))){
			mysqli_query($con, "UPDATE Supervises SET Accepted='1' WHERE type='Internship Contact' AND SupID='$contactid' AND StuID='".$_POST["SecondStudent"]."'")
			or die('Unable to run query:' . mysqli_error());
			mysqli_query($con, "UPDATE Supervises SET ActivationCode=NULL WHERE type='Internship Contact' AND SupID='$contactid' AND StuID='".$_POST["SecondStudent"]."'")
                        or die('Unable to run query:' . mysqli_error());
			$type = 'Internship Contact';
			sendMailToStudent($con, $configs, $_POST["InternshipStudent"], $contactid, $type);
		}		
		mysqli_close($con);
	}
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
    	function sendMailToStudent($con, $configs, $studID, $contactID, $type){
		
		$result = mysqli_query($con, "SELECT IConName, CompanyName FROM Internship_Contact WHERE IConID='$contactID'");
		$rowres = mysqli_fetch_array($result);
		$result2 = mysqli_query($con, "SELECT StuName, StuEMAIL FROM Student WHERE StuID='$studID'");
		$rowres2 = mysqli_fetch_array($result2);
		$StudentName = $rowres2["StuName"];
		$email = $rowres2["StuEMAIL"];
		$contactName = $rowres["IConName"];
		$compName = $rowres["CompanyName"];
		$email_from = $configs["noreply"];
		$subject = "You have been accepted for an internship";
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
				  <p>Dear $StudentName,</p><br/>
				  <p>$contactName, from $compName has accepted being your $type</p></br>
				  <p>Please do not reply to this e-mail.</p><br/>
				</body>
				</html> ";
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: $email_from \r\n";
		$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
		//echo "<div class=\"main\">";
		//var_dump($email, $subject, $messsage, $headers);	echo "</div>";
		mail($email,$subject,$message,$headers);
	}
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Requesting Students" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Requesting Students</title>
    </head>
    <body>

        <div class="sidepane">
            <a href="main_page.php">Overview</a>
            <a href="project_list.php">Projects</a>
            <a href="#">Contact</a>
            <a href="database_table.php">Database</a>
            <a href="#">Help</a></a>
        </div>

        <div class="main">
            <?php
                $configs = include("config.php");
                $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
                
                // check connection
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                }
        
        
                $temp = htmlspecialchars($_SERVER["PHP_SELF"]);
        
        
		        $project_table = mysqli_query($con, "SELECT * FROM Supervises b WHERE b.SupID='$contactid' AND b.type = 'Internship Contact' AND b.Accepted='0'") or die('Unable to run query:' . mysqli_error());
		        echo "These students want to join your internship";
		        echo "<table width='40%' id='1strequest_table'>"; // start a table tag in the HTML
		        // column names
		        echo "<tr><th onclick=\"sortTable(0)\">Student Id</th>
				          <th onclick=\"sortTable(1)\">Student Name</th></tr>";
		
		        // rows of the database
		        while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
			        $student_name_get = mysqli_query($con, "SELECT StuName FROM Student WHERE StuID='".$row['StuID']."'")or die('Unable to run query:' . mysqli_error());
			        $student_name = mysqli_fetch_array($student_name_get);
			        echo "<tr><form action=\"$temp\" method=\"post\">
				          <td>" . $row['StuID'] . "</td>
				          <td>".$student_name['StuName']."</td>
				          <td><input type=\"submit\" name=\"InternshipStudentDisp\" value=\"Accept This Student\">
					          <input type=\"hidden\" name=\"InternshipStudent\" value=\"".$row['StuID']."\" /></td>
				          </form></tr>";  //$row['index'] the index here is a field name
		        }
		
		        echo "</table>"; //Close the table in HTML
		        echo "</br></br>";
	
	
	
                mysqli_close($con);
  
  
    
            ?>
        </div>

    </body>
</html>

