<?php
Session_start();
require_once "sidebar_selector.php";
require_once "general_functions.php";

$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]); 
$id = $_SESSION["id"];
echo $id;
if ($con->connect_error){
	die("Connection failed: " . $conn->connect_error);
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
    <script src="sortTable.js"></script>
    <title>Edit a project - LIACS Student Project Manager</title>
</head>
<body>

<div class="main">
    <h1>LIACS Student Project Manager</h1>
    <h3>Edit a project</h3>

    <form action="#" method="post">
	    <select name="Projects">
		    <?php 
		        while($row = mysqli_fetch_array($result)){
			        echo "<option value=". $row['ProjectName'] . ">" . $row['ProjectName'] . "</option>";
			        //echo "<option value=". $id . ">" . $id . "</option>";
			    } 
	        ?>
	    </select>
	    <select name = "attribute">
		    <option value= "Naam"> Name </option>
		    <option value= "Beschrijving"> Description </option>
		    <option value= "Tijd"> Time </option>
		    <option value= "qualities"> Requirements </option>
		    <option value= "Topic"> Topic </option>
		    <option value= "Internship"> Internship Status </option>
		    <option value= "DocentID"> Supervisor Number</option>
		    <option value= "SBegeleider"> Business </option>
		    <option value= "Progress"> Progress </option>
		    <option value= ""> </option>
	    </select>
	    <input type="submit" name="submit" value="edit" />
    </form>
    <?php
    if(isset($_POST['submit'])){
	    $selected_val = $_POST['attribute'];
	    $prjct = $_POST['Projects'];
	    $sql = "SELECT Internship FROM Project WHERE ProjectNaam =" . $prjct;
	    $result = $conn->query($sql);
	    //$row = $result->fetch_assoc();
	    //echo "ah" . $row["Internship"] . "";
	    if($selected_val == "SBegeleider" && $row['Internship'] == "0"){
		    echo "the selected project is not an internship";
	    } else {
		    if($selected_val != "Internship"){
			    if($selected_val == "SBegeleider"){
				    echo "
				    <form action=\"##\" method=\"post\">
				    Business Supervisor <input type=\"text\" name=\"icon\">
				    <br><br>
				    Business Name: <input type=\"text\" name=\"comp\">
				    <br><br>
				    <input type=\"submit\" name=\"create\" value=\"replace\" />
				    ";
			    } else{
				    echo "
				    <form action=\"##\" method=\"post\">
				    <textarea name=\"project\" rows=\"5\" cols=\"40\"></textarea> <br>
				    ";
				    if($selected_val == "Progress"){
					    echo"
					    <input type=\"submit\" name=\"create\" value=\"append\" />
					    ";
				    }else{
					    echo"
					    <input type=\"submit\" name=\"create\" value=\"replace\" />
					    ";
				    }
				    echo "</form>";
			    }
		    } else{ /*
			    if($row['Internship'] == "0"){
				    echo "Make an internship?";
				    $switch = 1;
			    }
			    else{
				    echo "Take away internship status?";
				    $switch = 0;
			    }
			    echo "
				    <form action=\"##\" method=\"post\">
				    <input type=\"submit\" name=\"create\" value=\"yes\" />
				    </form>
			    ";*/
			    echo "not yet implemented";
		    }
	    }
	    if(isset($_POST['create'])){
		    echo $selected_val;
		    if($selected_val == "SBegeleider"){
			    $sql = "UPDATE Project SET IConName = '" . $_POST['icon'] . "' WHERE ProjectName = '" . $prjct . "'";
			    $sql .= "UPDATE Project SET CompanyName = '" . $_POST['comp'] . "' WHERE ProjectName = " . $prjct . "'";
		    }
		    echo $sql;
		    $conn->query($sql);
	    }
    }

    ?>
</div>
</body>
</html>
