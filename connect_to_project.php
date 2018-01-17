<?php
session_start();
require_once "sidebar_selector.php";
require_once "general_functions.php";

$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]); 
$id = $_SESSION["ID"];
if ($con->connect_error){
	echo "ERROR";
	die("Connection failed: " . $con->connect_error);
}
$sql = "SELECT ProjectName, Topic FROM Project";
$result = $con->query($sql);

?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="utf-8" />
    <meta name="Description" content= "InternshipApp login" />
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Connect to project - LIACS Graduation Application</title>
</head>
<body>

<div class="main">
    <h1>LIACS Graduation Application</h1>
    <h3>Connect to a project</h3>

    <select name="Projects"> 
	
	    <option value="nothing"> none </option>

	    <?php 
		    while($row = mysqli_fetch_array($result)) {
			    echo "<option value=". $row['ProjectName'] . ">" . $row['ProjectName'] . "</option>";
			    //echo "<option value=". $id . ">" . $id . "</option>";
			} 
	    ?>
	    
    </select>
    <input type="submit" name="submit" value="Subscribe" />
    </form>
    <?php
    if(isset($_POST['submit'])){
	    $sql = "DELETE FROM Does WHERE StuID=" . $id;
	    if (!$conn->query($sql)){
		    echo "ERROR";
	    } else {
		    $selected_val = $_POST['Projects'];  // Storing Selected Value In Variable
		    if ($selected_val == "nothing"){
			    echo "You have been unsubscribed";
		    } else{
			    $stmt = $conn->prepare("INSERT INTO Does (StuID, ProjectName) VALUES (?,?)");
			    $stmt->bind_param("ss", $id, $selected_val);
			    echo "We are subscribing " . $id . " to project: " . $selected_val;
			    $stmt->execute();
		    }
	    }
    }
    ?>
</div>
</body>
</html>
