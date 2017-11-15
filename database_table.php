<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Registered Students" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Registered Students</title>
    </head>
    <body>

        <?php include 'general_functions.php';?>

        <div class="sidepane">
            <a href="main_page.php">Overview</a>
            <a href="project_list.php">Projects</a>
            <a href="#">Contact</a>
            <a href="database_table.php">Database</a>
            <a href="#">Help</a></a>
        </div>

        <div class="main">
            <?php
                $class = $_SESSION["class"];
                if ($class != "Admin") {
                    header("Location: main_page.php");
                    exit;
                }
                else {
                    $result = query_our_database("SELECT * FROM Student");
        
                    echo "<table>"; // start a table tag in the HTML
        
                    // column names
                    echo "<tr><th>Student ID</th>
                          <th>Name</th>
                          <th>E-mail</th>
                          <th>Telephone</th></tr>";
        
                    // rows of the database
                    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                        echo "<tr><td>" . $row['StuID'] . "</td>
                              <td>" . $row['StuName'] . "</td>
                              <td>" . $row['StuEMAIL'] . "</td>
                              <td>" . $row['StuTel'] . "</td></tr>";  //$row['index'] the index here is a field name
                    }

                    echo "</table>"; //Close the table in HTML
                }
            ?>
</div>

</body>
</html> 
