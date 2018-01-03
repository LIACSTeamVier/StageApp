<?php
    session_start();
    require_once "sidebar_selector.php";
    require_once "general_functions.php";
    
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // Check connection
    if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $stuid = test_input($_POST["stuHistID"]);
?>
<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Progress history for <?php echo $stuid;?> - LIACS Student Project Manager</title>
        <script src="sortTable.js"></script>
    </head>
    <body>
        <div class="main">
            <h1>LIACS Student Project Manager</h1>
            <?php
                if($_SESSION["class"] != "Admin" || empty($stuid)) {
                    header("Location: main_page.php");
                    die();
                }
                //Get student info
                $stmt = mysqli_prepare($con, "SELECT * FROM Student s LEFT JOIN Does d ON s.StuID = d.StuID LEFT JOIN Project p ON d.ProjectName = p.Projectname WHERE s.StuID=?");
                mysqli_bind_param($stmt, 's', $stuid);
                mysqli_stmt_execute($stmt);
                $stuinfo = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
                $stmt = mysqli_prepare($con, "SELECT * FROM Does d WHERE d.StuID=?");
                mysqli_bind_param($stmt, 's', $stuid);
                mysqli_stmt_execute($stmt);
                $project = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
                if (!$project) {
                    echo "<h3>Student has not yet joined a project.</h3>";
                } else {
                    $propacc = $stuinfo['PropAccept'];
                    $startpro = $stuinfo['StartPro'];
                    $midrev = $stuinfo['MidRev'];
                    $thesissub = $stuinfo['ThesisSub'];
                    $thesisacc = $stuinfo['ThesisAcc'];
                    $pressched = $stuinfo['PresSched'];
                    if (($propacc != "False") || ($startpro != "False") || ($midrev != "False") || 
                        ($thesissub != "False") || ($thesisacc != "False") || ($pressched != "False")){
                        echo "<h3>Progress History for the student:</h3>
                                <table class=\"list\">
                                    <tr>
                                        <th>Progress</th><th>Date Progressed</th>
                                    </tr>";
                        if(preg_match('/True/',$propacc)){
                            echo "<tr><td>Research Proposal Accepted</td><td>";
                            echo substr($propacc, 5);
                            echo "</td></tr>";
                        }
                        if(preg_match('/True/',$startpro)){
                            echo "<tr><td>Started Project</td><td>";
                            echo substr($startpro, 5);
                            echo "</td></tr>";
                        }
                        if(preg_match('/True/',$midrev)){
                            echo "<tr><td>Midterm Review</td><td>";
                            echo substr($midrev, 5);
                            echo "</td></tr>";
                        }
                        if(preg_match('/True/',$thesissub)){
                            echo "<tr><td>Thesis Submitted</td><td>";
                            echo substr($thesissub, 5);
                            echo "</td></tr>";
                        }
                        if(preg_match('/True/',$thesisacc)){
                            echo "<tr><td>Thesis Accepted</td><td>";
                            echo substr($thesisacc, 5);
                            echo "</td></tr>";
                        }
                        if(preg_match('/True/',$pressched)){
                            echo "<tr><td>Presentation Scheduled</td><td>";
                            echo substr($pressched, 5);
                            echo "</td></tr>";
                        }    
                        echo "     </tr>
                                </table>";            
                        echo "</br></br></br>";
                    }
                    if ( preg_match('/False /',$propacc) || preg_match('/False /',$startpro) || preg_match('/False /',$midrev) || 
                        preg_match('/False /',$thesissub) || preg_match('/False /',$thesisacc) || preg_match('/False /',$pressched) ){
                        echo "<h3>Progress Removal History for the student:</h3>
                                <table class=\"list\">
                                    <tr>
                                        <th>Progress</th><th>Date Progress Removed</th>
                                    </tr>";
                        if(preg_match('/False /',$propacc)){
                            echo "<tr><td>Research Proposal Accepted</td><td>";
                            echo substr($propacc, 6);
                            echo "</td></tr>";
                        }
                        if(preg_match('/False /',$startpro)){
                            echo "<tr><td>Started Project</td><td>";
                            echo substr($startpro, 6);
                            echo "</td></tr>";
                        }
                        if(preg_match('/False /',$midrev)){
                            echo "<tr><td>Midterm Review</td><td>";
                            echo substr($midrev, 6);
                            echo "</td></tr>";
                        }
                        if(preg_match('/False /',$thesissub)){
                            echo "<tr><td>Thesis Submitted</td><td>";
                            echo substr($thesissub, 6);
                            echo "</td></tr>";
                        }
                        if(preg_match('/False /',$thesisacc)){
                            echo "<tr><td>Thesis Accepted</td><td>";
                            echo substr($thesisacc, 6);
                            echo "</td></tr>";
                        }
                        if(preg_match('/False /',$pressched)){
                            echo "<tr><td>Presentation Scheduled</td><td>";
                            echo substr($pressched, 6);
                            echo "</td></tr>";
                        }    
                        echo "     </tr>
                                </table>";            
                        echo "</br></br></br>";
                    }
                    
                    if(($propacc == "False") || ($startpro == "False") || ($midrev == "False") || 
                        ($thesissub == "False") || ($thesisacc != "False") || ($pressched == "False")){
                        echo "<p>No progess yet in: ";
                        if($propacc == "False")
                            echo "</br>Research Proposal Accepted";
                        if($startpro == "False")
                            echo "</br>Started Project";
                        if($midrev == "False")
                            echo "</br>Midterm Review";
                        if($thesissub == "False")
                            echo "</br>Thesis Submitted";
                        if($thesisacc == "False")
                            echo "</br>Thesis Accepted";
                        if($pressched == "False")
                            echo "</br>Presentation Scheduled";
                        echo "</p>";
                    }
                }
                $result = query_our_database("SELECT Date, Entry FROM Log WHERE StuID =".$stuid);
                echo "<h3>Progress Log</hr>
                            <table class=\"list\" id='log_table'>"; // start a table tag in the HTML
                
                // column names
                echo "<tr><th onclick=\"sortTable(0, 'log_table')\">Date</th>
                          <th onclick=\"sortTable(1, 'log_table')\">Entry</th>
                      </tr>";
                // rows of the database
                while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results

                    echo "<tr><td>".$row['Date']."</td>
                              <td>".$row['Entry']."</td>
                          </tr>";
                }
                echo "</table><br>"; //Close the table in HTML
                
                ?>
        </div>
    </body>
</html> 

