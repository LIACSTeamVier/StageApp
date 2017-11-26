<?php
	session_start();
    date_default_timezone_set("Europe/Amsterdam");
	require_once "sidebar_selector.php";
    require_once "general_functions.php";
	
	$configs = include("config.php");
	if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if(!(empty($_GET["code"]))){
			$randomstr = test_input($_GET["code"]);
			$dateacp = date("Y-m-d: H:i:s");
			$stmt = mysqli_prepare($con, "UPDATE Does SET Accepted='1', DateAccepted='$dateacp' WHERE ActivationCode=?");
			mysqli_stmt_bind_param($stmt,'s', $randomstr);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_affected_rows($con)>0){
				mysqli_stmt_close($stmt);
				
				$stmt3 = mysqli_prepare($con, "SELECT  StuID, ProjectName FROM Does WHERE ActivationCode=?");
				mysqli_stmt_bind_param($stmt3, 's', $randomstr);
				mysqli_stmt_execute($stmt3);
				$res3 = mysqli_stmt_get_result($stmt3);
				$rowres3 = mysqli_fetch_array($res3);
                $studid = $rowres3["StuID"];
                $projname = $rowres3["ProjectName"];
                mysqli_stmt_close($stmt3);
                
                $stmt4 = mysqli_prepare($con, "SELECT IConID, IConName FROM Project WHERE ProjectName=?");
                mysqli_stmt_bind_param($stmt4, 's', $projname);
				mysqli_stmt_execute($stmt4);
				$res4 = mysqli_stmt_get_result($stmt4);
				$rowres4 = mysqli_fetch_array($res4);
				$contactid = $rowres4["IConID"];
				$contactname = $rowres4["IConName"];
                mysqli_stmt_close($stmt4);    
                    
				$stmt2 = mysqli_prepare($con, "UPDATE Does SET ActivationCode=NULL WHERE ActivationCode=?");
				mysqli_stmt_bind_param($stmt2,'s', $randomstr);
				$result2 = mysqli_stmt_execute($stmt2);
				//$result2 = mysqli_stmt_get_result($stmt2);
				if(!$result2){
					die("mysql error");
				}
				mysqli_stmt_close($stmt2);
				sendMailToStudentInternAccept($con, $configs, $studid, $contactid, $projname, $contactname);
				echo "<script>alert(\"Successfully accepted student for your internship!\");
						location.href='main_page.php';
						exit;
						</script>";
				die();
			}
			echo "<div class=\"main\"><a>Wrong or Expired code</a>
                              <a href=\"main_page.php\">Go to the main page</a>
                              </div>";
			die();
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
		        
        $dateacp = date("Y-m-d: H:i:s");
		if(!(empty($_POST["InternshipStudent"]))){
			mysqli_query($con, "UPDATE Does SET Accepted='1', DateAccepted='$dateacp' WHERE ProjectName='".$_POST["ProjectName"]."' AND StuID='".$_POST["InternshipStudent"]."' AND Accepted='0'")
			or die('Unable to run query:' . mysqli_error());
			//mysqli_query($con, "UPDATE Supervises SET ActivationCode=NULL WHERE type='Internship Contact' AND SupID='$contactid' AND StuID='".$_POST["InternshipStudent"]."'")
              //          or die('Unable to run query:' . mysqli_error());
			//$type = 'Internship Contact';
			sendMailToStudentInternAccept($con, $configs, $_POST["InternshipStudent"], $contactid, $_POST["ProjectName"], NULL);
		}		
		mysqli_close($con);
	}
    
    	function sendMailToStudentInternAccept($con, $configs, $studid, $contactid, $projname, $contactname){
		
            $result = mysqli_query($con, "SELECT IConName, CompanyName FROM Internship_Contact WHERE IConID='$contactid'");
            $rowres = mysqli_fetch_array($result);
            $result2 = mysqli_query($con, "SELECT StuName, StuEMAIL FROM Student WHERE StuID='$studid'");
            $rowres2 = mysqli_fetch_array($result2);
            $StudentName = $rowres2["StuName"];
            $email = $rowres2["StuEMAIL"];
            if($contactname == NULL)
                $contactname = $rowres["IConName"];
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
            $message .= "Dear $StudentName,\n$contactname, from $compName has accepted your request to take part in their internship program, $projname\nPlease do not reply to this e-mail."; //"Hello,\nPlease open this e-mail in HTML-mode to view its contents.\nPlease do not reply to this e-mail.\n\nThanks"; 
            $message .= "\r\n\r\n--" . $boundary . "\r\n";
            $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
        
            // HTML body
            $message .= "<html lang=\"en-UK\">
                    <body>
                      <p>Dear $StudentName,</p>
                      <p>$contactname, from $compName has accepted your request to take part in their internship program, $projname</p>
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
    </head>
    <body>

        <div class="main">
            <?php
            $configs = include("config.php");
            $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
            // check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
        
            $temp = htmlspecialchars($_SERVER["PHP_SELF"]);
       
            $project_table = mysqli_query($con, "SELECT p.ProjectName, Description, IConID, d.StuID, Accepted, StuName, StuTel, StuEMAIL FROM Project p LEFT JOIN Does d ON d.ProjectName=p.ProjectName LEFT JOIN Student s ON d.StuID=s.StuID WHERE IConId='$contactid' AND Accepted='0'") or die('Unable to run query:' . mysqli_error());
            echo "These students want to join your internship, they should probably have contacted you already";
            echo "<table width='40%' id='1strequest_table' class=\"list\">"; // start a table tag in the HTML
            // column names
            echo "<tr>
                  <th onclick=\"sortTable(0)\">Project Name and Description</th>
                  <th onclick=\"sortTable(1)\">Student Name</th>
                  <th onclick=\"sortTable(2)\">Student Id</th>
                  <th onclick=\"sortTable(3)\">Student PhoneNumber</th>
                  <th onclick=\"sortTable(4)\">Student Email</th>
                  </tr>";
    
            // rows of the database
            while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
                echo "<tr><form action=\"$temp\" method=\"post\">
                      <td width='40%'><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                      <td>".$row['StuName']."</td>
                      <td>".$row['StuID']."</td>
                      <td>".$row['StuTel']."</td>
                      <td>".$row['StuEMAIL']."</td>
                      <td><input type=\"submit\" name=\"InternshipStudentDisp\" value=\"Accept This Student\">
                          <input type=\"hidden\" name=\"InternshipStudent\" value=\"".$row['StuID']."\" /></td>
                          <input type=\"hidden\" name=\"ProjectName\" value=\"".$row['ProjectName']."\" /></td>
                      </form></tr>"; 
            }
    
            echo "</table>"; //Close the table in HTML
            echo "</br></br>";

            mysqli_close($con);

            ?>
        </div>

    </body>
</html>
