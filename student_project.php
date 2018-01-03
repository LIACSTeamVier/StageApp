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
	$sql = "SELECT StuID, ProjectName FROM Does WHERE StuID = ".$_SESSION['ID']." AND (Accepted = '0' OR Accepted = '1')";
	$result = $con->query($sql);
	if($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		echo "You are already subscribed to " . $row["ProjectName"] . ", if you continue making this project you will automatically be re-allocated to it.";
		if($row["SupID"] == NULL && $row["IConID"] == NULL){
			$previous = $row["ProjectName"];
			echo " and " . $row["ProjectName"] . "will be deleted";
		}
	}

	$nameErr = $descriptionErr = $timeErr = $topicErr = "";
	$name = $description = $time = $topic = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$success = "true";
	  if (empty($_POST["name"])) {
		$nameErr = "Name is required";
		$success = "false";
	  } else {
		$name = test_input($_POST["name"]);
	  }
	  
	  if (empty($_POST["project"])) {
		$descriptionErr = "Description is required";
		$success = "false";
	  } else {
		$description = test_input($_POST["project"]);
	  } 
		
	  if (empty($_POST["time"])) {
		$timeErr = "Time is required";
		$success = "false";
	  } else {
		$time = test_input($_POST["time"]);
	  }

	  if (empty($_POST["topic"])) {
		$topicErr = "topic is required";
		$success = "false";
	  } else {
		$topic = test_input($_POST["topic"]);
	  }
	  
	  if($success == "true"){
		  $nameErr = $descriptionErr = $timeErr = $topicErr = "";
          $stmt = $con->prepare( "INSERT INTO Project VALUES (?,?,NULL,?,NULL,?,'0',NULL,NULL,NULL,NULL,False,False,False,False,False,False)");
          $stmt->bind_param("ssss", $name, $description, $time, $topic);
		  $stmt->execute();
		  
		  $sql = "INSERT INTO Does(ProjectName, Accepted, DateAccepted, DateRequested, StuID) VALUES ('". $name ."', '1', '" . date("Y-m-d: H:i:s") ."','". date("Y-m-d: H:i:s") ."', '" . $_SESSION['ID'] . "')";
		   
		  $sql2 = "DELETE FROM Does WHERE StuID = '" . $_SESSION['ID'] . "' AND (Accepted='0' OR Accepted='1')";
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
		  header("Location: main_page.php");
	  }
	}
	?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" name="form1" >
        <table class="form">
		    <tr>
			    <td>Project name:</td>
				<td><input type="text" name="name" value="<?php echo $name;?>"></td>
				<td><span class="error">* <?php echo $nameErr;?></span></td>
			</tr>
            <tr>
			    <td>Required time:</td>
				<td><input type="text" name="time" value="<?php echo $time;?>"></td>
				<td><span class="error">* <?php echo $timeErr;?></span></td>
			</tr>
            <tr>
                <td>Topic:</td>
                <td><input type="text" name="topic" value="<?php echo $topic;?>"></td>
		        <td><span class="error">* <?php echo $topicErr;?></span></td>
            </tr>
            <tr>
                <td>Description:</td> 
	            <td><textarea name="project" rows="5" cols="40"></textarea></td>
		        <td><span class="error">* <?php echo $descriptionErr;?></span></td>
            </tr>
        </table>
    <input type="submit" name="create" value="create" />
    </form>

</div>
</body>
</html> 
