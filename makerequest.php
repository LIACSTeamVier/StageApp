<?php
session_start();
	//ID is the student id for the student that makes a request to a teacher
	if( (empty($_SESSION["ID"])) || ($_SESSION["class"] != "Student") ){
		header("Location: main_page.php");
		die();
	}
	$con = mysqli_connect("mysql.liacs.leidenuniv.nl", "csthesis", "-", "csthesis");
	// Check connection
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		die();
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["RequestType"]) && !empty($_POST["RequestDocID"])){
		$reqtyp = test_input($_POST["RequestType"]);
		$reqdoc = test_input($_POST["RequestDocID"]);
		//echo "$reqtyp   $reqdoc";die();
		$stmt = mysqli_prepare($con, "SELECT DocentID, type FROM Begeleid WHERE StudentID='".$_SESSION["ID"]."'");
		//mysqli_bind_param($stmt, 's',$reqtyp);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		mysqli_stmt_close($stmt);
		if($result){
			$numrow = mysqli_num_rows($result);echo "<p>temp: numb of rows $numrow</p></br>";
			//$row = mysqli_fetch_array($result);
			if($numrow >= 2){
				//echo "<p>You already made a request for this type of supervisor.
					//  </br>Choose a different type or delete that request.</p>"
				echo "<p>You already made requests for both types of supervisor, delete one or more of them</p></br>";
			}
			else if($numrow == 0){//als er nog geen requests gemaakt zijn
				$stmt2 = mysqli_prepare($con, "SELECT RoleFirst, RoleSecond FROM Begeleider	WHERE DocentID=?");
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
						echo "<p>Requested supervisor isn't available for selected supervising type.</p></br>";
					}
				}
				else
					echo "<p>Supervisor doesn't exist</p></br>";
			}
			else if($numrow == 1){
				$row = mysqli_fetch_array($result);
				if($reqtyp == $row["type"]){
					echo "<p>You already made a request for this type of supervisor,
					</br> delete that request or request another type</p><br>";
				}
				else{
					$existingDocID = $row["DocentID"];
					$result3 = mysqli_query($con, "SELECT Background FROM Begeleider WHERE DocentID='$existingDocID'");
					$rowres3 = mysqli_fetch_array($result3);
					if(!empty($rowres3)){
						$existingBackground = $rowres3["Background"];
						$stmt4 = mysqli_prepare($con, "SELECT RoleFirst, RoleSecond, Background FROM Begeleider WHERE DocentID=?");
						mysqli_bind_param($stmt4, 's', $reqdoc);
						mysqli_stmt_execute($stmt4);
						$result4 = mysqli_stmt_get_result($stmt4);
						mysqli_stmt_close($stmt4);
						$rowres4 = mysqli_fetch_array($result4);
						if(!empty($rowres4)){
							if ( ($rowres4["Background"] == $existingBackground)
									&& ($existingBackground != "BOTH") )
								echo "<p>You need one supervisor with background in CS and one in Business.</br>
										You already have one with background in: $existingBackground</p></br>";
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
										echo "<p>Requested supervisor isn't available for selected supervising type.</p></br>";
									}
							}
						}
						else
							echo "<p>Supervisor doesn't exist</p></br>";
					}
					else
						die("mysql error");
				}
			}
		}
		else{
			die("mysql error");
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
<!DOCTYPE HTML>
<html>
	<head>
        <meta charset="utf-8" /> 
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
	<body>
		<div class="sidepane">
            <a href="main_page.php">Overview</a>
            <a>standardise this</a>
        </div>
        <div class="main">
            <p>
            Temporary way of requesting supervisors.
            </p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                First Professor/Supervisor id: <input type="text" name="RequestDocID">                
                <input type="hidden" value="Request First Supervisor">
                <input type="submit" name="RequestType" value="First Supervisor">
            </form>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                Second Professor/Supervisor id: <input type="text" name="RequestDocID">                
                <input type="hidden" value="Request Second Supervisor">
                <input type="submit" name="RequestType" value="Second Supervisor">
            </form>
        </div>
	</body>
</html>
