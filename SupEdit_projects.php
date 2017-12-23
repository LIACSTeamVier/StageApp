<?php
session_start();
require_once "general_functions.php";
require_once "sidebar_selector.php";

    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }   
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["progressupdate"])){
//            if (!empty($_POST["progupdate"])){
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
  //          }          
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
				if( ($_SESSION["class"] != "Supervisor")  ){
					header("Location: main_page.php");
					die();
				}
				$username = $_SESSION["username"];
				$class = $_SESSION["class"];
				
				
					//List assigned students and their projects
					$result = query_our_database("SELECT Project.ProjectName, Project.Description, Project.Progress, Project.Time, Project.Studentqualities, Project.Topic, Project.Internship FROM Project WHERE Project.SupID='".$_SESSION["ID"]."'");
				
					echo "<h3>Your Projects</h3>
								<table class=\"list\" width='100%' id='project_table'>"; // start a table tag in the HTML
				
					// column names
					echo "<tr><th>Name</th>
								<th>Description</th>
								<th>Time</th>
								<th>Qualities</th>
								<th>Topic</th>
								</tr>";

					// rows of the database
					while($row = mysqli_fetch_array($result)){	 //Creates a loop to loop through results
						echo "<tr><td>
										" . $row['ProjectName'] . 
										"<br>
										</td>
									<td>
										<br>
										<form method=\"post\" action=\"ChangeDesc.php?prjct=" . $row['ProjectName'] . "\">
										<input type=\"text\" name = \"name\" value=\"" . $row['Description'] . "\" ><br>
										<input type=\"submit\" value=\"edit\">
										</form>
										</td>
									<td>
										<br>
										<form method=\"post\" action=\"ChangeTime.php?prjct=" . $row['ProjectName'] . "\">
										<input type=\"text\" name = \"time\" value=\"" . $row['Time'] . "\" ><br>
										<input type=\"submit\" value=\"edit\">
										</form>
										</td>
									<td>
										<br>
										<form method=\"post\" action=\"ChangeQual.php?prjct=" . $row['ProjectName'] . "\">
										<input type=\"text\" name = \"qual\" value=\"" . $row['Studentqualities'] . "\" ><br>
										<input type=\"submit\" value=\"edit\">
										</form>
										</td>
									<td>
										<br>
										<form method=\"post\" action=\"ChangeTopic.php?prjct=" . $row['ProjectName'] . "\">
										<input type=\"text\" name = \"topic\" value=\"" . $row['Topic'] . "\" ><br>
										<input type=\"submit\" value=\"edit\">
										</form>
										</td>
									<td></tr>";	//$row['index'] the index here is a field name
					}

					echo "</table><br>"; //Close the table in HTML				
					
					//Button for creating a project (TODO: move to projects page)
					echo "<form action='project.php' method='head'>
									<input type='submit' value='Create a university project'>
								</form>";
				
				
			
			
			?>
			<form action="logout.php" method="post">
				<input type="submit" value="Logout">
			</form>

		</div>

	</body>
