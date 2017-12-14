<?php //file made with help from w3schools.com
session_start();
include 'sidebar_selector.php';

//TODO put stuff in sessions when logging in, fix the vars in here to match the session, fix the stuff in here to match the correct database, and fix to match the table
///!!!! put correct stuff in the session
//echo "Hello " . $_SESSION["name"] . $_SESSION["surname"]. ".<br>";
// define variables and set to empty values
$nameErr = $emailErr = $teleErr = $descriptionErr = "";
$name = $email = $tele = $location = $company = $description = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
    }
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $emailErr = "Invalid email format";
        }
    }
    if (empty($_POST["tele"])) {
        $tele = "";
    } else {
        $tele = test_input($_POST["tele"]); 
        if (!preg_match("/^[0-9 ]*$/",$tele)) {
              $teleErr = "Only numbers allowed";
        }       
    }
    if (empty($_POST["location"])) {
        $location = "";
    } else {
        $location = test_input($_POST["location"]); 
    }
    if (empty($_POST["company"])) {
        $company = "";
    } else {
        $company = test_input($_POST["company"]);
    }
    
    if (empty($_POST["description"])) {
        $descriptionErr = "A description of your internship is required";
    } else {
        $description = test_input($_POST["description"]);
    }
    if ( ($nameErr == "") && ($emailErr == "") && ($teleErr == "")
	 && ($descriptionErr =="")){
        insertIntoDatabase($name, $email, $tele, $location, $company, $description);
    }
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function insertIntoDatabase($name, $email, $tele, $location, $company, $description){
    $con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "9sdu8kG09u", "s1551396");
    // Check connection
    if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	//$stmt = $con->prepare("INSERT INTO Stage (name, email, phone, location, company, description) VALUES (?,?,?,?,?,?)");
	//$stmt->bind_param("ssssss", $name, $email, $tele, $location, $company, $description);
	
	//$stmt->execute(); //ipv de stukjes hieronder
    $statement = "INSERT INTO Stage (name, email, phone, location, company, description) VALUES ('$name', '$email','$tele','$location','$company','$description')";
    $qr = "INSERT INTO Stage (name, email, phone, location, company, description) VALUES ('test4', 'testmail', '012344', 'test', 'test', 'testestststeste')";
    $result = mysqli_query($con, $statement);
//or die('Unable to run query:' . mysqli_error());
    if (!$result){
	echo "database error!";
    	die ('Unable to run query:' . mysqli_error());
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
          
        <meta name="Description" content= "Page To Offer Internships" />
        <link rel="stylesheet" type="text/css" href="style.css">
        </style>
        <title>Submit internship - LIACS Student Project Manager</title>
    </head>
   <body>
   <div class="main">
	<h1>LIACS Student Project Manager</h1>
    <p>
        Fill in this form to make your internship information available to the
        students.
    </p>
    <p><span class="error">* Required field.</span></p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="stage">
        <table class="form">
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" value="<?php  echo $name;?>">
				<span class="error">* <?php echo $nameErr;?></span></td>
			</tr>
			<tr>
				<td>Email address:</td>
				<td><input type="text" name="email" value="<?php echo $email;?>">
				<span class="error">* <?php echo $emailErr;?></span></td>
			</tr>
			<tr>
				<td>Phone number:</td>
				<td><input type="text" name="tele" value="<?php echo $tele;?>">
				<span class="error"> <?php echo $teleErr;?></span></td>
			</tr>
			<tr>
				<td>Location:</td>
				<td><input type="text" name="location" value="<?php echo $location;?>"></td>
			</tr>
			<tr>
				<td>Company name:</td>
				<td><input type="text" name="company" value=<?php echo $company;?>></td>
			</tr>
			<tr>
				<td>Internship description:</td>
			</tr>
		</table>
		<textarea name="description" rows="5" cols="40"><?php echo $description;?></textarea>
        <span class="error">* <?php echo $descriptionErr;?></span>
        <br><br>
        <input type="submit" value="Post internship">
    </form>
   </div>
   </body>
</html>
