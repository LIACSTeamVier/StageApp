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
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    
    // check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $result = mysqli_query($con, "SELECT * FROM Student") or die('Unable to run query:' . mysqli_error());

    echo "<table>"; // start a table tag in the HTML
    
    // column names
    echo "<tr><th>StuID</th>
              <th>StuName</th>
              <th>StuEMAIL</th>
              <th>StuTel</th></tr>";
    
    // rows of the database
    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['StuID'] . "</td>
              <td>" . $row['StuName'] . "</td>
              <td>" . $row['StuEMAIL'] . "</td>
              <td>" . $row['StuTel'] . "</td></tr>";  //$row['index'] the index here is a field name
    }
    
    echo "</table>"; //Close the table in HTML
    
    mysqli_close($con);
  
  
    
    ?>
</div>

</body>
</html> 
