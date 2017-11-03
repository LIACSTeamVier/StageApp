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
    
    $project_table = mysqli_query($con, "SELECT * FROM Project") or die('Unable to run query:' . mysqli_error());
    echo "<table width='90%'>"; // start a table tag in the HTML
    
    // column names
    echo "<tr><th>Name and description</th>
              <th>Topic</th>
              <th>Time</th>
              <th>Progress</th>
              <th>Student type</th>
              <th>Internship</th>
              <th>Teacher</th>
              <th>Company</th></tr>";
    
    // rows of the database
    while($row = mysqli_fetch_array($project_table)){   //Creates a loop to loop through results
        $teacher_name_get = mysqli_query($con, "SELECT BegeleiderNaam FROM Begeleider WHERE DocentID=" . $row['DocentID']) or die('Unable to run query:' . mysqli_error());
        $teacher_name = mysqli_fetch_array($teacher_name_get);
        echo "<tr><td width='40%'><b>" . $row['ProjectNaam'] . "</b><p style='margin-left: 5px'>" . $row['Beschrijving'] . "</p></td>
              <td>" . $row['Topic'] . "</td>
              <td>" . $row['Tijd'] . "</td>
              <td>" . $row['Voortgang'] . "</td>
              <td>" . $row['Studentqualities'] . "</td>";  
              if ($row['Internship'] == 1)
                  echo "<td>Yes</td>";
              else
                  echo "<td>No</td>";
        echo "<td>" . $teacher_name['BegeleiderNaam'] . "</td>
              <td>" . $row['BedrijfNaam'] . "</td></tr>";  //$row['index'] the index here is a field name
    }
    
    echo "</table>"; //Close the table in HTML
    
    mysqli_close($con);
  
  
    
    ?>
</div>

</body>
</html> 
