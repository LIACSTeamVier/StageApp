<?php
session_start();
$id = $_SESSION['ID'];
$project = $_GET['prjct'];
?>
 
 <!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="sortTable.js"></script>
<title>Request is being processed - LIACS Student Project Manager</title>
</head>
<body>
<h2>Your request is being processed, please wait...</h2>
<?php

	$configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);

	$sql = "DELETE FROM Does WHERE StuID= '" . $id . "'";
	if (!$con->query($sql)){
		echo "ERROR: connection time-out";
	}else {
			$stmt = $con->prepare("INSERT INTO Does (StuID, ProjectName, DateRequested) VALUES (?,?,?)");
			$stmt->bind_param("sss", $id, $project, date('Y-m-d:h:i:s'));
			$stmt->execute();
			
			$sql = "SELECT SupEMAIL, SupID FROM Supervisor AS I, Supervises AS S WHERE I.SupID = S.SupID";
			$results = $con->query($sql);
			if(!$result){
				echo "ERROR: you have no supervisor <br>\n ";
			}else{
			$to = $result->fetch_assoc();	
			mail($to["SupEMAIL"], "Subscription Request", "Student " .$id. "has requested access to the project" .$prjct. ".");
			header( 'Location: http://csthesis.liacs.leidenuniv.nl/main_page.php' );
			}
	}
	
	echo "<a href= 'main_page.php'>Return home</a>";

?>
</body>
</html>
