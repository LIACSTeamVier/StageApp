<?php
include 'general_functions.php';

session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
    <head>
        <meta charset="utf-8" /> 

        <meta name="Description" content= "Home" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Home</title>
    </head>
    <body>

        <div class="sidepane">
	        <a href="main_page.php">Overview</a>
            <?php
	            if($_SESSION["class"] == "Admin" || $_SESSION["class"] == "Supervisor")
		            echo "<a href=\"request_list.php\">Student Supervison Requests</a>"
            ?>
	        <a href="project_list.php">Projects</a>
	        <a href="#">Contact</a>
	        <a href="database_table.php">Database</a>
	        <a href="#">Help</a></a>
        </div>

        <div class="main">
            <?php
		        if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
			        header("Location: index.php");
			        exit;
		        }
		        $username = $_SESSION["username"];
		        $class = $_SESSION["class"];
		        echo "<h1>Welcome " . "$username" . "." ."</h1>";
		        echo "<p>You are a(n) " . "$class" . "." ."<p>"; //TODO temp, remove line.
		
		        // After sending an e-mail
		        if (isset($_SESSION["creatingAccount"])) {
				        if (isset($_SESSION["accCreateErr"]))
						        echo "ERROR: " . $_SESSION["accCreateErr"] . "</br>Could not create account. No e-mail was sent.</br>";
				        else {
						        echo "Account created successfully.</br>";
						        if (isset($_SESSION["emailErr"]))
								        echo "ERROR: e-mail could not be delivered. Please manually inform the recipient. Their username should be their e-mail address. Their password can be found in the 'name' table."; //TODO vervang 'name'
				        }
		        }
		        unset($_SESSION["emailErr"]);
		        unset($_SESSION["accCreateErr"]);
		        unset($_SESSION["creatingAccount"]);
		
                if ($class == "Admin") {
			        //List all students with their projects
                    $result = query_our_database("SELECT * FROM Student");
            
                    echo "<table>"; // start a table tag in the HTML
              
                    // column names
                    echo "<tr><th>Student ID</th>
                              <th>Name</th>
                              <th>E-mail</th>
                              <th>Telephone</th></tr>";

                    // rows of the database
                    while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
                        echo "<tr><td>" . $row['StuID'] . "</td>
                              <td>" . $row['StuName'] . "</td>
                              <td>" . $row['StuEMAIL'] . "</td>
                              <td>" . $row['StuTel'] . "</td></tr>";  //$row['index'] the index here is a field name
                    }

                    echo "</table>"; //Close the table in HTML

                    //Button for creating admin account (TODO: move to accounts page)
                    echo "<form action='create_admin_account.php' method='head'>
                              <input type='submit' value='Create an admin account'>
                          </form>";
            }
		    if ($class == "Supervisor") {
                //List assigned students and their projects
                //Button for creating a project (TODO: move to projects page)
                echo "<form action='project.php' method='head'>
                          <input type='submit' value='Create a university project'>
                      </form>";
            }
            if ($class == "Internship Contact") {
			    //List assigned students and their projects
                //Button for creating an internship (TODO: move to projects page?)
                echo "<form action='stage.php' method='head'>
                          <input type='submit' value='Create an internship'>
                      </form>";
		        }
		        if ($class == "Student") {
			        //Show your project and supervisors
                    //Button for requesting a supervisor (TODO: move to supervisor page)
			        echo "<form action='makerequest.php' method='post'>
                              <input type='submit' value='Make a request for a supervisor'>
						  </form>";
		        }
            
            ?>
	        <form action="attempting_Logout.php" method="post">
		        <input type="submit" value="Logout">
	        </form>

        </div>

    </body>
</html> 
