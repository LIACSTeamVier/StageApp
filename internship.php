<?php //file made with help from w3schools.com
    session_start();
    require_once "general_functions.php";
    require_once "sidebar_selector.php";
    //TODO put stuff in sessions when logging in, fix the vars in here to match the session, fix the stuff in here to match the correct database, and fix to match the table
    ///!!!! put correct stuff in the session

    $locationErr = $streetErr = $streetnrErr = $payErr = $tnotesErr = $naamErr = $topicErr = $tijdrestErr = $squalErr = $descriptionErr = "";
    $location = $street = $streetnr = $pay = $travel = $tnotes = $description = $tijdrest = $naam = $topic = $squal = "";

    //test if the user is allowed to make a project   TODO put correct vars in session and check the correct values
    if (($_SESSION["class"] != "Admin") && ($_SESSION["class"] != "InternshipInstructor") && ($_SESSION["class"] != "Internship Contact")){
        //redirect to main page
        header("Location: main_page.php");
        die();
    }
    else{
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $configs = include("config.php");
            $con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
        
            if (empty($_POST["naam"])) {
                $naamErr = "Name is required";
            } else {
                $naam = test_input($_POST["naam"]);
                if(strlen($naam) > 30)
                    $naamErr = "Input can be no more than 30 characters";

                //test if name is taken
                $stmt = mysqli_prepare($con, "SELECT * FROM Project p WHERE p.ProjectName = ?");
                mysqli_stmt_bind_param($stmt,'s', $naam);
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
                    $naamErr = "Name is taken";
            }

            if (empty($_POST["topic"])) {
                $topic = "";
            } else {
                $topic = test_input($_POST["topic"]);
                if(strlen($topic) > 127)
                    $topicErr = "Input can be no more than 127 characters";
            }
		
            if (empty($_POST["location"])) {
                $locationErr = "Location is required";
            } else {
                $location = test_input($_POST["location"]); 
                if(strlen($location) > 30)
                    $locationErr = "Input can be no more than 30 characters";
            }

            if(empty($_POST["street"])){
                $street = "";
            }else{
                $street = test_input($_POST["street"]);
                if(strlen($street) > 30)
                    $streetErr = "Input can be no more than 30 characters";
            }

            if(empty($_POST["streetnr"])){
                $streetnr = "";
            }else{
                $streetnr = test_input($_POST["streetnr"]);
                if(strlen($streetnr) > 30)
                    $streetnrErr = "Input can be no more than 30 characters";
            }

            if(empty($_POST["pay"])){
                $pay = "";
            }else{
                $pay = test_input($_POST["pay"]);
                if(strlen($pay) > 30)
                    $payErr = "Input can be no more than 30 characters";
            }

            if(empty($_POST["tnotes"])){
                $tnotes = "";
            }else{
                $tnotes = test_input($_POST["tnotes"]);
                if(strlen($tnotes) > 30)
                    $tnotesErr = "Input can be no more than 30 characters";
            }
            if(empty($_POST["travel"])){
                $travel = NULL;
            }else{
                $travel =  test_input($_POST["travel"]);
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

            if ( ($naamErr == "") && ($topicErr == "") && ($locationErr == "") && ($streetErr == "") && ($streetnrErr == "") 
                 && ($payErr == "")  && ($tnotesErr == "") && ($descriptionErr =="") && ($squalErr == "") && ($tijdrestErr == "") ){
                insertIntoDatabase($naam, $topic, $location, $street, $streetnr, $pay, $travel, $tnotes, $description, $squal, $tijdrest, $con);
            }
            else
                mysqli_close($con);
        }
    } //BRACKET FOR SESSION ROLE CHECK!
    
    function insertIntoDatabase($naam, $topic, $location, $street, $streetnr, $pay, $travel, $tnotes, $description, $squal, $tijdrest, $con){
        $intsupname = $_SESSION["username"];//has to be the same name as the name in the stagebegeleider table
        $stmt1 = mysqli_prepare($con, "SELECT CompanyName FROM Internship_Contact s WHERE s.IConName = ?");
        mysqli_stmt_bind_param($stmt1,'s', $intsupname);
        mysqli_stmt_execute($stmt1);
        $result = mysqli_stmt_get_result($stmt1);
        mysqli_stmt_close($stmt1);
        if (!$result){
            echo "database error!";
            die ('Unable to run query:' . mysqli_error());
        }
        else{
            $row = mysqli_fetch_row($result);
            $compname = $row[0];
        }
        if(empty($compname)){
            //change error message in future
            die("no company name linked to this account");
            //	header("Location: main_page.php");
        }	
	
	$icid = $_SESSION["ID"];
        $stmt2 = mysqli_prepare($con,
         "INSERT INTO Project(ProjectName, Description, Time, Studentqualities, Topic, Internship, IConID, IConName, CompanyName)
          VALUES (?,?,?,?,?,'1',?,?,?)");
        mysqli_stmt_bind_param($stmt2, 'ssssssss', $naam, $description, $tijdrest, $squal, $topic,$icid, $intsupname,$compname);
        $result2 = mysqli_stmt_execute($stmt2);
        //$result2 = mysqli_stmt_get_result($stmt2);
        mysqli_stmt_close($stmt2);
        if (!$result2){
            //        echo "database error!";
            //header("Location: ".$_SERVER["PHP_SELF"]);
            die('Unable to run query:' . mysqli_error() );
        }


        $stmt4 = mysqli_prepare($con, "INSERT INTO Internship_of(ProjectName, LocName, Location, StreetNr, Travel, Tnotes,
                                 Pay, CompanyName) VALUES (?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt4,'ssssssss', $naam, $location, $street, $streetnr, $travel, $tnotes, $pay, $compname);
        $result4 = mysqli_stmt_execute($stmt4);
        mysqli_stmt_close($stmt4);
        if (!$result4){
            header("Location: ".$_SERVER["PHP_SELF"]);
            die('Unable to run query:' . mysqli_error() );
        }


        $stmt3 = mysqli_prepare($con, "INSERT INTO Part_of(ProjectName, CompanyName) VALUES(?,?)");
        mysqli_stmt_bind_param($stmt3,'ss', $naam, $compname);
        $result3 = mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
        if (!$result3){
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

        <meta name="Description" content= "Offer Internship" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Offer Internship</title>
    </head>
    <body>
        <div class="main">
            <p>
            Fill in the forms to make your internship information available to the
            students.
            </p>
            <p><span class="error">* required field.</span></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <table class="form">
                <tr>
                <td>ProjectName:</td><td><input type="text" name="naam" value="<?php  echo $naam;?>">
                <span class="error">* <?php echo $naamErr;?></span></td>
                </tr>
                <tr>
                <td>Topic/keywords:</td><td><input type="text" name="topic" value="<?php  echo $topic;?>">
				<span class="error"><?php echo $topicErr;?></span></td>
				</tr>
				<tr>
                <td>Your internship's City:</td><td><input type="text" name="location" value="<?php echo $location;?>">
                <span class="error">* <?php echo $locationErr;?></span></td>
                </tr>
				<tr>
                <td>Street:</td><td><input type="text" name="street" value="<?php echo $street;?>">
                <span class="error"> <?php echo $streetErr;?></span></td>
                </tr>
				<tr>
                <td>StreetNumber:</td><td><input type="text" name="streetnr" value="<?php echo $streetnr;?>">
                <span class="error"> <?php echo $streetnrErr;?></span></td>
                </tr>
				<tr>
                <td>Pay:</td><td><input type="text" name="pay" value="<?php echo $pay;?>">
                <span class="error"> <?php echo $payErr;?></span></td>
                </tr>
				<tr>
                <td>Travel Arrangements:</td><td><input type="radio" name="travel" value="1">Included <input type="radio" name="travel" value="0">Excluded</td>
                </tr>
				<tr>
                <td>Notes:</td><td><input type="text" name="tnotes" value="<?php echo $tnotes;?>">
                <span class="error"> <?php echo $tnotesErr;?></span></td>
                </tr>
				<tr>
                <td>Describe your internship:</td><td><textarea name="description" rows="5" cols="40"><?php echo $description;?></textarea>
                <span class="error">* <?php echo $descriptionErr;?></span></td>
                </tr>
				<tr>
                <td>Describe the qualities you seek in a student (i.e. skillset):</td><td><textarea name="squal" rows="5" cols="40"><?php echo $squal;?></textarea>
                <span class="error"> <?php echo $squalErr;?></span></td>
                </tr>
				<tr>
                <td>Describe time restriction (when the internship is available):</td><td><textarea name="tijdrest" rows="5" cols="40"><?php echo $tijdrest;?></textarea>
                <span class="error"> <?php echo $tijdrestErr;?></span></td>
                </tr>
                </table>
                <input type="submit" value="Post internship">
            </form>
        </div>
    </body>
</html>
