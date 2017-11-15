<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="sortTable.js"></script>
</head>
<body>
	
<h1>Creeer je project</h1>
<?php

$servername = "mysql.liacs.leidenuniv.nl";
$username = "csthesis";
$password = "ldOIouqs";
$dbname = "csthesis";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$stmt = $conn->prepare( "INSERT INTO Project(ProjectName, Description, Time, Studentqualities, Topic, Internship, SupID) VALUES (?,?,?,?,?,'0',?)");

$stmt->bind_param("ssssss", $name, $description, $tijdrest, $squal, $topic, $docid);
?> 

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form1" >
	Naam: <input type="text" name="naam"><br>
	Beschrijving: <input type="text" name="project"><br>
	Jouw kwaliteiten: <input type="text" name="squal"><br>
	Onderwerp: <input type="text" name="onderwerp"><br>
	Je supervisor: <input type="text" name="SupID"><br>
<input type="submit" name="create" value="suscribe" />
</form>

<?php

if(isset($_POST['create'])){
$name = $_POST['naam'];
$description = $_POST['project'];
$tijdrest = date('l jS \of F Y h:i:s A');
$squal = $_POST['squal'];
$topic = $_POST['onderwerp'];
$docid = $_POST['SupID'];
$stmt->execute();
}
?>

</body>
</html> 
