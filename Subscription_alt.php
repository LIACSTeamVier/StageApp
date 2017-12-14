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
<title>Unsubscribe from project - LIACS Student Project Manager</title>
</head>
<body>
<h2>Are you certain you want to unsubscribe from your current project?</h2>

<form method="POST" action= <?php echo $_SERVER['PHP_SELF']; ?> >
							
	<input type="submit" name="yes" value="yes" />
	<input type="hidden" name="chosen" value= "<?php echo $project; ?>">
	
</form>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
							
	<input type="submit" name="no" value="no" />
	
</form>


<?php
if(isset($_POST['no']))
{
	if($_POST['name'] = "no"){
		header( 'Location: http://csthesis.liacs.leidenuniv.nl/main_page.php' );
	}
}

if(isset($_POST['yes']))
{
	if($_POST['name'] = "yes"){
		$configs = include("config.php");
		$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		$project = $_POST['chosen'];
		if(!$con){
			echo "error, no connection";
		}

		$sql = "DELETE FROM Does WHERE StuID= '" . $id . "'";
		if (!$con->query($sql)){
			echo "ERROR: connection time-out";
		}else {
				$stmt = $con->prepare("INSERT INTO Does (StuID, ProjectName, DateRequested) VALUES (?,?,?)");
				$stmt->bind_param("sss", $id, $project, date('Y-m-d:h:i:s'));
				$stmt->execute();
				
				
				$sql2 = "SELECT * FROM Supervisor INNER JOIN Supervises ON Supervisor.SupID = Supervises.SupID WHERE Supervises.StuID = '" . $_SESSION['ID'] . "' AND Supervises.Accepted = '1'";
				
				$result = $con->query($sql2);
				if(!$result){
					echo "ERROR: Connection time-out";
				}else{
				$to = $result->fetch_assoc();	
				echo $to["SupEMAIL"];
				mail($to["SupEMAIL"], "Subscription Request", "Student " .$id. " has requested access to the project " .$project. ".");
				header( 'Location: http://csthesis.liacs.leidenuniv.nl/main_page.php' );
				}
				
		}
		
		echo "<a href= 'main_page.php'>Return Home</a>";
	}
}

?>
</body>
</html>
