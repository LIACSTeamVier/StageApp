<?php
    session_start();
    require_once "general_functions.php";
    require_once "sidebar_selector.php";
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Registered Students" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Student list - LIACS Graduation Application</title>
        <script src="sortTable.js"></script>
    </head>
    <body>
        <div class="main">
            <h1>LIACS Graduation Application</h1> 
            <?php
                $class = $_SESSION["class"];
                if ($class != "Admin") {
                    header("Location: main_page.php");
                    exit;
                }
                else {
                    $result = query_our_database("SELECT * FROM Student");
        
                    echo "<table class=\"list\" id='student_table'>"; // start a table tag in the HTML
        
                    // column names
                    echo "<tr><th onclick=\"sortTable(0, 'student_table')\">Student ID</th>
                          <th onclick=\"sortTable(1, 'student_table')\">Name</th>
                          <th>Supervision history</th>
                          <th>Progress history</th></tr>";
        
                    // rows of the database
                    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                        echo "<tr><td>" . $row['StuID'] . "</td>
                              <td>" . $row['StuName'] . "</td>
                              <td><form action=\"supervisor_history.php\" method=\"post\">
                              <input type=\"submit\" name=\"suphist\" value=\"Get supervision history\">
                              <input type=\"hidden\" name=\"stuHistID\" value=\"".$row['StuID']."\">
                              </form></td>
                              <td><form action=\"progress_history.php\" method=\"post\">
                              <input type=\"submit\" name=\"proghist\" value=\"Get progress history\">
                              <input type=\"hidden\" name=\"stuHistID\" value=\"".$row['StuID']."\">
                              </form></td></tr>";
                    }

                    echo "</table>"; //Close the table in HTML
                }
            ?>
</div>

</body>
</html> 
