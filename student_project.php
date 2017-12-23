<?php
session_start();
require_once "general_functions.php";
require_once "sidebar_selector.php";
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="utf-8" />
    <meta name="Description" content= "InternshipApp login" />
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="sortTable.js"></script>
    <title>Create a project - LIACS Student Project Manager</title>
</head>
<body>

<div class="main">
    <h1>LIACS Student Project Manager</h1>
    <h3>Create a project</h3>
    
    <?php
    
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    $stmt = $con->prepare( "INSERT INTO Project(ProjectName, Description, Progress, Time, Studentqualities, Topic, Internship, SupID) VALUES (?,?, NULL,?,?,?,'0')");


	$sql = "SELECT StuID, ProjectName FROM Does WHERE StuID = " . $_SESSION['ID'];
	$result = $con->query($sql);
	if($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		echo "You are already suscribed to " . $row["ProjectName"] . ", if you continue making this project you will automatically be re-allocated to it.";
		if($row["SupID"] = NULL){
			$previous = $row["ProjectName"];
			echo " and " . $row["ProjectName"] . "will be deleted";
		}
	}


    $stmt->bind_param("sssss", $name, $description, $tijdrest, $squal, $topic);
    
	$nameErr = $descriptionErr = $tijdrestErr = $squalErr = $topicErr = "";
	$name = $description = $tijdrest = $squal = $topic = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$success = "true";
	  if (empty($_POST["naam"])) {
		$nameErr = "Name is required";
		$success = "false";
	  } else {
		$name = test_input($_POST["naam"]);
	  }
	  
	  if (empty($_POST["project"])) {
		$descriptionErr = "Description is required";
		$success = "false";
	  } else {
		$description = test_input($_POST["project"]);
	  } 
		
	  if (empty($_POST["looptijd"])) {
		$tijdrestErr = "Time is required";
		$success = "false";
	  } else {
		$tijdrest = test_input($_POST["looptijd"]);
	  }

	  if (empty($_POST["eisen"])) {
		$squalErr = "Student qualities are required";
		$success = "false";
	  } else {
		$squal = test_input($_POST["eisen"]);
	  }

	  if (empty($_POST["onderwerp"])) {
		$topicErr = "topic is required";
		$success = "false";
	  } else {
		$topic = test_input($_POST["onderwerp"]);
	  }
	  
	  if($success == "true"){
		  $nameErr = $descriptionErr = $tijdrestErr = $squalErr = $topicErr = "";
		  $stmt->execute();
		  
		  $sql = "INSERT INTO Does(ProjectName, Accepted, DateAccepted, DateRequested, StuID) VALUES ('". $name ."', '1', '" . date("Y-m-d: H:i:s") ."','". date("Y-m-d: H:i:s") ."', '" . $_SESSION['ID'] . "')";
		   
		  $sql2 = "DELETE FROM Does WHERE StuID = '" . $_SESSION['ID'] . "'";
		  $result = $con->query($sql2);
		  if(!$result){echo "query 1";
			die('Unable to run query1:' . mysqli_error());}
			
		  if($previous != NULL){
			  $sql2 = "DELETE FROM Project WHERE ProjectName = '" . $previous . "'";
			  $result = $con->query($sql2);
			  if(!$result){echo "query 1";
				die('Unable to run query1:' . mysqli_error());}
		  }
			
		  $result = $con->query($sql);
		  if(!$result){echo "query 2";
			die('Unable to run query2:' . mysqli_error());}
		  //header("Location: main_page.php");
	  }
	}
	?> 

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" name="form1" >
	    Project name: <input type="text" name="naam">
		<span class="error">* <?php echo $nameErr;?></span><br> <br>
	    Required skills: <input type="text" name="eisen">
		<span class="error">* <?php echo $squalErr;?></span><br> <br>
	    Required time: <input type="text" name="looptijd">
		<span class="error">* <?php echo $tijdrestErr;?></span><br> <br>
	    Description: 
	    <textarea name="project" rows="5" cols="40"></textarea>
		<span class="error">* <?php echo $descriptionErr;?></span> <br>
	    Topic: <input type="text" name="onderwerp">
		<span class="error">* <?php echo $topicErr;?></span><br> <br>
    <input type="submit" name="create" value="create" />
    </form>

    <?php
/*
    if(isset($_POST['create'])){
    $name = $_POST['naam'];
    $description = $_POST['project'];
    $tijdrest = $_POST['looptijd'];
    $squal = $_POST['eisen'];
    $topic = $_POST['onderwerp'];
    $stmt->execute();
    $sql = "INSERT INTO Does(ProjectName, Accepted, DateAccepted, DateRequested, StuID) VALUES (". $name; .", 1, " . date('l jS \of F Y h:i:s A') .",". date('l jS \of F Y h:i:s A') .", " . $_SESSION['ID'] . ")";
    $result = $con->query($sql);
    header("Location: main_page.php");
    }
    * */
    ?>

</div>
</body>
</html> 
