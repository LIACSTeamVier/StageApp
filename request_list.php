<?php
	session_start();
	if ($_SERVER["REQUEST_METHOD"] == "GET"){
		$con = mysqli_connect("mysql.liacs.leidenuniv.nl", "csthesis", "-", "csthesis");
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if(!(empty($_GET["code"]))){
			$randomstr = test_input($_GET["code"]);
			$stmt = mysqli_prepare($con, "UPDATE Begeleid SET Accepted='1' WHERE ActivationCode=?");
			mysqli_stmt_bind_param($stmt,'s', $randomstr);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			
			if(mysqli_affected_rows($con)>0){
				mysqli_stmt_close($stmt);
				$stmt2 = mysqli_prepare($con, "UPDATE Begeleid SET ActivationCode='' WHERE ActivationCode=?");
				mysqli_stmt_bind_param($stmt2,'s', $randomstr);
				mysqli_stmt_execute($stmt2);
				$result2 = mysqli_stmt_get_result($stmt2);
				mysqli_stmt_close($stmt2);
				echo "<script>alert(\"Successfully accepted being a supervisor for the student!\");
						location.href='main_page.php';
						exit;
						</script>";
				die();

			}
			header("Location: main_page.php");
			die("Wrong Code");
		}
		
	}

	if (($_SESSION["class"] != "Admin") && ($_SESSION["class"] != "Supervisor")){
		//redirect to main page
		header("Location: main_page.php");
		die();
	}
		$docid = $_SESSION["ID"];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$con = mysqli_connect("mysql.liacs.leidenuniv.nl", "csthesis", "-", "csthesis");
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if(!(empty($_POST["FirstStudent"]))){
			mysqli_query($con, "UPDATE Begeleid SET Accepted='1' WHERE type='First Supervisor' AND DocentID='$docid' AND StudentID='".$_POST["FirstStudent"]."'")
			or die('Unable to run query:' . mysqli_error());
		}
		if(!(empty($_POST["SecondStudent"]))){
			mysqli_query($con, "UPDATE Begeleid SET Accepted='1' WHERE type='Second Supervisor' AND DocentID='$docid' AND StudentID='".$_POST["SecondStudent"]."'")
			or die('Unable to run query:' . mysqli_error());
		}
				
		mysqli_close($con);
	}


	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
    
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="sortTable.js"></script>
</head>
<body>

<div class="sidepane">
  <a href="main_page.php">Overview</a>
  <a href="request_list.php">Student Supervision Requests</a>
  <a href="project_list.php">Projects</a>
  <a href="#">Contact</a>
  <a href="database_table.php">Database</a>
  <a href="#">Help</a></a>
</div>

<div class="main">
  <?php
    $host = "mysql.liacs.leidenuniv.nl";
    $username = "csthesis";
    $password = "-";
    $dbname = "csthesis";
    $con = mysqli_connect($host, $username, $password, $dbname);
    
    // check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    
    $temp = htmlspecialchars($_SERVER["PHP_SELF"]);
    
    $RoleAllowRes = mysqli_query($con, "SELECT DocentID, RoleFirst, RoleSecond FROM Begeleider WHERE DocentID='$docid'")or die('Unable to run query:' . mysqli_error());
    $RoleAllow = mysqli_fetch_array($RoleAllowRes);
    if($RoleAllow['RoleFirst'] == "yes"){
		$project_table = mysqli_query($con, "SELECT * FROM Begeleid b WHERE b.DocentID='$docid' AND b.type = 'First Supervisor' AND b.Accepted='0'") or die('Unable to run query:' . mysqli_error());
		echo "These students want you as FIRST SUPERVISOR";
		echo "<table width='40%' id='1strequest_table'>"; // start a table tag in the HTML
		// column names
		echo "<tr><th onclick=\"sortTable(0)\">Student Id</th>
				  <th onclick=\"sortTable(1)\">Student Name</th></tr>";
		
		// rows of the database
		while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
			$student_name_get = mysqli_query($con, "SELECT StudentNaam FROM Afstudeerder WHERE StudentID='".$row['StudentID']."'")or die('Unable to run query:' . mysqli_error());
			$student_name = mysqli_fetch_array($student_name_get);
			echo "<tr><form action=\"$temp\" method=\"post\">
				  <td>" . $row['StudentID'] . "</td>
				  <td>".$student_name['StudentNaam']."</td>
				  <td><input type=\"submit\" name=\"FirstStudentDisp\" value=\"Accept This Student\">
					  <input type=\"hidden\" name=\"FirstStudent\" value=\"".$row['StudentID']."\" /></td>
				  </form></tr>";  //$row['index'] the index here is a field name
		}
		
		echo "</table>"; //Close the table in HTML
		echo "</br></br>";
	}
	if($RoleAllow['RoleSecond'] == "yes"){
		$project_table = mysqli_query($con, "SELECT * FROM Begeleid b WHERE b.DocentID='$docid' AND b.type = 'Second Supervisor' AND b.Accepted='0'") or die('Unable to run query:' . mysqli_error());
		echo "These students want you as SECOND SUPERVISOR";
		echo "<table width='40%' id='2ndrequest_table'>"; // start a table tag in the HTML
		// column names
		echo "<tr><th onclick=\"sortTable(0)\">Student Id</th>
				  <th onclick=\"sortTable(1)\">Student Name</th></tr>";
		
		// rows of the database
		while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
			$student_name_get = mysqli_query($con, "SELECT StudentNaam FROM Afstudeerder WHERE StudentID='".$row['StudentID']."'")or die('Unable to run query:' . mysqli_error());
			$student_name = mysqli_fetch_array($student_name_get);
			echo "<tr><form action=\"$temp\" method=\"post\">
				  <td>" . $row['StudentID'] . "</td>
				  <td>".$student_name['StudentNaam']."</td>
				  <td><input type=\"submit\" name=\"SecondStudentDisp\" value=\"Accept This Student\">
					  <input type=\"hidden\" name=\"SecondStudent\" value=\"".$row['StudentID']."\" /></td>
				  </form></tr>";  //$row['index'] the index here is a field name
			
		}
		
		echo "</table>"; //Close the table in HTML
	}
	
    mysqli_close($con);
  
  
    
    ?>
</div>

</body>
</html>

