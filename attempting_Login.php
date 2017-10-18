<!DOCTYPE html>
<html lang="en-UK">
<body>

<?php
    $identifier = $_POST["username"];
    $password = $_POST["password"];
    $con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "-", "s1551396");
    // Check connection
    if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $result = mysqli_query($con, "SELECT * FROM StageApp_Gebruikers g WHERE g.Identifier='$identifier'") or die('Unable to run query:' . mysqli_error());
    
    $row = mysqli_fetch_row($result);
    
    if ($row[3] == $password)
        echo "Welkom, $row[2].";
    else
        echo "Onjuiste Gebruiker-Paswoord combinatie.";
    /*echo "<table>";
    echo "<tr>";
    echo "<td>Naamvak</td>";
    echo "<td>Deadline 1</td>";
    echo "<td>Deadline 2</td>";
    echo "<td>Deadline 3</td>";
    echo "<td>Deadline 4</td>";
    echo "<td>Deadline 5</td>";
    echo "<td>Deadline 6</td>";
    echo "<td>Deadline 7</td>";
    echo "<td>Deadline 8</td>";
    echo "<td>Deadline 9</td>";
    echo "<td>Deadline 10</td>";
    echo "</tr>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['Naamvak'] . "</td>";
        echo "<td>" . $row['Deadline 1'] . "</td>";
        echo "</tr>\n";
    }
    echo "</table>";*/
    mysqli_close($con);
?>



</body>
</html> 