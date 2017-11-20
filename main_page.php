<?php
include 'general_functions.php';

session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
	<head>
		<meta charset="utf-8" /> 

		<meta name="Description" content= "Home" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Home</title>
	</head>
	<body>

		<div class="sidepane">
			<a href="main_page.php">Overview</a>
			<?php
				if($_SESSION["class"] == "Admin" || $_SESSION["class"] == "Supervisor")
					echo "<a href=\"request_list.php\">Student Supervison Requests</a>"
			?>
			<a href="project_list.php">Projects</a>
			<a href="#">Contact</a>
			<a href="database_table.php">Database</a>
			<a href="#">Help</a></a>
		</div>

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
				if (isset($_SESSION["creatingAccount"])) {
					if (isset($_SESSION["accCreateErr"])) {
						echo "ERROR: " . $_SESSION["accCreateErr"] . "</br>Could not create account. No e-mail was sent.</br>";
					}
					else {
						echo "Account created successfully.</br>";
						if (isset($_SESSION["emailErr"]))
							echo "ERROR: e-mail could not be delivered. Please manually inform the recipient. Their username should be their e-mail address. Their password can be found in the 'name' table."; //TODO vervang 'name'
					}
				}
				unset($_SESSION["emailErr"]);
				unset($_SESSION["accCreateErr"]);
				unset($_SESSION["creatingAccount"]);

				if ($class == "Admin") {
					//List all students with their projects
					$result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress, Supervisor.SupName FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName LEFT JOIN Supervisor ON Project.SupID=Supervisor.SupID");
					 
					echo "<h3>Student overview</h3>
							<table>"; // start a table tag in the HTML
				
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
					echo "<form action='suphistory.php' method='post'>
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
					//List assigned students and their projects
					//Button for creating an internship (TODO: move to projects page?)
					echo "<form action='stage.php' method='head'>
								<input type='submit' value='Create an internship'>
							</form>";
				}
				if ($class == "Student") {
					//Show your project and supervisors
					
					$result = query_our_database("SELECT Does.ProjectName, Project.Description, Project.Progress FROM Does LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE StuID='".$_SESSION["ID"]."'");
					$row = mysqli_fetch_array($result);
					
					echo "<h2>My project</h2>";
					if ($row['ProjectName'] != "") {
						echo "<h3>Title: " . $row['ProjectName']. "</h3>";
						echo "<p style='margin-left: 5px'>". $row['Description'] . "<p>";
						
						if ($row['Progress'] != "") 
							echo "<h3>Progress: " . $row['Progress'] . "</h3>";
						else
							echo "<h3>Progress: -</h3>";
					}
					else {
						echo "You currently have no project.<br>";
					}
					
					
					$result = query_our_database("SELECT Supervises.type, Supervisor.SupName, Supervises.Accepted, Supervisor.SupEMAIL, Supervisor.SupTel FROM Supervises LEFT JOIN Supervisor ON Supervises.SupID=Supervisor.SupID WHERE StuID='".$_SESSION["ID"]."' ORDER BY type"); //TODO: when "active" is in database, filter on it
					
					
					//first supervisor
					$row = mysqli_fetch_array($result); //go through the two (active) rows
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
					}
					
					
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
					
				//}
				
				echo "<br>";
				
				//Button for requesting a supervisor (TODO: move to supervisor page)
				echo "<form action='suplist.php' method='post'>
							<input type='submit' value='Make a request for a supervisor'>
						</form>";
				}
			
			?>
			<form action="attempting_Logout.php" method="post">
				<input type="submit" value="Logout">
			</form>

		</div>

	</body>
</html>
