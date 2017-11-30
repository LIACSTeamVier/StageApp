<?php

$servername = "mysql.liacs.leidenuniv.nl";
$username = "csthesis";
$password = "ldOIouqs";
$dbname = "csthesis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection 
session_start(); 
$id = $_GET["id"];

if ($conn->connect_error){
	die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT ProjectNaam FROM Projects";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="sortTable.js"></script>
</head>
<body>
<h1>suscribe to a project</h1>

<form action="#" method="post">
<select name="Projects">
	<td><?php echo $counter++ ?></td>
	<?php
		while($row1 = mysqli_fetch_array($result1)):;
	?>
<option value="<?php echo $row1 [1]; ?>"><?php echo $row1 [1]; ?></option>
<?php endwhile; ?>
<option value="nothing"> none </option>
</select>
<input type="submit" name="submit" value="suscribe" />
</form>
<?php
if(isset($_POST['submit'])){
$stmt = $conn->prepare("INSERT INTO volbrengt (afstudeerderID, ProjectNaam) VALUES (?,?)");
$stmt->bind_param("ss", $id, $selected_val);

$selected_val = $_POST['Projects'];  // Storing Selected Value In Variable
echo "We schrijven " . $id . " in in project: " . $selected_val;
//$stmt->execute();
}
?>

</body>
</html> 
