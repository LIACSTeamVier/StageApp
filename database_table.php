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
    $host = "mysql.liacs.leidenuniv.nl";
    $username = "csthesis";
    $password = "-";
    $dbname = "csthesis";
    $con = mysqli_connect($host, $username, $password, $dbname);
    
    // check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $result = mysqli_query($con, "SELECT * FROM Afstudeerder") or die('Unable to run query:' . mysqli_error());

    echo "<table>"; // start a table tag in the HTML
    
    // column names
    echo "<tr><th>StudentID</th>
              <th>StudentNaam</th>
              <th>StuEMAIL</th>
              <th>StudentTel</th></tr>";
    
    // rows of the database
    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['StudentID'] . "</td>
              <td>" . $row['StudentNaam'] . "</td>
              <td>" . $row['StuEMAIL'] . "</td>
              <td>" . $row['StudentTel'] . "</td></tr>";  //$row['index'] the index here is a field name
    }
    
    echo "</table>"; //Close the table in HTML
    
    mysqli_close($con);
  
  
    
    ?>
</div>

</body>
</html> 
