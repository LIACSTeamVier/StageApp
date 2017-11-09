<?php
session_start();
?>

<!DOCTYPE html>
<html>
<body>


<?php
$host = "mysql.liacs.leidenuniv.nl";
$username = "csthesis";
$password = "ldOIouqs";
$dbname = "csthesis";
$con = mysqli_connect($host, $username, $password, $dbname);

// Create connection
$conn = new mysqli($volbrengt);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "INSERT INTO volbrengt (afstudeerderID, ProjectNaam) Values (" $_GET["id"]; "," $_GET["project"]; ")" ;
$result = $conn->query($sql);

</body>
</html> 
