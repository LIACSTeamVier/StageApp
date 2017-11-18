<?php
	session_start();
	date_default_timezone_set("Europe/Amsterdam");
	$configs = include("config.php");
	$_SESSION["needsDeleting"] = "false";
	$class = $_SESSION["class"];
	if (empty($_SESSION["ID"]))
		header("Location: index.php");
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		//$needsDeleting = false;

		if(!empty($_POST["supreq1"])){
			$requesttyp = "First Supervisor";
			$requestdoc = $_POST["supreq1"];
			request($con, $requesttyp, $requestdoc);
		}
		
		if(!empty($_POST["supreq2"])){
			$requesttyp = "Second Supervisor";
			$requestdoc = $_POST["supreq2"];
			request($con, $requesttyp, $requestdoc);			
		}
		
		
		$studentid = $_SESSION["ID"];
		$dateterm = date("Y-m-d: H:i:s");
		if(!empty($_POST["delreq1"])){
			mysqli_query($con, "UPDATE Supervises SET Accepted='-1', ActivationCode='NULL', DateTerminated='$dateterm' WHERE type='First Supervisor' AND StuID=$studentid AND Accepted='1'");//keep track when accepted relations are deleted
			$del1res = mysqli_affected_rows($con);
			//dont keep track of unaccepted deletion
			mysqli_query($con, "DELETE FROM Supervises WHERE type='First Supervisor' AND StuID=$studentid AND Accepted='0'");
			if(mysqli_affected_rows($con) ==0 && $del1res == 0)
				die("mysql error");
//			$needsDeleting = false;
		}
		if(!empty($_POST["delreq2"])){
			mysqli_query($con, "UPDATE Supervises SET Accepted='-1', ActivationCode='NULL', DateTerminated='$dateterm' WHERE type='Second Supervisor' AND StuID=$studentid AND Accepted='1'");//keep track when accepted relations are deleted
                        $del2res = mysqli_affected_rows($con);
                        //dont keep track of unaccepted deletion
                        mysqli_query($con, "DELETE FROM Supervises WHERE type='Second Supervisor' AND StuID=$studentid AND Accepted='0'");
                        if(mysqli_affected_rows($con) ==0 && $del2res == 0)
                                die("mysql error");

			//$del1res = mysqli_query($con, "DELETE FROM Supervises WHERE type='Second Supervisor' AND StuID=$studentid");
			//if(mysqli_affected_rows($con) ==0)
			//	die("mysql error");
//			$needsDeleting = false;
		}

			
		mysqli_close($con);
	}

	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	
	function request($con, $reqtyp, $reqdoc){
		//$reqtyp = test_input($_POST["RequestType"]);
		//$reqdoc = test_input($_POST["RequestDocID"]);
		//echo "$reqtyp   $reqdoc";
		$stmt = mysqli_prepare($con, "SELECT SupID, type FROM Supervises WHERE StuID='".$_SESSION["ID"]."' AND (Accepted='0' OR Accepted='1')");
		//mysqli_bind_param($stmt, 's',$reqtyp);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		mysqli_stmt_close($stmt);
		if($result){
			$numrow = mysqli_num_rows($result);//echo "<p>temp: numb of rows $numrow</p></br>";
			//$row = mysqli_fetch_array($result);
			if($numrow >= 2){ 
				$_SESSION["needsDeleting"] = true;//var_dump($needsDeleting);
				echo "<div class=\"main\"><p>You already made requests for both types of supervisor, delete one or more of them</p></br></div>";
			}
			else if($numrow == 0){//als er nog geen requests gemaakt zijn
				$stmt2 = mysqli_prepare($con, "SELECT RoleFirst, RoleSecond FROM Supervisor WHERE SupID=?");
				mysqli_bind_param($stmt2, 's', $reqdoc);
				mysqli_stmt_execute($stmt2);
				$result2 = mysqli_stmt_get_result($stmt2);
				mysqli_stmt_close($stmt2);
				$rowres2 = mysqli_fetch_array($result2);
				if(!empty($rowres2)){
					if ( ($rowres2["RoleFirst"] == "yes" && $reqtyp == "First Supervisor") ||
						 ($rowres2["RoleSecond"] == "yes" && $reqtyp == "Second Supervisor")){
							 $_SESSION["ReqDocID"] = $reqdoc;
							 $_SESSION["ReqType"] = $reqtyp;
							 $_SESSION["ReqStudentID"] = $_SESSION["ID"];
							 header("Location: make_activation.php");
							 die();
					 }
					else{
						echo "<div class=\"main\"><p>Requested supervisor isn't available for selected supervising type.</p></br></div>";
					}
				}
				else
				    echo "<div class=\"main\"><p>Supervisor doesn't exist</p></br></div>";
			}
			else if($numrow == 1){
				$row = mysqli_fetch_array($result);
				if($reqtyp == $row["type"]){
					$_SESSION["needsDeleting"] = $row["type"];//$needsDeleting = true;
					echo "<div class=\"main\"><p>You already made a request for this type of supervisor,
					</br> delete that request or request another type</p><br></div>";
				}
				else{
					$existingDocID = $row["SupID"];
					$result3 = mysqli_query($con, "SELECT Background FROM Supervisor WHERE SupID='$existingDocID'");
					$rowres3 = mysqli_fetch_array($result3);//var_dump($row);//rowres3);
					if(!empty($rowres3)){
						$existingBackground = $rowres3["Background"];
						$stmt4 = mysqli_prepare($con, "SELECT RoleFirst, RoleSecond, Background FROM Supervisor WHERE SupID=?");
						mysqli_bind_param($stmt4, 's', $reqdoc);
						mysqli_stmt_execute($stmt4);
						$result4 = mysqli_stmt_get_result($stmt4);
						mysqli_stmt_close($stmt4);
						$rowres4 = mysqli_fetch_array($result4);
						if(!empty($rowres4)){
							if ( ($rowres4["Background"] == $existingBackground)
									&& ($existingBackground != "BOTH") )
								echo "<div class=\"main\"><p>You need one supervisor with background in CS and one in Business.</br>
										You already have one with background in: $existingBackground</p></br></div>";
							else{
									if ( ($rowres4["RoleFirst"] == "yes" && $reqtyp == "First Supervisor") ||
										 ($rowres4["RoleSecond"] == "yes" && $reqtyp == "Second Supervisor")){
											 $_SESSION["ReqDocID"] = $reqdoc;
											 $_SESSION["ReqType"] = $reqtyp;
											 $_SESSION["ReqStudentID"] = $_SESSION["ID"];
											 header("Location: make_activation.php");
											 die();
									 }
									else{
										echo "<div class=\"main\"><p>Requested supervisor isn't available for selected supervising type.</p></br></div>";
									}
							}
						}
						else
							echo "<div class=\"main\"><p>Supervisor doesn't exist</p></br></div>";
					}
					else
						die("mysql error1");
					
				}
			}
		}
		else{
			die("mysql error2");
		}
		//mysqli_close($con);
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

    if ($_SESSION["needsDeleting"] == "true"){
		echo "<a><form action=\"$temp\" method=\"post\">
					<input type=\"submit\" name=\"delreq1\" value=\"Delete Request For The First Supervisor\">
					</form></a>
			  <a><form action=\"$temp\" method=\"post\">
                    <input type=\"submit\" name=\"delreq2\" value=\"Delete Request For The Second Supervisor\">
					</form></a>";	
    }
    else if($_SESSION["needsDeleting"] == "First Supervisor"){
                echo "<a><form action=\"$temp\" method=\"post\">
                                        <input type=\"submit\" name=\"delreq1\" value=\"Delete Request For The First Supervisor\">
                                        </form></a>";
    }
    else if($_SESSION["needsDeleting"] == "Second Supervisor"){
                echo "<a><form action=\"$temp\" method=\"post\">
                    <input type=\"submit\" name=\"delreq2\" value=\"Delete Request For The Second Supervisor\">
                                        </form></a>";
    }

    $sup_table = mysqli_query($con, "SELECT * FROM Supervisor") or die('Unable to run query:' . mysqli_error());
	if($class =="Student"){
		echo "These are the available supervisors.</br>";
		echo "You need one First Supervisor and one Second Supervisor.</br>";
		echo "You need one supervisor with a background in Computer Science(ICT), and one with background in Business(BUS).</br>";
		echo "Supervisors with background 'BOTH' can fill either role</br>";
	}
	echo "<table width='80%' id='1strequest_table'>"; // start a table tag in the HTML
	
	// column names
	echo "<tr><th onclick=\"sortTable(0)\">Supervisor Name</th>
			  <th onclick=\"sortTable(1)\">Supervisor Email</th>
			  <th onclick=\"sortTable(2)\">Supervisor Topics</th>
			  <th onclick=\"sortTable(3)\">First Supervisor</th>
			  <th onclick=\"sortTable(4)\">Second Supervisor</th>
			  <th onclick=\"sortTable(5)\">Background</th></tr>";
	
	// rows of the database
	while($row = mysqli_fetch_array($sup_table)){   //Creates a loop to loop through results
		echo "<tr><td>" . $row['SupName'] . "</td>
			  <td>" . $row['SupEMAIL'] . "</td>
			  <td>" . $row['Topics'] . "</td>";
			  //<td>" . $row['RoleFirst'] . "</td>
			  //<td>" . $row['RoleSecond'] . "</td>";
			 // <td>" . $row['Background']."</td>";
		if($class == "Student"){
			if ($row['RoleFirst'] == "yes"){
				echo "<td><form action=\"$temp\" method=\"post\">
						<input type=\"submit\" name=\"supreq1disp\" value=\"Request as First\">
						<input type=\"hidden\" name=\"supreq1\" value=\"".$row['SupID']."\">
						</form></td>";
			     	}
			else{
			    echo "<td>no</td>";
			}
			if ($row['RoleSecond'] == "yes"){
				echo "<td><form action=\"$temp\" method=\"post\">
						<input type=\"submit\" name=\"supreq2disp\" value=\"Request as Second\">
						<input type=\"hidden\" name=\"supreq2\" value=\"".$row['SupID']."\">
						</form></td>";
			}
			else{
			    echo "<td>no</td>";
			}
		}
		else{
			echo "<td>" . $row['RoleFirst'] . "</td>
                              <td>" . $row['RoleSecond'] . "</td>";
			//echo "</tr>";  //$row['index'] the index here is a field name
		}
		echo "<td>". $row['Background']."</td></tr>";
	}
	
	echo "</table>"; //Close the table in HTML
	echo "</br></br>";
	
	
    mysqli_close($con);
    
    ?>
</div>

</body>
</html>
