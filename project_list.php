<?php
/*This file lists the available projects, students can subscribe to these projects, or make their own*/
session_start(); 
require_once "general_functions.php";
require_once "sidebar_selector.php";
date_default_timezone_set("Europe/Amsterdam");

$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
$self = htmlspecialchars($_SERVER["PHP_SELF"]);
                
$studentid = $_SESSION["ID"];
        
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

                
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    
    if(!empty($_POST["newprojname"]))
        $newProjectName = $_POST["newprojname"];
        
    if(!empty($_POST["delreq"])){
        if(!empty($_POST["confirmedDelete"])){
            if($_POST["confirmedDelete"] == "true"){ 
                $dateterm = date("Y-m-d: H:i:s");
                mysqli_query($con, "UPDATE Does SET Accepted='-1', ActivationCode=NULL, DateTerminated='$dateterm' WHERE StuID=$studentid AND Accepted='1'");//keep track when accepted relations are deleted
                $delres = mysqli_affected_rows($con);
                //dont keep track of unaccepted deletion
                mysqli_query($con, "DELETE FROM Does WHERE StuID=$studentid AND Accepted='0'");
                if(mysqli_affected_rows($con) ==0 && $delres == 0) 
                    die("mysql error");
                $_SESSION["needsDeleting"] = "";
                //header ("Location: Subscription.php?prjct=$newProjectName");
                echo "<form id='redirectSubscription' method=\"POST\" action=\"Subscription.php\">
                        <input type=\"hidden\" name=\"prjctname\" value=\"$newProjectName\"/>
                        </form>";
                echo "<script>document.getElementById('redirectSubscription').submit()</script>";            
            }
            else
               $_SESSION["needsDeleting"] = "";
        }
        else
            $_SESSION["needsDeleting"]="projectRequest";
        }
}
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Projects and Internships" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Projects and internships - LIACS Graduation Application</title>
        <script src="sortTable.js"></script>
        </style>
    </head>
    <body>
        <div class="main">
            <h1>LIACS Graduation Application</h1>
            <?php
                if($_SESSION["needsDeleting"] == "projectRequest"){
                    echo "<form id='1' action=\"$self\" method=\"post\">
                          <input type=\"hidden\" name=\"delreq\" value=\"true\">
                          <input type=\"hidden\" name=\"newprojname\" value=\"$newProjectName\">
                          <script>document.write('<input type=\"hidden\" name=\"confirmedDelete\" value=\"'+confirm(\"Confirm to unsubscribe from your current project and subscribe to: '$newProjectName'\")+'\">');</script>
                         </form>";
                    echo "<script>document.getElementById(1).submit()</script>";
                }
                
                if($_SESSION["class"] == "Student"){
                    echo "<form>
                            <input
                                type = \"button\" value = \"Make your own project\"
                                onclick = \"window.location.href='student_project.php'\"
                                style = \"background-color: #001158;
                                          border: none;
                                          color: white;
                                          padding: 10px 15px;
                                          text-align: center;
                                          display: inline-block;
                                          font-size: 16px;
                                          margin: 4px 2px;
                                          cursor: pointer;\"
                            />
                        </form>";
                }
                
                $in_a_project = mysqli_query($con, "SELECT ProjectName FROM Does WHERE StuID = '" . $_SESSION["ID"] ."' AND (Accepted='0' OR Accepted='1')") or die('Unable to run query:' . mysqli_error());
                $project_name = mysqli_fetch_array($in_a_project);
                
                $project_table = mysqli_query($con, "SELECT * FROM Project WHERE SupID IS NOT NULL OR IConID IS NOT NULL") or die('Unable to run query:' . mysqli_error());
                echo "<table class=\"list\" id='project_table'>"; // Start a table tag in the HTML
                
                // Column names
                echo "<tr><th onclick=\"sortTable(0, 'project_table')\">Name and description</th>
                          <th onclick=\"sortTable(1, 'project_table')\">Topic</th>
                          <th onclick=\"sortTable(2, 'project_table')\">Time</th>
                          <th onclick=\"sortTable(3, 'project_table')\">Progress</th>
                          <th onclick=\"sortTable(4, 'project_table')\">Student type</th>
                          <th onclick=\"sortTable(5, 'project_table')\">Internship</th>
                          <th onclick=\"sortTable(6, 'project_table')\">Teacher</th>
                          <th onclick=\"sortTable(7, 'project_table')\">Company</th>
                          <th onclick=\"sortTable(8, 'project_table')\"></th></tr>";
                          
                $row = mysqli_fetch_array($project_table);
                // Rows of the database
                while($row = mysqli_fetch_array($project_table)){ // Creates a loop to loop through results
                    $teacher_name_get = mysqli_query($con, "SELECT SupName FROM Supervisor WHERE SupID='".$row['SupID']."'")or die('Unable to run query:' . mysqli_error());
                    $teacher_name = mysqli_fetch_array($teacher_name_get);
                    echo "<tr><td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                          <td>" . $row['Topic'] . "</td>
                          <td>" . $row['Time'] . "</td>
                          <td>" . $row['Progress'] . "</td>
                          <td>" . $row['Studentqualities'] . "</td>";  
                 
                  if ($row['Internship'] == 1)
                      echo "<td>Yes</td>";
                  else
                      echo "<td>No</td>";
                   
                    echo "<td>" . $teacher_name['SupName'] . "</td>
                          <td>" . $row['CompanyName'] . "</td>";
                          
                    if ($_SESSION["class"] == "Student"){
                        echo "<td> ";
                        if($project_name){
                            $newProjectName = $row['ProjectName'];
                            /*echo "
                            <form method=\"POST\" action=\"Subscription_alt.php?prjct=" . $row['ProjectName'] . "\">
                                <input type=\"submit\" name=" . $row['ProjectName'] . " value=\"Subscribe\" />
                            </form>
                            </td>";*/
                            echo "<a><form action=\"$self\" method=\"post\">
                                <input type=\"hidden\" name=\"newprojname\" value=\"$newProjectName\">
                                <input type=\"submit\" name=\"delreq\" value=\"Subscribe\">
                                </form></a>";
                        } else {
                            echo "<form method=\"POST\" action=\"Subscription.php\">
                                <input type=\"hidden\" name=\"prjctname\" value=\"" . $row['ProjectName'] . "\"/>
                                <input type=\"submit\" name=\"scrub\" value=\"Subscribe\" />
                            </form>";

                        }
                    }
                    echo "</tr>";  // $row['index'] the index here is a field name
                }
                echo "</table>"; // Close the table in HTML
                mysqli_close($con);
            ?>
        </div>
    </body>
</html>

