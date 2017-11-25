<?php 
    session_start();
    if(empty($_SESSION["ID"])){
        header("Location: index.php");
        die();
    }        
    require_once "sidebar_selector.php";
    require_once "general_functions.php";
    $configs = include("config.php");
    $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }    
    $class = $_SESSION["class"];
    $id = $_SESSION["ID"];
        
    $result = mysqli_query($con, "SELECT * FROM InternshipApp_Users WHERE Identifier='$id'");
    $row = mysqli_fetch_array($result);
    $hashedpass = $row["Password"];
    $name = $row["Name"];
        
    $newemailErr = $newtelErr = $newpassErr = $newpassverErr = $currentpassErr = $newtopics = "";
    $newemail = $newtel = $newpass = $newpassver = $currentpass = $newtopicsErr ="";
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
    
        //password update section
        if (!empty($_POST["passupdate"])){
            $newpass = $_POST["newpassword"];
            $newpassver = $_POST["newpasswordver"];
            $currentpass = $_POST["passwordcheck"];
            if(empty($newpass)){
                $newpassErr = "Enter a new password";
            }
            else if($newpass != $newpassver){
                $newpassverErr = "Passwords don't match";
            }
            else if($currentpass != $hashedpass){//if(hashfunctionTODO($currentpass) != $hashedpass){
                $currentpassErr = "Wrong password";
            }
            else {
                $newpass = test_input($newpass);
                if(strlen($newpass) > 60)
                    $newpassErr = "Input too big, max 60 characters";
            }
            if( $newpassErr =="" && $newpassverErr == "" && $currentpassErr ==""){
                $hashednewpass = $newpass;//hashfunctionTODO($newpass);
                $stmt = mysqli_prepare($con, "UPDATE InternshipApp_Users SET Password=? WHERE Identifier='$id'");
                mysqli_bind_param($stmt, 's', $hashednewpass);
                mysqli_stmt_execute($stmt);
                $numrow = mysqli_affected_rows($con);
                mysqli_stmt_close($stmt);
                if($numrow != 1){
                    die("Error updating password");
                }
            }
        }

        //phone update section
        if (!empty($_POST["telupdate"])){
            $newtel = $_POST["newtelnum"];
            if(empty($newtel)){
                $newtelErr = "Enter a phone number";
            }
            else {
                $newtel = test_input($newtel);
                if(strlen($newtel) > 10) {
                    $newtelErr = "Needs to be 10 or fewer digits";
                }
                if (!ctype_digit($newtel)){
                    $newtelErr = "Digits only";
                }
            }
            if($newtelErr ==""){
                $stmt = "";
                if($class == "Student")
                    $stmt = mysqli_prepare($con, "UPDATE Student SET StuTel=? WHERE StuID='$id'");
                if($class == "Supervisor")
                    $stmt = mysqli_prepare($con, "UPDATE Supervisor SET SupTel=? WHERE SupID='$id'");
                if($class == "Internship Contact")
                    $stmt = mysqli_prepare($con, "UPDATE Internship_Contact SET IConTel=? WHERE IConID='$id'");
                mysqli_bind_param($stmt, 's', $newtel);
                mysqli_stmt_execute($stmt);
                $numrow = mysqli_affected_rows($con);
                mysqli_stmt_close($stmt);
                if($numrow != 1){
                    die("Error updating phone number");
                }
            }
        }

        //email update section
        if (!empty($_POST["emailupdate"])){
            $newemail = $_POST["newemailaddress"];
            if(empty($newemail)){
                $newemailErr = "Enter a new email address";
            }
            else {
                $newemail = test_input($newemail);
                if(strlen($newemail) > 50) {
                    $newemailErr = "Email address too big, max 50 characters";
                }
                if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
                    $newemailErr = "Invalid email format"; // W3Schools
                }
            }
            if($newemailErr ==""){
                $stmt = "";
                if($class == "Student")
                    $stmt = mysqli_prepare($con, "UPDATE Student SET StuEMAIL=? WHERE StuID='$id'");
                if($class == "Supervisor")
                    $stmt = mysqli_prepare($con, "UPDATE Supervisor SET SupEMAIL=? WHERE SupID='$id'");
                if($class == "Internship Contact")
                    $stmt = mysqli_prepare($con, "UPDATE Internship_Contact SET IConEMAIL=? WHERE IConID='$id'");
                mysqli_bind_param($stmt, 's', $newemail);
                mysqli_stmt_execute($stmt);
                $numrow = mysqli_affected_rows($con);
                mysqli_stmt_close($stmt);
                if($numrow != 1){
                    die("Error updating email address");
                }
            }
        }
        
        //topics update section
        if (!empty($_POST["topicsupdate"])){
            $newtopics = $_POST["newsuptopics"];
            if(empty($newtopics)){
                $newtopicsErr = "Enter new topics";
            }
            else {
                $newtopics = test_input($newtopics);
                if(strlen($newtopics) > 144) {
                    $newtopicsErr = "No bigger than a tweet, 144 characters";
                }
            }
            if($newtopicsErr ==""){
                $stmt = mysqli_prepare($con, "UPDATE Supervisor SET Topics=? WHERE SupID='$id'");
                mysqli_bind_param($stmt, 's', $newtopics);
                mysqli_stmt_execute($stmt);
                $numrow = mysqli_affected_rows($con);
                mysqli_stmt_close($stmt);
                if($numrow != 1){
                    die("Error updating topics");
                }
            }
        }
    
    }//if ($_SERVER["REQUEST_METHOD"] == "POST")
    
