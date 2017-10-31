<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="sidepane">
  <a href="main_page.php">Overview</a>
  <a href="project_list.php">Projects</a>
  <a href="#">Contact</a>
  <a href="database_table.php">Database</a>
  <a href="#">Help</a></a>
</div>

<div class="main">
  <?php
    $host = "";
    $username = "";
    $password = "";
    $dbname = "";
    $con = mysqli_connect($host, $username, $password, $dbname);
    
    // check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $result = mysqli_query($con, "SELECT * FROM Project") or die('Unable to run query:' . mysqli_error());

    echo "<table>"; // start a table tag in the HTML
    
    // column names
    echo "<tr><th>Projectnaam</th>
              <th>Beschrijving</th>
              <th>Voortgang</th>
              <th>Tijd</th>
              <th>Student qualities</th>
              <th>Topic</th>
              <th>Internship</th>
              <th>DocentID</th>
              <th>Begeleider</th>
              <th>Bedrijfsnaam</th></tr>";
    
    // rows of the database
    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['ProjectNaam'] . "</td>
              <td>" . $row['Beschrijving'] . "</td>
              <td>" . $row['Voortgang'] . "</td>
              <td>" . $row['Tijd'] . "</td>
              <td>" . $row['Studentqualities'] . "</td>
              <td>" . $row['Topic'] . "</td>
              <td>" . $row['Internship'] . "</td>
              <td>" . $row['DocentID'] . "</td>
              <td>" . $row['SBegeleiderNaam'] . "</td>
              <td>" . $row['BedrijfNaam'] . "</td></tr>";  //$row['index'] the index here is a field name
    }
    
    echo "</table>"; //Close the table in HTML
    
    mysqli_close($con);
  
  
    
    ?>
</div>

</body>
</html> 
