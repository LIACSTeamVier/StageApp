<?php
    session_start();
    require_once "sidebar_selector.php";
    require_once "general_functions.php";
    
    $descErr = $topicErr = "";
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["updateproject"]))
            updateProject();
        $result = query_our_database("SELECT ProjectName, Description, Time, Studentqualities, Topic FROM Project WHERE ProjectName='".$_POST["projname"]."'");
        $row = mysqli_fetch_array($result);
    }
    else {
        header("Location: main_page.php");
        exit;
    }
    
    function updateProject() {
        global $descErr, $topicErr;
        $description = $time = $squal = $topic = "";
        $error = False;
        
        if (empty($_POST["description"])) {
            $descErr = "Description is required";
            $error = True;
        } else {
            $description = test_input($_POST["description"]); 
        }
        
        $time = test_input($_POST["time"]);
        $squal = test_input($_POST["squal"]);

        $topic = test_input($_POST["topic"]); 
        if(strlen($topic) > 127) {
            $topicErr = "No more than 127 characters";
            $error = True;
        }
        
        if (!$error) {
            $configs = include("config.php");
            $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
            // Check connection
            if (mysqli_connect_errno()) {
                $_SESSION["regErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
                header("Location: main_page.php");
                exit;
            }
            else {
                $stmt2 = mysqli_prepare($con, "UPDATE Project SET Description = ?, Time = ?, Studentqualities = ?, Topic = ? WHERE ProjectName = ?");
	            mysqli_stmt_bind_param($stmt2,'sssss', $description, $time, $squal, $topic, $_POST['projname']);
	            $result2 = mysqli_execute($stmt2);
	            mysqli_close($stmt2);
                mysqli_close($con);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="utf-8" />
    <meta name="Description" content= "InternshipApp login" />
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="sortTable.js"></script>
    <title>Edit a project - LIACS Student Project Manager</title>
</head>
<body>

<div class="main">
    <h1>LIACS Student Project Manager</h1>
    <h3>Edit a project</h3>
    
    <table class="list" id='project_table'>
      <tr>
        <th>Name and Description</th>
        <th>Time</th>
        <th>Studentqualities</th>
        <th>Topic</th>
      </tr>
      <tr>
        <td><b><?php echo $row['ProjectName'];?></b><p style='margin-left: 5px'><?php echo $row['Description'];?></p></td>
        <td><?php echo $row['Time'];?></td>
        <td><?php echo $row['Studentqualities'];?></td>
        <td><?php echo $row['Topic'];?></td>
      </tr>
    </table>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      <table class="form">
        <tr>
          <td>Description:</td>
          <td><textarea name="description" rows="5" cols="40"><?php echo $row['Description'];?></textarea></td>
	      <td><span class="error"><?php echo $descErr;?></span></td>
        </tr>
        <tr>
		  <td>Time:</td>
		  <td><textarea name="time" rows="5" cols="40"><?php echo $row['Time'];?></textarea></td>
        </tr>
        <tr>
		  <td>Studentqualities:</td>
		  <td><textarea name="squal" rows="5" cols="40"><?php echo $row['Studentqualities'];?></textarea></td>
        </tr>
        <tr>
		  <td>Topic:</td>
		  <td><input type="text" name="topic" value="<?php echo $row['Topic'];?>"></td>
		  <td><span class="error"><?php echo $topicErr;?></span></td>
        </tr>
      </table>
      <input type="hidden" name ="projname" value="<?php echo $_POST['projname'];?>">
      <input type="submit" name="updateproject" value="Update project">
    </form>
</div>
</body>
</html>