?>
<!DOCTYPE html>
<html lang="en-UK">
	<head>
		<meta charset="utf-8" /> 
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Profile Information - LIACS Student Project Manager</title>
	</head>
    <body>
        <div class="main">
        <?php
        if($class != "Admin"){
            echo "<h3>Hello $name, this is your profile information</h3>";
            echo "<table class=\"list\">";
        }
        if ($class == "Supervisor"){
            echo "<tr>
                    <th>Name</th><th>Email</th><th>PhoneNumber</th><th>Topics</th><th>Allow First?</th><th>Allow Second?</th><th>Background</th>
                  </tr>";
            $ressup = mysqli_query($con, "SELECT * FROM Supervisor WHERE SupID='$id'");
            $rowsup = mysqli_fetch_array($ressup);
            echo "<tr>
                    <td>".$rowsup["SupName"]."</td><td>".$rowsup["SupEMAIL"]."</td><td>".$rowsup["SupTel"]."</td><td>".$rowsup["Topics"]."</td><td>".$rowsup["RoleFirst"]."</td><td>".$rowsup["RoleSecond"]."</td><td>".$rowsup["Background"]."</td>
                  </tr>";
            echo "</table>";      
            
        }
        else if ($class == "Internship Contact"){
            echo "<tr>
                    <th>Name</th><th>Email</th><th>PhoneNumber</th><th>Company Name</th>
                  </tr>";
            $resicon = mysqli_query($con, "SELECT * FROM Internship_Contact WHERE IConID='$id'");
            $rowicon = mysqli_fetch_array($resicon);
            echo "<tr>
                    <td>".$rowicon["IConName"]."</td><td>".$rowicon["IConEMAIL"]."</td><td>".$rowicon["IConTel"]."</td><td>".$rowicon["CompanyName"]."</td>
                  </tr>";
            echo "</table>";      
            
        }
        else if ($class == "Student"){
            echo "<tr>
                    <th>Name</th><th>Email</th><th>PhoneNumber</th>
                  </tr>";
            $resstu = mysqli_query($con, "SELECT * FROM Student WHERE StuID='$id'");
            $rowstu = mysqli_fetch_array($resstu);
            echo "<tr>
                    <td>".$rowstu["StuName"]."</td><td>".$rowstu["StuEMAIL"]."</td><td>".$rowstu["StuTel"]."</td>
                  </tr>";
            echo "</table>";      
            
            
        }
        
        mysqli_close($con);
        
        echo "<h3>Update your information</h3>";
        
        $temp = htmlspecialchars($_SERVER["PHP_SELF"]);
        
        if ($class != "Admin"){
            echo "<form action=\"$temp\" method=\"post\">
                <table class=\"form\">
                    <tr>
                        <td>New Email address:</td>
                        <td><input type=\"text\" name=\"newemailaddress\" value=\"\">
                        <span class=\"error\">$newemailErr</span></td>
                    </tr>
                </table>
                <input type=\"submit\" name=\"emailupdate\" value=\"Update Email\">
            </form>";
            
            echo "<form action=\"$temp\" method=\"post\">
                <table class=\"form\">
                    <tr>
                        <td>New Phone Number:</td>
                        <td><input type=\"text\" name=\"newtelnum\" value=\"\">
                        <span class=\"error\">$newtelErr</span></td>
                    </tr>
                </table>
                <input type=\"submit\" name=\"telupdate\" value=\"Update PhoneNumber\">
            </form>";
        }
        if ($class == "Supervisor"){
            echo "<form action=\"$temp\" method=\"post\">
                <table class=\"form\">
                    <tr>
                        <td>Update Topics:</td>
                        <td><input type=\"text\" name=\"newsuptopics\" value=\"\">
                        <span class=\"error\">$newtopicsErr</span></td>
                    </tr>
                </table>
                <input type=\"submit\" name=\"topicsupdate\" value=\"Update Topics\">
            </form>";
        }
        ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <table class="form">
                <tr>
                    <td>New Password:</td>
                    <td><input type="password" name="newpassword" value="">
                    <span class="error"><?php echo $newpassErr;?></span></td>
                </tr>
                <tr>
                    <td>Repeat New Password:</td>
                    <td><input type="password" name="newpasswordver" value="">
                    <span class="error"><?php echo $newpassverErr;?></span></td>
                </tr>
                <tr>
                    <td>Current Password:</td>
                    <td><input type="password" name="passwordcheck" value="">
                    <span class="error"><?php echo $currentpassErr;?></span></td>
                </tr>
            </table>
            <input type="submit" name="passupdate" value="Update Password">
        </form>
        </div>
    </body>
</html> 
