<?php
session_start();
?>

<!DOCTYPE html>
<html>
<body>


<?php
$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
// Create connection
$conn = new mysqli($volbrengt);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$sql = "INSERT INTO Does (StuID, ProjectName) Values (" $_GET["id"]; "," $_GET["project"]; ")" ;
$result = $conn->query($sql);
?>
</body>
</html> 
