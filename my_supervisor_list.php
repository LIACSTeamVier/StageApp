 <!DOCTYPE html>
<html>
<body>

<h1>Supervisors</h1>

<?php
$servername = "mysql.liacs.leidenuniv.nl";
$username = "csthesis";
$password = "ldOIouqs";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error){
	die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Docent_ID, Naam FROM Begeleider B, Begeleid L, Afstudeerder A WHERE (B.Docent_ID = L.Docent_ID)  AND (L.Student_ID = " . $_SESSION["my_ID"] . ")" ;
$result = $conn->query($sql);

if($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "" . $row["Naam"]. "";
	}
}
else{
	echo "You have are no supervisors";
}

echo $result->num_rows

?>

</body>
</html> 