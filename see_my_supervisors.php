<?php
$servernaam = "localhost";
$gebruikersnaam = "username";
$paswoord = "password";
$dbnaam = "database";

// Create connection
$conn = new mysqli($servernaam, $gebruikersnaam, $paswoord, $dbnaam);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT Docent_ID, Naam FROM Begeleider B, Begeleid L, Afstudeerder A WHERE (B.Docent_ID = L.Docent_ID)  AND (L.Student_ID = " . $_SESSION[“my_ID”] . “)” ;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo  . $row["naam"]. "<br>";
    }
} else {
    echo "Er zijn geen begeleiders.";
}
$conn->close();
?> 