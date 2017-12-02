<?php
Session_start();
require_once "sidebar_selector.php";
require_once "general_functions.php";
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
    $stmt = $con->prepare( "INSERT INTO Project(ProjectName, Description, Progress, Time, Studentqualities, Topic, Internship, SupID) VALUES (?,?, NULL,?,?,?,'0',?)");

    $stmt->bind_param("ssssss", $name, $description, $tijdrest, $squal, $topic, $docid);
    ?> 

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form1" >
	    Project name: <input type="text" name="naam"><br>
	    Required skills: <input type="text" name="eisen"><br> <br>
	    Required time: <input type="text" name="looptijd"><br> <br>
	    Description: 
	    <textarea name="project" rows="5" cols="40"></textarea> <br>
	    Subject: <select name="onderwerp">
				    <option value = "ICT">ICT</option>">
				    <option value = "Business">Business</option>">
				    <option value = "ICT & Business">ICT & Business</option>">
				    </select>
    <input type="submit" name="create" value="create" />
    </form>

    <?php

    if(isset($_POST['create'])){
    $name = $_POST['naam'];
    $description = $_POST['project'];
    $tijdrest = $_POST['looptijd'];
    $squal = $_POST['eisen'];
    $topic = $_POST['onderwerp'];
    $sql = "SELECT SupID FROM Supervises D WHERE D.type = 'First Supervisor' AND D.Accepted = '1' AND D.StuID = '" . $_SESSION['ID'] . "'";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();
    $docid = $row["SupID"];
    $stmt->execute();
    }
    ?>

</div>
</body>
</html> 
