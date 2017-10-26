<?php //file made with help from w3schools.com

session_start();

//TODO put stuff in sessions when logging in, fix the vars in here to match the session, fix the stuff in here to match the correct database, and fix to match the table

///!!!! put correct stuff in the session
//echo "Hello " . $_SESSION["name"] . $_SESSION["surname"]. ".<br>";
echo "Hello ". $_SESSION["uname"]".<br>";
//test if the user is allowed to make a project   TODO put correct vars in session and check the correct values
if (($_SESSION["class"] != "Admin") && ($_SESSION["class"] != "begeleider")){
	//redirect to main page
	header("Location: main_page.php");
	die();
}
else{
// define variables and set to empty values
$nameErr = $emailErr = $teleErr = $descriptionErr = "";
$name = $email = $tele = $location = $room = $field = $description = "";


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

    if (empty($_POST["room"])) {
        $room = "";
    } else {
        $room = test_input($_POST["room"]); 
    }

    if (empty($_POST["field"])) {
        $field = "";
    } else {
        $field = test_input($_POST["field"]);
    }
    
    if (empty($_POST["description"])) {
        $descriptionErr = "A description of your internship is required";
    } else {
        $description = test_input($_POST["description"]);
    }



    if ( ($nameErr == "") && ($emailErr == "") && ($teleErr == "")
	 && ($descriptionErr =="")){
        insertIntoDatabase($name, $email, $tele, $location, $room ,$field, $description);
    }
}
} //BRACKET FOR SESSION ROLE CHECK!

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function insertIntoDatabase($name, $email, $tele, $location, $room , $field, $description){
    $con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1553968", "-", "s1553968");
    ///change into this maybe later $con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "-", "s1551396");
    // Check connection
    if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    //$statement = "INSERT INTO Project (name, email, phone, location, room, field, description) VALUES ('$name', '$email','$tele','$location','$room', '$field','$description')";
    $stmt = mysqli_prepare($con, "INSERT INTO Project (name, email, phone, location, room, field, description) VALUES (?,?,?,?,?,?,?)");
    //change type of parameters in future if needed for db
    mysqli_stmt_bind_param($stmt, 'sssssss', $name, $email, $tele, $location, $room, $field, $description);
    //check if true or false
    $result = mysqli_stmt_execute($stmt);
    //$result = mysqli_stmt_get_result($stmt);


//$qr = "INSERT INTO Stage (name, email, phone, location, field, description) VALUES ('test4', 'testmail', '012344', 'test', 'test', 'testestststeste')";
//    $result = mysqli_query($con, $statement);
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
          
        <meta name="Description" content= "Page To Offer Projects" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Page To Offer Projects</title>
    </head>
   <body>
     
    <div class="sidepane">
       <a href="#">Overview</a>
       <a href="#">Projects</a>
       <a href="#">Contact</a>
       <a href="#">Help</a></a>
    </div>
     
    <div class="main">
    <p>
        Fill in the forms to make your project information available to the
        students.
    </p>
    <p><span class="error">* required field.</span></p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="project">
        Name: <input type="text" name="name" value="<?php  echo $name;?>">
        <span class="error">* <?php echo $nameErr;?></span>
        <br><br>
        E-mail: <input type="text" name="email" value="<?php echo $email;?>">
        <span class="error">* <?php echo $emailErr;?></span>
        <br><br>
        Phone number: <input type="text" name="tele" value="<?php echo $tele;?>">
        <span class="error"> <?php echo $teleErr;?></span>
        <br><br>
        Relevant university building: <input type="text" name="location" value="<?php echo $location;?>">
        Room number: <input type="text" name="room" value="<?php echo $room;?>">
        <br><br>
	    Field of study: <input type="text" name="field" value=<?php echo $field;?>>
	<br><br>
        Describe your project: <textarea name="description" rows="5" cols="40"><?php echo $description;?></textarea>
        <span class="error">* <?php echo $descriptionErr;?></span>
        <br><br>
        <input type="submit" value="Post project">
    </form>
    </div>
   
   </body>
</html>

