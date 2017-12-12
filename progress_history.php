<?php
    Session_start();
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
                $stmt = mysqli_prepare($con, "SELECT * FROM Student WHERE StuID=?");//!!!!!JOIN PROJECT info
                mysqli_bind_param($stmt, 's', $stuid);
                mysqli_stmt_execute($stmt);
                $stuinfo = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
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
                ?>
        </div>
    </body>
</html> 
