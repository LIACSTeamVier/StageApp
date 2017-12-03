<?php //file made with help from w3schools.com
session_start();
require_once "sidebar_supervisor.php";
//TODO put stuff in sessions when logging in, fix the vars in here to match the session, fix the stuff in here to match the correct database, and fix to match the table
///!!!! put correct stuff in the session

//test if the user is allowed to make a project   TODO put correct vars in session and check the correct values
if (($_SESSION["class"] != "Admin") && ($_SESSION["class"] != "Supervisor")){
	//redirect to main page
	header("Location: main_page.php");
	die();
}
else{
	// define variables and set to empty values
	$nameErr = $topicErr = $descriptionErr = $tijdrestErr = $sqaulErr = "";
	$name = $topic = $description = $tijdrest = $squal ="";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	    $configs = include("config.php");
		$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if (empty($_POST["name"])) {
			$nameErr = "Name is required";
		} else {
			$name = test_input($_POST["name"]);
                if(strlen($name) > 30)
                    $nameErr = "Input can be no more than 30 characters";

                //test if name is taken
                $stmt = mysqli_prepare($con, "SELECT * FROM Project p WHERE p.ProjectName = ?");
                mysqli_stmt_bind_param($stmt,'s', $name);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
                if (!$result){
                    echo "database error!";
                    die ('Unable to run query:' . mysqli_error());
                }
                else
                    $row = mysqli_fetch_row($result);
                if(!empty($row))
                    $nameErr = "Name is taken";
		}
		
		if (empty($_POST["topic"])) {
			$topic = "";
		} else {
			$topic = test_input($_POST["topic"]);
			if  (strlen($topic) > 127)
				$topicErr = "Input can be no more than 127 characters";
		}
		
		if (empty($_POST["description"])) {
			$descriptionErr = "A description of your internship is required";
		} else {
			$description = test_input($_POST["description"]);
		}
		
		if(empty($_POST["squal"])){
                $squal = "";
		}else{
			$squal =  test_input($_POST["squal"]);
		}
		if(empty($_POST["tijdrest"])){
			$tijdrest = "";
		}else{
			$tijdrest =  test_input($_POST["tijdrest"]);
		}
		
		if ( ($nameErr == "") && ($topicErr == "") && ($descriptionErr == "")
		 && ($squalErr =="") && ($tijdrestErr =="")){
			insertIntoDatabase($name, $topic, $description, $squal, $tijdrest, $con);
		}
		else
			mysqli_close($con);
	}
} //BRACKET FOR SESSION ROLE CHECK!

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function insertIntoDatabase($name, $topic, $description, $squal, $tijdrest, $con){	
	$docid = $_SESSION["ID"];//of haal het uit de begeleider tabel als alleen de naam in de sessie staat
	
	$stmt1 = mysqli_prepare($con,
	 "INSERT INTO Project(ProjectName, Description, Time, Studentqualities, Topic, Internship, SupID)
	 VALUES (?,?,?,?,?,'0',?)"); 
	mysqli_bind_param($stmt1, 'ssssss', $name, $description, $tijdrest, $squal, $topic, $docid);
	$result1 = mysqli_execute($stmt1);
	mysqli_close($stmt1);
	if (!$result1){
            header("Location: ".$_SERVER["PHP_SELF"]);
            die('Unable to run query:' . mysqli_error() );
	}

	
    mysqli_close($con);
    //redirect to main page
    header("Location: main_page.php");
    die();
}
?>
<!doctype html>

<html>
        <head>
        <meta charset="utf-8" /> 
          
        <meta name="Description" content= "Offer Project" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Offer Project</title>
    </head>
   <body>
     
    <!--<div class="sidepane">
       <a href="main_page.php">Overview</a>
       <a href="#">Projects</a>
       <a href="contact.html">Contact</a>
       <a href="#">Help</a></a>
    </div>-->
     
    <div class="main">
    <p>
        Fill in the forms to make your project information available to the
        students.
    </p>
    <p><span class="error">* required field.</span></p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="project">
        <table class="form">
        <tr>
        <td>ProjectName:</td><td><input type="text" name="name" value="<?php  echo $name;?>">
        <span class="error">* <?php echo $nameErr;?></span></td>
        </tr>
        <tr>
        <td>Topic/keywords:</td><td> <input type="text" name="topic" value="<?php  echo $topic;?>">
        <span class="error"><?php echo $topicErr;?></span></td>
        </tr>
        <tr>
        <td>Describe your project:</td>
        <td> <textarea name="description" rows="5" cols="40"><?php echo $description;?></textarea>
        <span class="error">* <?php echo $descriptionErr;?></span></td>
        </tr>
        <tr>
        <td>Describe the qualities you seek in a student (i.e. skillset):</td>
        <td>  <textarea name="squal" rows="5" cols="40"><?php echo $squal;?></textarea></td>
	<td><span class="error"> <?php echo $squalErr;?></span></td>
	</tr>
        <tr>
	<td>Describe time restriction (when the project is available):</td>
        <td>  <textarea name="tijdrest" rows="5" cols="40"><?php echo $tijdrest;?></textarea></td>
        <td><span class="error"> <?php echo $tijdrestErr;?></span></td>
	</tr>
        </table>
        <input type="submit" value="Post project">
    </form>
    </div>
   
   </body>
</html>

