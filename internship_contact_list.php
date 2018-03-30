<?php
    session_start();
    $highlight = "Internship contacts";
    require_once "general_functions.php";
    require_once "sidebar_selector.php";
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["delwarn"])) {
                echo "<form id='1' action=\"$self\" method=\"post\">
	                  <input type=\"hidden\" name=\"del\" value=\"true\">
	                  <input type=\"hidden\" name=\"IConID\" value=\"".$_POST['IConID']."\">
	                  <script>document.write('<input type=\"hidden\" name=\"confirmed\" value=\"'+confirm(\"Do you really want to delete this internship contact?\")+'\">');</script>
	                  </form>";
	            echo "<script>document.getElementById(1).submit()</script>";
        }
        else if (!empty($_POST["del"]) && $_POST["confirmed"] == "true") {
            // Deletes the contact, as well as every project related to the contact.
            $IConID = $_POST["IConID"];
            $ProjectName = mysqli_fetch_array(query_our_database("SELECT ProjectName FROM Project WHERE IConID='$IConID'"));
            while (!empty($ProjectName[0])){
                query_our_database("DELETE FROM Part_of WHERE ProjectName='".$ProjectName[0]."'");
                query_our_database("DELETE FROM Does WHERE ProjectName='".$ProjectName[0]."'");
                query_our_database("DELETE FROM Internship_of WHERE ProjectName='".$ProjectName[0]."'");
                query_our_database("DELETE FROM Project WHERE ProjectName='".$ProjectName[0]."'");
                $ProjectName = mysqli_fetch_array(query_our_database("SELECT ProjectName FROM Project WHERE IConID='$IConID'"));
            }
            query_our_database("DELETE FROM Internship_Contact WHERE IConID='".$IConID."'");
            query_our_database("DELETE FROM InternshipApp_Users WHERE Identifier='".$IConID."'");
        }
    }
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Registered internship contacts" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Internship contact list - LIACS Graduation Application</title>
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
                    $result = query_our_database("SELECT * FROM Internship_Contact");
        
                    echo "<table class=\"list\" id='icon_table'>"; // start a table tag in the HTML
        
                    // column names
                    echo "<tr><th onclick=\"sortTable(0, 'icon_table')\">Internship contact ID</th>
                          <th onclick=\"sortTable(1, 'icon_table')\">Company name</th>
                          <th>Internship contact name</th>
                          <th>E-mail</th>
                          <th>Phone number</th>
                          <th>Delete</th></tr>";
        
                    // rows of the database
                    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                        echo "<tr><td>" . $row['IConID'] . "</td>
                              <td>" . $row['CompanyName'] . "</td>
                              <td>" . $row['IConName'] . "</td>
                              <td>" . $row['IConEMAIL'] . "</td>
                              <td>" . $row['IConTel'] . "</td>
                              <td><form action=\"$self\" method=\"post\">
                              <input type=\"submit\" name=\"delwarn\" value=\"Delete internship contact\">
                              <input type=\"hidden\" name=\"IConID\" value=\"".$row['IConID']."\">
                              </form></td></tr>";
                    }

                    echo "</table>"; //Close the table in HTML
                }
            ?>
</div>

</body>
</html> 
