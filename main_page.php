<?php
include 'general_functions.php';
session_start();
include 'sidebar_selector.php';
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }   
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["progressupdate"])){
            //if (!empty($_POST["progupdate"])){
                if($_POST["progupdate"] != $_POST["progold"]){
                    $pupupdate = test_input($_POST["progupdate"]);
                    $resproj = mysqli_query($con,"SELECT * FROM Does WHERE StuID='".$_SESSION["ID"]."'");
                    $rowproj = mysqli_fetch_array($resproj);
                    $projname = $rowproj["ProjectName"];
                    $stmt = mysqli_prepare($con, "UPDATE Project SET Progress=? WHERE ProjectName=?");
                    mysqli_bind_param($stmt, 'ss', $pupupdate, $projname);
                    mysqli_stmt_execute($stmt);
                    $numrow = mysqli_affected_rows($con);
                    mysqli_stmt_close($stmt);
                    if($numrow != 1){
                        die("Error updating progress");
                    }
                }
            //}          
        }        
    }
    mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en-UK">
	<head>
		<meta charset="utf-8" /> 

		<meta name="Description" content= "Home" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Overview - LIACS Student Project Manager</title>
	</head>
	<body>

		<div class="main">
			<?php
				if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
					header("Location: index.php");
					exit;
				}
				$username = $_SESSION["username"];
				$class = $_SESSION["class"];
				echo "<h1>Welcome " . "$username" ."</h1>";
				echo "You are logged in as " . "$class" . "." ."<p>"; //TODO temp, remove line.

				// After sending an e-mail
				if (isset($_SESSION["regErr"])) {
					echo $_SESSION["regErr"];
				}
				unset($_SESSION["regErr"]);

				if ($class == "Admin") {
					//List all students with their projects
					$result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress, Supervisor.SupName FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName LEFT JOIN Supervisor ON Project.SupID=Supervisor.SupID");
					 
					echo "<h3>Student overview</h3>
							<table class=\"list\">"; // start a table tag in the HTML
				
					// column names
					echo "<tr><th>Name</th>
								<th>Project</th>
								<th>Progress</th>
								<th>First Supervisor</th>
								<th>Second Supervisor</th>
								<th>Student ID</th>
								<th>E-mail</th>
								<th>Telephone</th>
								</tr>";

					// rows of the database
					while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
						$SupName1 ="";//No Supervisor";
						$SupName2 ="";//"No Supervisor";
						$supervisorresult = query_our_database("SELECT * FROM Supervises WHERE StuID='".$row['StuID']."' AND Accepted='1'");
                                                while($suprow = mysqli_fetch_array($supervisorresult)){
						    if($suprow['type'] == "First Supervisor"){
						        $NameRow1 = mysqli_fetch_array(query_our_database("SELECT SupName FROM Supervisor WHERE SupID='".$suprow['SupID']."'"));
							$SupName1 = $NameRow1['SupName'];						    	
						    }
						    if($suprow['type'] == "Second Supervisor"){
                                                        $NameRow2 = mysqli_fetch_array(query_our_database("SELECT SupName FROM Supervisor WHERE SupID='".$suprow['SupID']."'"));
                                                        $SupName2 = $NameRow2['SupName'];
                                                    }
						}
						echo "<tr><td>" . $row['StuName'] . "</td>
									<td>" . $row['ProjectName'] . "</td>
									<td>" . $row['Progress'] . "</td>
									<td>" . $SupName1 . "</td>
									<td>" . $SupName2 . "</td>
									<td>" . $row['StuID'] . "</td>
									<td>" . $row['StuEMAIL'] . "</td>
									<td>" . $row['StuTel'] . "</td></tr>";	//$row['index'] the index here is a field name
					}

					echo "</table><br>"; //Close the table in HTML

					//Button for creating admin account (TODO: move to accounts page)
					echo "<h3>Actions</h3>
							<form action='create_admin_account.php' method='head'>
								<input type='submit' value='Create an admin account'>
							</form>";
					//Button for getting full sup history
					echo "<form action='supervisor_history.php' method='post'>
						<input type='text' name='stuHistID' value=''>
						<input type='submit' value='Get Full Supervision History For Student'>
						</form>";
				}
				if ($class == "Supervisor") {
					//List assigned students and their projects
					$result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE Project.SupID='".$_SESSION["ID"]."'");
				
					echo "<h3>Student overview</h3>
								<table>"; // start a table tag in the HTML
				
					// column names
					echo "<tr><th>Name</th>
								<th>Project</th>
								<th>Progress</th>
								<th>Student ID</th>
								<th>E-mail</th>
								<th>Telephone</th>
								</tr>";

					// rows of the database
					while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
						echo "<tr><td>" . $row['StuName'] . "</td>
									<td>" . $row['ProjectName'] . "</td>
									<td>" . $row['Progress'] . "</td>
									<td>" . $row['StuID'] . "</td>
									<td>" . $row['StuEMAIL'] . "</td>
									<td>" . $row['StuTel'] . "</td></tr>";	//$row['index'] the index here is a field name
					}

					echo "</table><br>"; //Close the table in HTML				
					
					//Button for creating a project (TODO: move to projects page)
					echo "<form action='project.php' method='head'>
									<input type='submit' value='Create a university project'>
								</form>";
				}
				if ($class == "Internship Contact") {
					//TODO here: add information on internship + participating students
				}
				if ($class == "Student") {
					//Show your project and supervisors
					
					$result = query_our_database("SELECT Does.ProjectName, Project.Description, Project.Progress, Project.Time, Project.Internship, Project.SupID, Project.IConID FROM Does LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE StuID='".$_SESSION["ID"]."'");
					$row = mysqli_fetch_array($result);
                    
                    
					
					echo "<h2>My project</h2>";
					if ($row['ProjectName'] != "") {
                        $type = $row["Internship"];//1 is internship
                        if($type == "1"){
                            $result2 = query_our_database("SELECT * FROM Internship_of WHERE ProjectName='".$row["ProjectName"]."'");
                            $rowinterinfo = mysqli_fetch_array($result2);
                            $result3 = query_our_database("SELECT * FROM Internship_Contact WHERE IConID='".$row["IConID"]."'");
                            $rowcontactinfo = mysqli_fetch_array($result3);
                        }
                        else{
                            $result2 = query_our_database("SELECT * FROM Supervisor WHERE SupID='".$row["SupID"]."'");
                            $rowcontactinfo = mysqli_fetch_array($result2);
                        }
						//echo "<h3>Title: " . $row['ProjectName']. "</h3>";
						//echo "<p style='margin-left: 5px'>". $row['Description'] . "<p>";
						echo "<table class=\"list\">
                            <tr>";
                        if($type == "1"){
                            echo "<th>Name and Description</th><th>Time</th><th>Project Owner Name</th><th>Owner Email</th><th>Owner Phone Number</th><th>Type</th><th>Company Name</th><th>City</th><th>Street</th><th>Nr</th><th>Travel</th><th>Pay</th>
                            </tr>
                            <tr>
                            <td width='40%'><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                            <td>" . $row['Time'] . "</td>
                            <td>" . $rowcontactinfo['IConName'] . "</td>
                            <td>" . $rowcontactinfo['IConEMAIL'] . "</td>
                            <td>" . $rowcontactinfo['IConTel'] . "</td>
                            <td>Internship</td>
                            <td>" . $rowinterinfo['CompanyName'] . "</td>
                            <td>" . $rowinterinfo['LocName'] . "</td>
                            <td>" . $rowinterinfo['Location'] . "</td>
                            <td>" . $rowinterinfo['StreetNr'] . "</td>";
                            if($rowinterinfo["Travel"] == 1)
                                echo "<td>Travel, </br>" . $rowinterinfo['Tnotes'] . "</td>";
                            else
                                echo "<td>No Travel Compensation</td>";
                            echo "<td>" . $rowinterinfo['Pay'] . "</td>
                            </tr>";
                        }
                        else{
                            echo "<th>Name and Description</th><th>Time</th><th>Project Owner Name</th><th>Owner Email</th><th>Owner Phone Number</th><th>Owner Topics</th><th>Type</th>
                            </tr>
                            <tr>
                            <td width='40%'><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                            <td>" . $row['Time'] . "</td>
                            <td>" . $rowcontactinfo['SupName'] . "</td>
                            <td>" . $rowcontactinfo['SupEMAIL'] . "</td>
                            <td>" . $rowcontactinfo['SupTel'] . "</td>
                            <td>" . $rowcontactinfo['Topics'] . "</td>
                            <td>University Project</td>
                            </tr>";
                        }
                        
                        echo "</table>";
                        echo "<h3>Progress:</h3>";
                        $prog = $row['Progress'];
                        $temp = htmlspecialchars($_SERVER["PHP_SELF"]);
                        echo "<form action=\"$temp\" method=\"post\">
                            <textarea name=\"progupdate\" rows=\"5\" cols=\"40\">$prog</textarea>
                            <input type=\"hidden\" name=\"progold\" value=\"$prog\">
                            <input type=\"submit\" name=\"progressupdate\" value=\"Update Your Progress\">
                        </form>";					
                        
					}
					else {
						echo "You currently have no project.<br>";
					}
					
                    /*    if ($row['Progress'] != "") 
							echo "<h3>Progress: " . $row['Progress'] . "</h3>";
						else*/
                   //$result = query_our_database("SELECT Supervises.type, Supervisor.SupName, Supervises.Accepted, Supervisor.SupEMAIL, Supervisor.SupTel FROM Supervises LEFT JOIN Supervisor ON Supervises.SupID=Supervisor.SupID WHERE StuID='".$_SESSION["ID"]."' ORDER BY type"); //TODO: when "active" is in database, filter on it
					$result = query_our_database("SELECT SupID, Accepted FROM Supervises WHERE StuID='".$_SESSION["ID"]."' AND type='First SuperVisor'");
					
					//first supervisor
                    $found = false;
                    while ($row = mysqli_fetch_array($result)){
                        if ($row['Accepted'] == "0" || $row['Accepted'] == "1"){
                            $result2 = query_our_database("SELECT * FROM Supervisor WHERE SupID='".$row['SupID']."'");
                            $rowcontact = mysqli_fetch_array($result2);
                            $found = true;
                            echo "<h3>First supervisor: " . $rowcontact['SupName'];
                            if($row['Accepted'] == "1")
                                echo "</h3>";
                            else
                                echo " (not confirmed yet)</h3>";
                            echo "<p style='margin-left: 5px'>E-mail: " . $rowcontact['SupEMAIL'] . "</p>";
                            echo "<p style='margin-left: 5px'>Telephone: " . $rowcontact['SupTel'] . "</p>";
                            break;
                        }
                    }
                    if (!$found)
                        echo "<h3>First supervisor: -</h3>";
					/*$row = mysqli_fetch_array($result); //go through the two (active) rows
					if ($row['type'] == "First Supervisor") {
						echo "<h3>First supervisor: " . $row['SupName'];
					}
					else {
						echo "<h3>First supervisor: -";
					}
					
					if ($row['Accepted'] == 1 || $row['Accepted'] == NULL) {
						echo "</h3>";
					}
					else {
						echo " (not confirmed yet)</h3>";
					}
					
					if ($row['SupName'] != NULL) {
						echo "<p style='margin-left: 5px'>E-mail: " . $row['SupEMAIL'] . "</p>";
						echo "<p style='margin-left: 5px'>Telephone: " . $row['SupTel'] . "</p>";
					}*/
					
                    $result = query_our_database("SELECT SupID, Accepted FROM Supervises WHERE StuID='".$_SESSION["ID"]."' AND type='Second SuperVisor'");
					
					//second supervisor
                    $found = false;
                    while ($row = mysqli_fetch_array($result)){
                        if ($row['Accepted'] == "0" || $row['Accepted'] == "1"){
                            $result2 = query_our_database("SELECT * FROM Supervisor WHERE SupID='".$row['SupID']."'");
                            $rowcontact = mysqli_fetch_array($result2);
                            $found = true;
                            echo "<h3>Second supervisor: " . $rowcontact['SupName'];
                            if($row['Accepted'] == "1")
                                echo "</h3>";
                            else
                                echo " (not confirmed yet)</h3>";
                            echo "<p style='margin-left: 5px'>E-mail: " . $rowcontact['SupEMAIL'] . "</p>";
                            echo "<p style='margin-left: 5px'>Telephone: " . $rowcontact['SupTel'] . "</p>";
                            break;
                        }
                    }
                    if (!$found)
                        echo "<h3>Second supervisor: -</h3>";
                    
					/*
					//second supervisor
					$row = mysqli_fetch_array($result); //go through the two (active) rows
					if ($row['type'] == "Second Supervisor") {
						echo "<h3>Second supervisor: " . $row['SupName'];
					}
					else {
						echo "<h3>Second supervisor: -";
					}
					
					if ($row['Accepted'] == 1 || $row['Accepted'] == NULL) {
						echo "</h3>";
					}
					else {
						echo " (not confirmed yet)</h3>";
					}
					
					if ($row['SupName'] != NULL) {
						echo "<p style='margin-left: 5px'>E-mail: " . $row['SupEMAIL'] . "</p>";
						echo "<p style='margin-left: 5px'>Telephone: " . $row['SupTel'] . "</p>";
					}
					*/
				}
			
			?>
			<form action="attempting_Logout.php" method="post">
				<input type="submit" value="Logout">
			</form>

		</div>

	</body>
</html> 
