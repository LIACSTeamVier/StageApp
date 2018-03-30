<?php
	session_start();
    $highlight = "Students";
	require_once "sidebar_selector.php";
	require_once "general_functions.php";
	
	$configs = include("config.php");
	$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
	// Check connection
	if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
    
    $stuid = test_input($_POST["stuHistID"]);

?>
<!DOCTYPE html>
<html lang="en-UK">
	<head>
		<meta charset="utf-8" /> 
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Supervisor history for <?php echo $stuid;?> - LIACS Graduation Application</title>
	</head>
	<body>
		<div class="main">
                       <h1>LIACS Graduation Application</h1>
			<?php
				if($_SESSION["class"] != "Admin" || empty($stuid)) {
					header("Location: main_page.php");
					die();
				}
				//Get student info
				$stmt = mysqli_prepare($con, "SELECT * FROM Student WHERE StuID=?");//!!!!!JOIN PROJECT info
				mysqli_bind_param($stmt, 's', $stuid);
				mysqli_stmt_execute($stmt);
				$stuinfo = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
				mysqli_stmt_close($stmt);
				$stmt1 = mysqli_prepare($con, "SELECT p.ProjectName, p.Progress, d.StuID FROM Project p INNER JOIN Does d ON p.ProjectName=d.ProjectName WHERE StuID=?");
				mysqli_bind_param($stmt1, 's', $stuid);
				mysqli_stmt_execute($stmt1);
				$proinfo = mysqli_fetch_array(mysqli_stmt_get_result($stmt1));
				mysqli_stmt_close($stmt1);
				echo "<h3>Supervisor history for the student:</h3>
						<table class=\"list\">
							<tr>
								<th>Name</th><th>Project</th><th>Progress</th><th>Email</th><th>PhoneNumber</th><th>Student ID</th>
							</tr>
							<tr>
								<td>".$stuinfo['StuName']."</td><td>".$proinfo['ProjectName']."</td><td>".$proinfo['Progress']."</td><td>".$stuinfo['StuEMAIL']."</td><td>".$stuinfo['StuTel']."</td><td>$stuid</td>
							</tr>
						</table>";
				//List all students with their projects
				
				echo "</br></br></br>";
				
				$stmt2 = mysqli_prepare($con,
				 "SELECT s.type, s.SupID, s.Accepted, s.DateRequested, s.DateAccepted, s.DateTerminated, s.StuID, Supervisor.SupName, Supervisor.Topics FROM Supervises s LEFT JOIN Supervisor ON s.SupID=Supervisor.SupID WHERE StuID=? AND Accepted=1");
				mysqli_bind_param($stmt2, 's', $stuid);
				mysqli_stmt_execute($stmt2);
				$result = mysqli_stmt_get_result($stmt2);
				mysqli_stmt_close($stmt2);
				echo "<h3>Active supervisions</h3>
						<table class=\"list\">"; // start a table tag in the HTML
			
				// column names
				echo "<tr><th>Name</th>
							<th>Supervisor Topics</th>
							<th>Supervision Type</th>
							<th>Date Requested</th>
							<th>Date Accepted</th>
							</tr>";

				// rows of the database
				while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
					echo "<tr><td>" . $row['SupName'] . "</td>
								<td>" . $row['Topics'] . "</td>
								<td>" . $row['type'] . "</td>
								<td>" . $row['DateRequested'] . "</td>
								<td>" . $row['DateAccepted'] . "</td></tr>";
				}

				echo "</table><br>"; //Close the table in HTML
				echo "</br></br></br>";

				$stmt3 = mysqli_prepare($con,
				 "SELECT s.type, s.SupID, s.Accepted, s.DateRequested, s.DateAccepted, s.DateTerminated, s.StuID, Supervisor.SupName, Supervisor.Topics FROM Supervises s LEFT JOIN Supervisor ON s.SupID=Supervisor.SupID WHERE StuID=? AND Accepted=0");
				mysqli_bind_param($stmt3, 's', $stuid);
				mysqli_stmt_execute($stmt3);
				$result2 = mysqli_stmt_get_result($stmt3);
				mysqli_stmt_close($stmt3);
				echo "<h3>Pending supervision requests</h3>
						<table class=\"list\">"; // start a table tag in the HTML
			
				// column names
				echo "<tr><th>Name</th>
							<th>Supervisor Topics</th>
							<th>Supervision Type</th>
							<th>Date Requested</th>
							</tr>";

				// rows of the database
				while($row = mysqli_fetch_array($result2)){	 //Creates a loop to loop through results
					echo "<tr><td>" . $row['SupName'] . "</td>
								<td>" . $row['Topics'] . "</td>
								<td>" . $row['type'] . "</td>
								<td>" . $row['DateRequested'] . "</td></tr>";
				}

				echo "</table><br>"; //Close the table in HTML
				echo "</br></br></br>";

				$stmt4 = mysqli_prepare($con,
				 "SELECT s.type, s.SupID, s.Accepted, s.DateRequested, s.DateAccepted, s.DateTerminated, s.StuID, Supervisor.SupName, Supervisor.Topics FROM Supervises s LEFT JOIN Supervisor ON s.SupID=Supervisor.SupID WHERE StuID=? AND (Accepted='-1' OR Accepted='2')");//accepted is -1 when it is deleted, 2 when completed (maybe)
				mysqli_bind_param($stmt4, 's', $stuid);
				mysqli_stmt_execute($stmt4);
				$result3 = mysqli_stmt_get_result($stmt4);
				mysqli_stmt_close($stmt4);
				echo "<h3>Terminated supervisions</h3>
						<table class=\"list\">"; // start a table tag in the HTML
			
				// column names
				echo "<tr><th>Name</th>
							<th>Supervisor Topics</th>
							<th>Supervision Type</th>
							<th>Date Requested</th>
							<th>Date Accepted</th>
							<th>Date Terminated</th>
							</tr>";

				// rows of the database
				while($row = mysqli_fetch_array($result3)){	 //Creates a loop to loop through results
					echo "<tr><td>" . $row['SupName'] . "</td>
								<td>" . $row['Topics'] . "</td>
								<td>" . $row['type'] . "</td>
								<td>" . $row['DateRequested'] . "</td>
								<td>" . $row['DateAccepted'] . "</td>
								<td>" . $row['DateTerminated'] . "</td></tr>";
				}

				echo "</table><br>"; //Close the table in HTML

				?>
		</div>

	</body>
</html> 
