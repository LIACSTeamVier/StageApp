<?php
session_start();
include 'general_functions.php';
include 'sidebar_selector.php';
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Projects and Internships" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Projects and Internships</title>
        <script src="sortTable.js"></script>
 <!--       <style>
        table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
		th, td {
			padding: 5px;
		}
		th {
			text-align: left;
		}-->
        </style>
    </head>
    <body>
<!--
        <div class="sidepane">
            <a href="main_page.php">Overview</a>
            <a href="project_list.php">Projects</a>
            <a href="#">Contact</a>
            <a href="database_table.php">Database</a>
            <a href="#">Help</a></a>
        </div>
-->
        <div class="main">
            <?php
                $configs = include("config.php");
                $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
                
                // check connection
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                }
                
                $project_table = mysqli_query($con, "SELECT * FROM Project") or die('Unable to run query:' . mysqli_error());
                echo "<table class=\"list\" id='project_table'>"; // start a table tag in the HTML
                
                // column names
                echo "<tr><th onclick=\"sortTable(0, 'project_table')\">Name and description</th>
                          <th onclick=\"sortTable(1, 'project_table')\">Topic</th>
                          <th onclick=\"sortTable(2, 'project_table')\">Time</th>
                          <th onclick=\"sortTable(3, 'project_table')\">Progress</th>
                          <th onclick=\"sortTable(4, 'project_table')\">Student type</th>
                          <th onclick=\"sortTable(5, 'project_table')\">Internship</th>
                          <th onclick=\"sortTable(6, 'project_table')\">Teacher</th>
                          <th onclick=\"sortTable(7, 'project_table')\">Company</th></tr>";
                          
                // rows of the database
                while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
                    $teacher_name_get = mysqli_query($con, "SELECT SupName FROM Supervisor WHERE SupID='".$row['SupID']."'")or die('Unable to run query:' . mysqli_error());
                    $teacher_name = mysqli_fetch_array($teacher_name_get);
                    echo "<tr><td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                          <td>" . $row['Topic'] . "</td>
                          <td>" . $row['Time'] . "</td>
                          <td>" . $row['Progress'] . "</td>
                          <td>" . $row['Studentqualities'] . "</td>";  
				 
				  if ($row['Internship'] == 1)
					  echo "<td>Yes</td>";
				  else
					  echo "<td>No</td>";
                   
                    echo "<td>" . $teacher_name['SupName'] . "</td>
                          <td>" . $row['CompanyName'] . "</td>";
                          
					if ($_SESSION["class"] == "Student"){
					echo "<td> 
					
					<form method=\"POST\" action=\"subscription.php?prjct=" . $row['ProjectName'] . "\">
					
						<input type=\"submit\" name=" . $row['ProjectName'] . " value=\"Subscribe\" />
						
					</form>
					
					 </td>";
					}
					
					
                    echo      "</tr>";  //$row['index'] the index here is a field name
                }
        
                echo "</table>"; //Close the table in HTML
        
                mysqli_close($con);
      
      
        
            ?>
        </div>
        
        <?php
        //$all_projects = mysqli_query($con, "SELECT ProjectName, Topic FROM Project") or die('Unable to run query:' . mysqli_error());
		//	while($row2 = mysqli_fetch_array($all_projects)){
		//		if(isset($_POST['' .$row2['ProjectName'] . ''])){
		//			alert("test");
		//	} 
        ?>
        

    </body>
</html>
