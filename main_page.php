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
		<script src="sortTable.js"></script>
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
                echo "<h1>LIACS Student Project Manager</h1>";
				//echo "<h1>Welcome " . "$username" ."</h1>";
				//echo "You are logged in as " . "$class" . "." ."<p>"; //TODO temp, remove line.

				// After sending an e-mail
				if (isset($_SESSION["regErr"])) {
					echo $_SESSION["regErr"];
				}
				unset($_SESSION["regErr"]);

				if ($class == "Admin") {
					//List all students with their projects
					$result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress, Supervisor.SupName FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName LEFT JOIN Supervisor ON Project.SupID=Supervisor.SupID");
					 
					echo "<h3>Student overview</h3>
							<table class=\"list\" id='admin_table'>"; // start a table tag in the HTML
				
					// column names
					echo "<tr><th onclick=\"sortTable(0, 'admin_table')\">Name</th>
								<th onclick=\"sortTable(1, 'admin_table')\">Project</th>
								<th onclick=\"sortTable(2, 'admin_table')\">Progress</th>
								<th onclick=\"sortTable(3, 'admin_table')\">First Supervisor</th>
								<th onclick=\"sortTable(4, 'admin_table')\">Second Supervisor</th>
								<th onclick=\"sortTable(5, 'admin_table')\">Student ID</th>
								<th onclick=\"sortTable(6, 'admin_table')\">E-mail</th>
								<th onclick=\"sortTable(7, 'admin_table')\">Telephone</th>
								</tr>";

					// rows of the database
	            while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
	                $SupName1 ="";
	                $SupName2 ="";
	                $supervisorresult = query_our_database("SELECT * FROM Supervises WHERE StuID='".$row['StuID']."' AND (Accepted='1' OR Accepted='0')");
                        while($suprow = mysqli_fetch_array($supervisorresult)){
                            if($suprow['type'] == "First Supervisor"){
                                $NameRow1 = mysqli_fetch_array(query_our_database("SELECT SupName FROM Supervisor WHERE SupID='".$suprow['SupID']."'"));
                                if($suprow['Accepted'] == '1')
                                   $SupName1 = $NameRow1['SupName'];
                                else
                                   $SupName1 = $NameRow1['SupName'] . " (not accepted yet)";
                            }
                            if($suprow['type'] == "Second Supervisor"){
                                $NameRow2 = mysqli_fetch_array(query_our_database("SELECT SupName FROM Supervisor WHERE SupID='".$suprow['SupID']."'"));
                                if($suprow['Accepted'] == '1')
                                    $SupName2 = $NameRow2['SupName'];
                                else
                                    $SupName2 = $NameRow2['SupName'] . " (not accepted yet)";
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
				}
				if ($class == "Supervisor") {
					//List assigned students and their projects
					//$result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE Project.SupID='".$_SESSION["ID"]."'");
                    $result = query_our_database("SELECT Supervises.StuID, StuName, StuEMAIL, StuTel, type, Supervises.SupID, Supervises.Accepted as SupAccepted, Does.ProjectName, Does.Accepted as ProjectAccepted, Description, Progress FROM Student LEFT JOIN Supervises ON Student.StuID=Supervises.StuID LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE Supervises.SupID='".$_SESSION["ID"]."' AND Supervises.Accepted='1' ORDER BY StuID");
                    if(mysqli_num_rows($result) > 0){
                        echo "<h3>Student overview</h3>
                                    <table class=\"list\" id='sup_table'>"; // start a table tag in the HTML
                    
                        // column names
                        echo "<tr><th onclick=\"sortTable(0, 'sup_table')\">Name</th>
                                <th onclick=\"sortTable(1, 'sup_table')\">First Supervisor</th>
                                <th onclick=\"sortTable(2, 'sup_table')\">Second Supervisor</th>
                                <th onclick=\"sortTable(3, 'sup_table')\">Project Name and Description</th>
                                <th onclick=\"sortTable(4, 'sup_table')\">Project Accepted</th>
                                <th onclick=\"sortTable(5, 'sup_table')\">Progress</th>
                                <th onclick=\"sortTable(6, 'sup_table')\">Student ID</th>
                                <th onclick=\"sortTable(7, 'sup_table')\">E-mail</th>
                                <th onclick=\"sortTable(8, 'sup_table')\">Phone Number</th>
                                </tr>";

                        // rows of the database
                        $prevStuID = "";
                        while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
                            $SupName1 ="";
                            $SupName2 ="";
                            if (!($prevStuID == $row['StuID'])){//prevent duplicate rows
                                $supsresult = query_our_database("SELECT SupName, Supervises.SupID, Accepted, type FROM Supervises LEFT JOIN Supervisor ON Supervises.SupID=Supervisor.SupID WHERE StuID='".$row['StuID']."' AND (Accepted='0' OR Accepted='1')");
                                while($suprow = mysqli_fetch_array($supsresult)){
                                    if($suprow['type'] == "First Supervisor"){
                                        if($suprow['SupID'] == $_SESSION["ID"])
                                            $SupName1 = "You";
                                        else{
                                            if($suprow['Accepted']=='1')
                                                $SupName1 = $suprow['SupName'];
                                            else
                                                $SupName1 = $suprow['SupName']." (not accepted yet)";
                                        }
                                    }
                                    if($suprow['type'] == "Second Supervisor"){
                                        if($suprow['SupID'] == $_SESSION["ID"])
                                            $SupName2 = "You";
                                        else{
                                            if($suprow['Accepted']=='1')
                                                $SupName2 = $suprow['SupName'];
                                            else
                                                $SupName2 = $suprow['SupName']." (not accepted yet)";
                                        }
                                    }
                                }
                                $pAccepted = "";
                                $prog = "";
                                if ($row['ProjectAccepted'] == '1'){
                                    $pAccepted = "Yes";
                                    $prog = $row['Progress'];
                                }
                                else if ($row['ProjectAccepted'] == '0'){
                                    $pAccepted = "No";
                                }
                                echo "<tr><td>" . $row['StuName'] . "</td>
                                            <td>" . $SupName1 . "</td>
                                            <td>" . $SupName2 . "</td>
                                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                                            <td>$pAccepted</td>
                                            <td>$prog</td>
                                            <td>" . $row['StuID'] . "</td>
                                            <td>" . $row['StuEMAIL'] . "</td>
                                            <td>" . $row['StuTel'] . "</td></tr>";	//$row['index'] the index here is a field name
                            }
                            $prevStuID = $row['StuID'];
                        }

                        echo "</table><br>"; //Close the table in HTML	
                    }
                    $result = query_our_database("SELECT Project.ProjectName, Description, Does.StuID, StuName, StuEMAIL, StuTel, Progress FROM Project LEFT JOIN Does ON Project.ProjectName=Does.ProjectName LEFT JOIN Student ON Does.StuID=Student.StuID WHERE Project.SupID='".$_SESSION["ID"]."'");
                    
                    if(mysqli_num_rows($result) > 0){
                        echo "<h3>Project Overview</h3>
                                    <table class=\"list\" id='sup_table2'>"; // start a table tag in the HTML
                        
                        // column names
                        echo "<tr><th onclick=\"sortTable(0, 'sup_table2')\">Name and Description</th>
                                <th onclick=\"sortTable(1, 'sup_table2')\">Student Name</th>
                                <th onclick=\"sortTable(2, 'sup_table2')\">Student E-mail</th>
                                <th onclick=\"sortTable(3, 'sup_table2')\">Student Phone Number</th>
                                <th onclick=\"sortTable(4, 'sup_table2')\">Progress</th>
                                </tr>";
                        
                        
                        // rows of the database
                        while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
                            echo "<tr><td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                                        <td>" . $row['StuName'] . "</td>
                                        <td>" . $row['StuEMAIL'] . "</td>
                                        <td>" . $row['StuTel'] . "</td>
                                        <td>" . $row['Progress'] . "</td></tr>";	//$row['index'] the index here is a field name
                        }

                        
                        echo "</table><br>"; //Close the table in HTML	
                    }
				}
                if ($class == "Internship Contact") {
					$result = query_our_database("SELECT Project.ProjectName, Description, Does.StuID, StuName, StuEMAIL, StuTel, Progress, Pay, LocName, Location, StreetNr, Travel, Tnotes FROM Project LEFT JOIN Does ON Project.ProjectName=Does.ProjectName LEFT JOIN Student ON Does.StuID=Student.StuID LEFT JOIN Internship_of ON Project.ProjectName=Internship_of.ProjectName WHERE Project.IConID='".$_SESSION["ID"]."'");
                    if(mysqli_num_rows($result) > 0){
                        echo "<h3>Internship Overview</h3>
                                    <table class=\"list\" id='icon_table'>"; // start a table tag in the HTML
                        
                        // column names
                        echo "<tr><th onclick=\"sortTable(0, 'icon_table')\">Name and Description</th>
                                <th onclick=\"sortTable(1, 'icon_table')\">Student Name</th>
                                <th onclick=\"sortTable(2, 'icon_table')\">Student E-mail</th>
                                <th onclick=\"sortTable(3, 'icon_table')\">Student Phone Number</th>
                                <th onclick=\"sortTable(4, 'icon_table')\">Progress</th>
                                <th onclick=\"sortTable(5, 'icon_table')\">Location</th>
                                <th onclick=\"sortTable(6, 'icon_table')\">Pay</th>
                                <th onclick=\"sortTable(7, 'icon_table')\">Travel</th>
                                </tr>";
                        
                        
                        // rows of the database
                        while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
                            echo "<tr><td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                                        <td>" . $row['StuName'] . "</td>
                                        <td>" . $row['StuEMAIL'] . "</td>
                                        <td>" . $row['StuTel'] . "</td>
                                        <td>" . $row['Progress'] . "</td>
                                        <td>" . $row['Location']. " " . $row['StreetNr'] . ", " . $row['LocName'] . "</td>
                                        <td>" . $row['Pay'] . "</td>";
                                        if($row["Travel"] == 1)
                                            echo "<td>Travel, </br>" . $row['Tnotes'] . "</td>";
                                        else
                                            echo "<td>No Travel Compensation</td>";
                                        echo "</tr>";	//$row['index'] the index here is a field name
                        }

                        
                        echo "</table><br>"; //Close the table in HTML	
                    }
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
                        
                        echo "<table class=\"list\" id='student_table'>
                            <tr>";
                        if($type == "1"){
                            echo "<th onclick=\"sortTable(0, 'student_table')\">Name and Description</th>
                                  <th onclick=\"sortTable(1, 'student_table')\">Time</th>
                                  <th onclick=\"sortTable(2, 'student_table')\">Project Owner Name</th>
                                  <th onclick=\"sortTable(3, 'student_table')\">Owner Email</th>
                                  <th onclick=\"sortTable(4, 'student_table')\">Owner Phone Number</th>
                                  <th onclick=\"sortTable(5, 'student_table')\">Type</th>
                                  <th onclick=\"sortTable(6, 'student_table')\">Company Name</th>
                                  <th onclick=\"sortTable(7, 'student_table')\">City</th>
                                  <th onclick=\"sortTable(8, 'student_table')\">Street</th>
                                  <th onclick=\"sortTable(9, 'student_table')\">Nr</th>
                                  <th onclick=\"sortTable(10, 'student_table')\">Travel</th>
                                  <th onclick=\"sortTable(11, 'student_table')\">Pay</th>
                            </tr>
                            <tr>
                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
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
                            echo "<th onclick=\"sortTable(0, 'student_table')\">Name and Description</th>
                                  <th onclick=\"sortTable(1, 'student_table')\">Time</th>
                                  <th onclick=\"sortTable(2, 'student_table')\">Project Owner Name</th>
                                  <th onclick=\"sortTable(3, 'student_table')\">Owner Email</th>
                                  <th onclick=\"sortTable(4, 'student_table')\">Owner Phone Number</th>
                                  <th onclick=\"sortTable(5, 'student_table')\">Owner Topics</th>
                                  <th onclick=\"sortTable(6, 'student_table')\">Type</th>
                            </tr>
                            <tr>
                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
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
                    
				}
			
			?>
		</div>

	</body>
</html> 


