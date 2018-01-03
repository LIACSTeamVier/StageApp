<?php
    session_start();
    require_once "sidebar_selector.php";
    require_once "general_functions.php";
    
    date_default_timezone_set("Europe/Amsterdam");
    $date = date("Y-m-d: H:i:s");
    $configs = include("config.php");
    $self = htmlspecialchars($_SERVER["PHP_SELF"]);
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } 
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["unsubproject"])) {
            echo "<form id='1' action=\"$self\" method=\"post\">
	              <input type=\"hidden\" name=\"delproj\" value=\"true\">
	              <script>document.write('<input type=\"hidden\" name=\"confirmedproj\" value=\"'+confirm(\"This is a serious action, do you really want to unsubscribe from project: ".$_POST["projname"]."?\")+'\">');</script>
	              </form>";
	        echo "<script>document.getElementById(1).submit()</script>";
        }
        if (!empty($_POST["delproj"]) && $_POST["confirmedproj"] == "true") {
            //keep track when accepted relations are deleted
            query_our_database("UPDATE Does SET Accepted='-1', ActivationCode=NULL, DateTerminated='$date' WHERE StuID=".$_SESSION["ID"]." AND Accepted='1'");
        }
        if (!empty($_POST["delreq"])) {
            //dont keep track of unaccepted deletion
            query_our_database("DELETE FROM Does WHERE StuID=".$_SESSION["ID"]." AND Accepted='0'");
        }
        if (!empty($_POST["progressupdate"])){
            if(!empty($_POST["progupdate"])){
                $pupupdate = test_input($_POST["progupdate"]);
                $resproj = mysqli_query($con,"SELECT * FROM Does WHERE StuID='".$_SESSION["ID"]."'");
                $rowproj = mysqli_fetch_array($resproj);
                $projname = $rowproj["ProjectName"];
                $stmt = mysqli_prepare($con, "UPDATE Project SET Progress=? WHERE ProjectName=?");
                mysqli_bind_param($stmt, 'ss', $pupupdate, $projname);
                mysqli_stmt_execute($stmt);
                $numrow = mysqli_affected_rows($con);
                mysqli_stmt_close($stmt);
                if($numrow != 1){
                    die("Error updating progress");
                }
                $stmt = mysqli_prepare($con, "INSERT INTO Log VALUES (?, ?, ?)");
                mysqli_bind_param($stmt, 'sss', $_SESSION["ID"], $date, $_POST["progupdate"]);
                mysqli_stmt_execute($stmt);
                $numrow = mysqli_affected_rows($con);
                mysqli_stmt_close($stmt);
                if($numrow != 1){
                    die("Error updating progress");
                }
            }
            $result = query_our_database("SELECT p.PropAccept, p.StartPro, p.MidRev, p.ThesisSub, p.ThesisAcc, p.PresSched FROM Project p WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            $row = mysqli_fetch_row($result);
            if(isset($_POST["newcheck0"]) && preg_match("/False/",$row[0]))             // change True to False 
                query_our_database("UPDATE Project SET PropAccept ='True ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            elseif(!isset($_POST["newcheck0"]) && preg_match("/True/",$row[0]))         // or vice versa for each
                query_our_database("UPDATE Project SET PropAccept ='False ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            if(isset($_POST["newcheck1"]) && preg_match("/False/",$row[1]))             // possible progresscheck.
                query_our_database("UPDATE Project SET StartPro ='True ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            elseif(!isset($_POST["newcheck1"]) && preg_match("/True/",$row[1]))
                query_our_database("UPDATE Project SET StartPro ='False ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            if(isset($_POST["newcheck2"]) && preg_match("/False/",$row[2]))
                query_our_database("UPDATE Project SET MidRev ='True ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            elseif(!isset($_POST["newcheck2"]) && preg_match("/True/",$row[2]))
                query_our_database("UPDATE Project SET MidRev ='False ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            if(isset($_POST["newcheck3"]) && preg_match("/False/",$row[3]))
                query_our_database("UPDATE Project SET ThesisSub ='True ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            elseif(!isset($_POST["newcheck3"]) && preg_match("/True/",$row[3]))
                query_our_database("UPDATE Project SET ThesisSub ='False ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            if(isset($_POST["newcheck4"]) && preg_match("/False/",$row[4]))
                query_our_database("UPDATE Project SET ThesisAcc ='True ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            elseif(!isset($_POST["newcheck4"]) && preg_match("/True/",$row[4]))
                query_our_database("UPDATE Project SET ThesisAcc ='False ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            if(isset($_POST["newcheck5"]) && preg_match("/False/",$row[5]))
                query_our_database("UPDATE Project SET PresSched ='True ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            elseif(!isset($_POST["newcheck5"]) && preg_match("/True/",$row[5]))
                query_our_database("UPDATE Project SET PresSched ='False ".$date."' WHERE ProjectName='".$_SESSION["ProjectName"]."'");
            
            $progressupdated = "Progress has been updated!";
            unset($_SESSION["ProjectName"]);
        }
        if (!empty($_POST["delwarn1"])) {
            echo "<form id='1' action=\"$self\" method=\"post\">
	              <input type=\"hidden\" name=\"del1\" value=\"true\">
	              <script>document.write('<input type=\"hidden\" name=\"confirmed\" value=\"'+confirm(\"This is a serious action, do you really want to delete the first supervisor?\")+'\">');</script>
	              </form>";
	        echo "<script>document.getElementById(1).submit()</script>";
        }
        if (!empty($_POST["del1"]) && $_POST["confirmed"] == "true") {
            query_our_database("UPDATE Supervises SET Accepted='-1', ActivationCode=NULL, DateTerminated='$date' WHERE type='First Supervisor' AND StuID=".$_SESSION["ID"]." AND Accepted='1'");//keep track when accepted relations are deleted
            //dont keep track of unaccepted deletion
            query_our_database("DELETE FROM Supervises WHERE type='First Supervisor' AND StuID=".$_SESSION["ID"]." AND Accepted='0'");
        }
        if (!empty($_POST["delwarn2"])) {
            echo "<form id='2' action=\"$self\" method=\"post\">
	              <input type=\"hidden\" name=\"del2\" value=\"true\">
	              <script>document.write('<input type=\"hidden\" name=\"confirmed2\" value=\"'+confirm(\"This is a serious action, do you really want to delete the second supervisor?\")+'\">');</script>
	              </form>";
	        echo "<script>document.getElementById(2).submit()</script>";
        }
        if (!empty($_POST["del2"]) && $_POST["confirmed2"] == "true") {
            query_our_database("UPDATE Supervises SET Accepted='-1', ActivationCode=NULL, DateTerminated='$date' WHERE type='Second Supervisor' AND StuID=".$_SESSION["ID"]." AND Accepted='1'");//keep track when accepted relations are deleted
            //dont keep track of unaccepted deletion
            query_our_database("DELETE FROM Supervises WHERE type='Second Supervisor' AND StuID=".$_SESSION["ID"]." AND Accepted='0'");
        }
    }
    mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Home" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Overview - LIACS Student Project Manager</title>
        <script src="sortTable.js"></script>
    </head>
    <body>

        <div class="main">
			<h1>LIACS Student Project Manager</h1>
            <?php
                if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
                    header("Location: index.php");
                    exit;
                }
                $username = $_SESSION["username"];
                $class = $_SESSION["class"];
                // After sending an e-mail
                if (isset($_SESSION["regErr"])) {
                    echo $_SESSION["regErr"];
                }
                unset($_SESSION["regErr"]);
                if ($class == "Admin") {
                    //List all students with their projects
                    $result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress, Supervisor.SupName FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName LEFT JOIN Supervisor ON Project.SupID=Supervisor.SupID");
                     
                    echo "<h3>Student overview</h3>
                            <table class=\"list\" id='admin_table'>"; // start a table tag in the HTML
                
                    // column names
                    echo "<tr><th onclick=\"sortTable(0, 'admin_table')\">Name</th>
                                <th onclick=\"sortTable(1, 'admin_table')\">Project</th>
                                <th onclick=\"sortTable(2, 'admin_table')\">Progress</th>
                                <th onclick=\"sortTable(3, 'admin_table')\">First Supervisor</th>
                                <th onclick=\"sortTable(4, 'admin_table')\">Second Supervisor</th>
                                <th onclick=\"sortTable(5, 'admin_table')\">Student ID</th>
                                <th onclick=\"sortTable(6, 'admin_table')\">E-mail</th>
                                <th onclick=\"sortTable(7, 'admin_table')\">Telephone</th>
                                </tr>";
                    // rows of the database
                    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                        $SupName1 ="";
                        $SupName2 ="";
                        $supervisorresult = query_our_database("SELECT * FROM Supervises WHERE StuID='".$row['StuID']."' AND (Accepted='1' OR Accepted='0')");
                        while($suprow = mysqli_fetch_array($supervisorresult)){
                            if($suprow['type'] == "First Supervisor"){
                                $NameRow1 = mysqli_fetch_array(query_our_database("SELECT SupName FROM Supervisor WHERE SupID='".$suprow['SupID']."'"));
                                if($suprow['Accepted'] == '1')
                                   $SupName1 = $NameRow1['SupName'];
                                else
                                   $SupName1 = $NameRow1['SupName'] . " (not accepted yet)";
                            }
                            if($suprow['type'] == "Second Supervisor"){
                                $NameRow2 = mysqli_fetch_array(query_our_database("SELECT SupName FROM Supervisor WHERE SupID='".$suprow['SupID']."'"));
                                if($suprow['Accepted'] == '1')
                                    $SupName2 = $NameRow2['SupName'];
                                else
                                    $SupName2 = $NameRow2['SupName'] . " (not accepted yet)";
                            }
                        }
                        echo "<tr><td>" . $row['StuName'] . "</td>
                                    <td>" . $row['ProjectName'] . "</td>
                                    <td>" . $row['Progress'] . "</td>
                                    <td>" . $SupName1 . "</td>
                                    <td>" . $SupName2 . "</td>
                                    <td>" . $row['StuID'] . "</td>
                                    <td>" . $row['StuEMAIL'] . "</td>
                                    <td>" . $row['StuTel'] . "</td></tr>";  //$row['index'] the index here is a field name
                    }
                    echo "</table><br>"; //Close the table in HTML
                }
                if ($class == "Supervisor") {
                    //List assigned students and their projects
                    //$result = query_our_database("SELECT Student.StuID, Student.StuName, Student.StuEMAIL, Student.StuTel, Does.ProjectName, Project.Progress FROM Student LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE Project.SupID='".$_SESSION["ID"]."'");
                    $result = query_our_database("SELECT Supervises.StuID, StuName, StuEMAIL, StuTel, PropAccept, StartPro, MidRev, ThesisSub, ThesisAcc, PresSched, type, Supervises.SupID, Supervises.Accepted as SupAccepted, Does.ProjectName, Does.Accepted as ProjectAccepted, Description, Progress FROM Student LEFT JOIN Supervises ON Student.StuID=Supervises.StuID LEFT JOIN Does ON Student.StuID=Does.StuID LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE Supervises.SupID='".$_SESSION["ID"]."' AND Supervises.Accepted='1' ORDER BY StuID");
                    if(mysqli_num_rows($result) > 0){
                        echo "<h3>Student overview</h3>
                                    <table class=\"list\" id='sup_table'>"; // start a table tag in the HTML
                    
                        // column names
                        echo "<tr><th onclick=\"sortTable(0, 'sup_table')\">Name</th>
                                <th onclick=\"sortTable(1, 'sup_table')\">First Supervisor</th>
                                <th onclick=\"sortTable(2, 'sup_table')\">Second Supervisor</th>
                                <th onclick=\"sortTable(3, 'sup_table')\">Project Name and Description</th>
                                <th onclick=\"sortTable(4, 'sup_table')\">Project Accepted</th>
                                <th onclick=\"sortTable(5, 'sup_table')\">Progress</th>
                                <th onclick=\"sortTable(6, 'sup_table')\">Student ID</th>
                                <th onclick=\"sortTable(7, 'sup_table')\">E-mail</th>
                                <th onclick=\"sortTable(8, 'sup_table')\">Phone Number</th>
                                </tr>";
                        // rows of the database
                        $prevStuID = "";
                        while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                            $SupName1 ="";
                            $SupName2 ="";
                            if (!($prevStuID == $row['StuID'])){//prevent duplicate rows
                                $supsresult = query_our_database("SELECT SupName, Supervises.SupID, Accepted, type FROM Supervises LEFT JOIN Supervisor ON Supervises.SupID=Supervisor.SupID WHERE StuID='".$row['StuID']."' AND (Accepted='0' OR Accepted='1')");
                                while($suprow = mysqli_fetch_array($supsresult)){
                                    if($suprow['type'] == "First Supervisor"){
                                        if($suprow['SupID'] == $_SESSION["ID"])
                                            $SupName1 = "You";
                                        else{
                                            if($suprow['Accepted']=='1')
                                                $SupName1 = $suprow['SupName'];
                                            else
                                                $SupName1 = $suprow['SupName']." (not accepted yet)";
                                        }
                                    }
                                    if($suprow['type'] == "Second Supervisor"){
                                        if($suprow['SupID'] == $_SESSION["ID"])
                                            $SupName2 = "You";
                                        else{
                                            if($suprow['Accepted']=='1')
                                                $SupName2 = $suprow['SupName'];
                                            else
                                                $SupName2 = $suprow['SupName']." (not accepted yet)";
                                        }
                                    }
                                }
                                $pAccepted = "";
                                //$prog = "";
                                if ($row['ProjectAccepted'] == '1'){
                                    $pAccepted = "Yes";
                                    //$prog = $row['Progress'];
                                }
                                else if ($row['ProjectAccepted'] == '0'){
                                    $pAccepted = "No";
                                }
                                if(preg_match('/True/', $row['PropAccept']))
                                    $p1 = "True";
                                else
                                    $p1 = "False";
                                if(preg_match('/True/', $row['StartPro']))
                                    $p2 = "True";
                                else
                                    $p2 = "False";
                                if(preg_match('/True/', $row['MidRev']))
                                    $p3 = "True";
                                else
                                    $p3 = "False";
                                if(preg_match('/True/', $row['ThesisSub']))
                                    $p4 = "True";
                                else
                                    $p4 = "False";
                                if(preg_match('/True/', $row['ThesisAcc']))
                                    $p5 = "True";
                                else
                                    $p5 = "False";
                                if(preg_match('/True/', $row['PresSched']))
                                    $p6 = "True";
                                else
                                    $p6 = "False";
                                echo "<tr><td>" . $row['StuName'] . "</td>
                                            <td>" . $SupName1 . "</td>
                                            <td>" . $SupName2 . "</td>
                                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                                            <td>$pAccepted</td>
                                            <td>Research&nbsp;proposal&nbsp;accepted: <b>$p1</b>
                                            </br>Started&nbsp;Project: <b>$p2</b>
                                            </br>Midterm&nbsp;review: <b>$p3</b>
                                            </br>Thesis&nbsp;submitted: <b>$p4</b>
                                            </br>Thesis&nbsp;accepted: <b>$p5</b>
                                            </br>Presentation&nbsp;scheduled: <b>$p6</b></td>
                                            <td>" . $row['StuID'] . "</td>
                                            <td>" . $row['StuEMAIL'] . "</td>
                                            <td>" . $row['StuTel'] . "</td></tr>";  //$row['index'] the index here is a field name
                            }
                            $prevStuID = $row['StuID'];
                        }
                        echo "</table><br>"; //Close the table in HTML  
                    }
                    $result = query_our_database("SELECT Project.ProjectName, Description, Does.StuID, StuName, StuEMAIL, StuTel, Progress FROM Project LEFT JOIN Does ON Project.ProjectName=Does.ProjectName LEFT JOIN Student ON Does.StuID=Student.StuID WHERE Project.SupID='".$_SESSION["ID"]."'");
                    
                    if(mysqli_num_rows($result) > 0){
                        echo "<h3>Project Overview</h3>
                                    <table class=\"list\" id='sup_table2'>"; // start a table tag in the HTML
                        
                        // column names
                        echo "<tr><th onclick=\"sortTable(0, 'sup_table2')\">Name and Description</th>
                                <th onclick=\"sortTable(1, 'sup_table2')\">Student Name</th>
                                <th onclick=\"sortTable(2, 'sup_table2')\">Student E-mail</th>
                                <th onclick=\"sortTable(3, 'sup_table2')\">Student Phone Number</th>
                                <th onclick=\"sortTable(4, 'sup_table2')\">Progress</th>
                                </tr>";
                        
                        
                        // rows of the database
                        while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                            echo "<tr><td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                                        <td>" . $row['StuName'] . "</td>
                                        <td>" . $row['StuEMAIL'] . "</td>
                                        <td>" . $row['StuTel'] . "</td>
                                        <td>" . $row['Progress'] . "</td></tr>";    //$row['index'] the index here is a field name
                        }
                        
                        echo "</table><br>"; //Close the table in HTML  
                    }
                }
                if ($class == "Internship Contact") {
                    $result = query_our_database("SELECT Project.ProjectName, Description, Does.StuID, StuName, StuEMAIL, StuTel, Progress, Pay, LocName, Location, StreetNr, Travel, Tnotes FROM Project LEFT JOIN Does ON Project.ProjectName=Does.ProjectName LEFT JOIN Student ON Does.StuID=Student.StuID LEFT JOIN Internship_of ON Project.ProjectName=Internship_of.ProjectName WHERE Project.IConID='".$_SESSION["ID"]."'");
                    if(mysqli_num_rows($result) > 0){
                        echo "<h3>Internship Overview</h3>
                                    <table class=\"list\" id='icon_table'>"; // start a table tag in the HTML
                        
                        // column names
                        echo "<tr><th onclick=\"sortTable(0, 'icon_table')\">Name and Description</th>
                                <th onclick=\"sortTable(1, 'icon_table')\">Student Name</th>
                                <th onclick=\"sortTable(2, 'icon_table')\">Student E-mail</th>
                                <th onclick=\"sortTable(3, 'icon_table')\">Student Phone Number</th>
                                <th onclick=\"sortTable(4, 'icon_table')\">Progress</th>
                                <th onclick=\"sortTable(5, 'icon_table')\">Location</th>
                                <th onclick=\"sortTable(6, 'icon_table')\">Pay</th>
                                <th onclick=\"sortTable(7, 'icon_table')\">Travel</th>
                                </tr>";
                        
                        
                        // rows of the database
                        while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                            echo "<tr><td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                                        <td>" . $row['StuName'] . "</td>
                                        <td>" . $row['StuEMAIL'] . "</td>
                                        <td>" . $row['StuTel'] . "</td>
                                        <td>" . $row['Progress'] . "</td>
                                        <td>" . $row['Location']. " " . $row['StreetNr'] . ", " . $row['LocName'] . "</td>
                                        <td>" . $row['Pay'] . "</td>";
                                        if($row["Travel"] == 1)
                                            echo "<td>Travel, </br>" . $row['Tnotes'] . "</td>";
                                        else
                                            echo "<td>No Travel Compensation</td>";
                                        echo "</tr>";   //$row['index'] the index here is a field name
                        }
                        
                        echo "</table><br>"; //Close the table in HTML  
                    }
                }
                if ($class == "Student") {
                    //Show your project and supervisors
                    $result = query_our_database("SELECT Does.ProjectName, Does.Accepted, Project.Description, Project.Progress, Project.Time, Project.Internship, Project.SupID, Project.IConID FROM Does LEFT JOIN Project ON Does.ProjectName=Project.ProjectName WHERE StuID='".$_SESSION["ID"]."' AND (Does.Accepted='0' OR Does.Accepted='1')");
                    $row = mysqli_fetch_array($result);
                    echo "<h2>My project</h2>";
                    if ($row['ProjectName'] != "" && $row['Accepted'] == "1") {
                        $_SESSION["ProjectName"] = $row["ProjectName"];
                        $type = $row["Internship"];//1 is internship
                        if($type == "1"){
                            $result2 = query_our_database("SELECT * FROM Internship_of WHERE ProjectName='".$row["ProjectName"]."'");
                            $rowinterinfo = mysqli_fetch_array($result2);
                            $result3 = query_our_database("SELECT * FROM Internship_Contact WHERE IConID='".$row["IConID"]."'");
                            $rowcontactinfo = mysqli_fetch_array($result3);
                        }
                        else{
                            $result2 = query_our_database("SELECT * FROM Supervisor WHERE SupID='".$row["SupID"]."'");
                            $rowcontactinfo = mysqli_fetch_array($result2);
                            if ($row["SupID"] == NULL)
                                $type = "2"; //2 is project made by student
                        }
                        echo "<table class=\"list\" id='student_table'>
                            <tr>";
                        
                        if($type == "2"){
                            echo "<th onclick=\"sortTable(0, 'student_table')\">Name and Description</th>
                                  <th onclick=\"sortTable(1, 'student_table')\">Time</th>
                                  <th onclick=\"sortTable(2, 'student_table')\">Project Owner Name</th>
                                  <th onclick=\"sortTable(3, 'student_table')\">Type</th>
                            </tr>
                            <tr>
                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                            <td>" . $row['Time'] . "</td>
                            <td>" . $_SESSION['username'] . "</td>
                            <td>University Project</td>
                            </tr>";
                        }
                        else if($type == "1"){
                            echo "<th onclick=\"sortTable(0, 'student_table')\">Name and Description</th>
                                  <th onclick=\"sortTable(1, 'student_table')\">Time</th>
                                  <th onclick=\"sortTable(2, 'student_table')\">Project Owner Name</th>
                                  <th onclick=\"sortTable(3, 'student_table')\">Owner Email</th>
                                  <th onclick=\"sortTable(4, 'student_table')\">Owner Phone Number</th>
                                  <th onclick=\"sortTable(5, 'student_table')\">Type</th>
                                  <th onclick=\"sortTable(6, 'student_table')\">Company Name</th>
                                  <th onclick=\"sortTable(7, 'student_table')\">City</th>
                                  <th onclick=\"sortTable(8, 'student_table')\">Street</th>
                                  <th onclick=\"sortTable(9, 'student_table')\">Nr</th>
                                  <th onclick=\"sortTable(10, 'student_table')\">Travel</th>
                                  <th onclick=\"sortTable(11, 'student_table')\">Pay</th>
                            </tr>
                            <tr>
                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                            <td>" . $row['Time'] . "</td>
                            <td>" . $rowcontactinfo['IConName'] . "</td>
                            <td>" . $rowcontactinfo['IConEMAIL'] . "</td>
                            <td>" . $rowcontactinfo['IConTel'] . "</td>
                            <td>Internship</td>
                            <td>" . $rowinterinfo['CompanyName'] . "</td>
                            <td>" . $rowinterinfo['LocName'] . "</td>
                            <td>" . $rowinterinfo['Location'] . "</td>
                            <td>" . $rowinterinfo['StreetNr'] . "</td>";
                            if($rowinterinfo["Travel"] == 1)
                                echo "<td>Travel, </br>" . $rowinterinfo['Tnotes'] . "</td>";
                            else
                                echo "<td>No Travel Compensation</td>";
                            echo "<td>" . $rowinterinfo['Pay'] . "</td>
                            </tr>";
                        }
                        else{
                            echo "<th onclick=\"sortTable(0, 'student_table')\">Name and Description</th>
                                  <th onclick=\"sortTable(1, 'student_table')\">Time</th>
                                  <th onclick=\"sortTable(2, 'student_table')\">Project Owner Name</th>
                                  <th onclick=\"sortTable(3, 'student_table')\">Owner Email</th>
                                  <th onclick=\"sortTable(4, 'student_table')\">Owner Phone Number</th>
                                  <th onclick=\"sortTable(5, 'student_table')\">Owner Topics</th>
                                  <th onclick=\"sortTable(6, 'student_table')\">Type</th>
                            </tr>
                            <tr>
                            <td><b>" . $row['ProjectName'] . "</b><p style='margin-left: 5px'>" . $row['Description'] . "</p></td>
                            <td>" . $row['Time'] . "</td>
                            <td>" . $rowcontactinfo['SupName'] . "</td>
                            <td>" . $rowcontactinfo['SupEMAIL'] . "</td>
                            <td>" . $rowcontactinfo['SupTel'] . "</td>
                            <td>" . $rowcontactinfo['Topics'] . "</td>
                            <td>University Project</td>
                            </tr>";
                        }
                        echo "</table>";
                        
                        echo "<form action=\"$self\" method=\"post\">
                                <input type=\"hidden\" name=\"projname\" value=\"".$row['ProjectName']."\">
                                <input type=\"submit\" name=\"unsubproject\" value=\"Unsubscribe from project\">
                              </form>";
                        
                        echo "<h3>Progress:</h3>";
                        $prog = $row['Progress'];
                        $result = query_our_database("SELECT * FROM Project WHERE ProjectName='".$_SESSION["ProjectName"]."'");
                        $row = mysqli_fetch_row($result);
                        $check = array("", "", "", "", "", ""); // Keeps track of previously checked checkboxes
                        if (preg_match('/True/',$row[11]))
                            $check[0] = "checked";
                        if (preg_match('/True/',$row[12]))
                            $check[1] = "checked";
                        if (preg_match('/True/',$row[13]))
                            $check[2] = "checked";
                        if (preg_match('/True/',$row[14]))
                            $check[3] = "checked";
                        if (preg_match('/True/',$row[15]))
                            $check[4] = "checked";
                        if (preg_match('/True/',$row[16]))
                            $check[5] = "checked";
                        echo "<form action=\"$self\" method=\"post\">
                            <textarea name=\"progupdate\" rows=\"5\" cols=\"40\"></textarea>
                            </br>
                            <td><input type=\"checkbox\" name=\"newcheck0\" $check[0]>Research proposal accepted</td></br>
                            <td><input type=\"checkbox\" name=\"newcheck1\" $check[1]>Started project</td></br>
                            <td><input type=\"checkbox\" name=\"newcheck2\" $check[2]>Midterm review</td></br>
                            <td><input type=\"checkbox\" name=\"newcheck3\" $check[3]>Thesis submitted</td></br>
                            <td><input type=\"checkbox\" name=\"newcheck4\" $check[4]>Thesis accepted</td></br>
                            <td><input type=\"checkbox\" name=\"newcheck5\" $check[5]>Presentation scheduled</td></br>
                            </br>
                            <input type=\"submit\" name=\"progressupdate\" value=\"Update Your Progress\">
                        </form>
                        <span class=\"error\">$progressupdated</span>"; 
                    }
                    else {
                        if($row['ProjectName'] != "") {
                            echo "You made a request to join the project: ".$row['ProjectName'].".
                                  <form action=\"$self\" method=\"post\">
                                    <input type=\"submit\" name=\"delreq\" value=\"Delete request\">
                                  </form>";
                        }
                        else
                            echo "You currently have no project.<br>";
                    }
                    $result = query_our_database("SELECT SupID, Accepted FROM Supervises WHERE StuID='".$_SESSION["ID"]."' AND type='First SuperVisor'");
                    //first supervisor
                    $found = false;
                    while ($row = mysqli_fetch_array($result)){
                        if ($row['Accepted'] == "0" || $row['Accepted'] == "1"){
                            $result2 = query_our_database("SELECT * FROM Supervisor WHERE SupID='".$row['SupID']."'");
                            $rowcontact = mysqli_fetch_array($result2);
                            $found = true;
                            echo "<h3>First supervisor: " . $rowcontact['SupName'];
                            if($row['Accepted'] == "1")
                                echo "</h3>";
                            else
                                echo " (not confirmed yet)</h3>";
                            echo "<p style='margin-left: 5px'>E-mail: " . $rowcontact['SupEMAIL'] . "</p>";
                            echo "<p style='margin-left: 5px'>Telephone: " . $rowcontact['SupTel'] . "</p>";
                            echo "<a><form action=\"$self\" method=\"post\">
                                     <input type=\"submit\" name=\"delwarn1\" value=\"Delete first supervisor\">
                                     </form></a>";
                            break;
                        }
                    }
                    if (!$found)
                        echo "<h3>First supervisor: -</h3>";
                    $result = query_our_database("SELECT SupID, Accepted FROM Supervises WHERE StuID='".$_SESSION["ID"]."' AND type='Second SuperVisor'");
                    //second supervisor
                    $found = false;
                    while ($row = mysqli_fetch_array($result)){
                        if ($row['Accepted'] == "0" || $row['Accepted'] == "1"){
                            $result2 = query_our_database("SELECT * FROM Supervisor WHERE SupID='".$row['SupID']."'");
                            $rowcontact = mysqli_fetch_array($result2);
                            $found = true;
                            echo "<h3>Second supervisor: " . $rowcontact['SupName'];
                            if($row['Accepted'] == "1")
                                echo "</h3>";
                            else
                                echo " (not confirmed yet)</h3>";
                            echo "<p style='margin-left: 5px'>E-mail: " . $rowcontact['SupEMAIL'] . "</p>";
                            echo "<p style='margin-left: 5px'>Telephone: " . $rowcontact['SupTel'] . "</p>";
                            echo "<a><form action=\"$self\" method=\"post\">
                                     <input type=\"submit\" name=\"delwarn2\" value=\"Delete second supervisor\">
                                     </form></a>";
                            break;
                        }
                    }
                    if (!$found)
                        echo "<h3>Second supervisor: -</h3>";
                    
                }
            
            ?>

        </div>

    </body>
</html> 
