<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  
<div class="sidepane">
  <a href="#">Overview</a>
  <a href="#">Projects</a>
  <a href="#">Contact</a>
  <a href="#">Help</a></a>
</div>

<div class="main">
	<?php
		if ($_POST["password"] != $_POST["password2"]) {
			echo "Passwords didn't match<br/>";
			echo "<a href='../main_page.php'>Go back to main page</a>";
		}
		else {
			$name = $_POST["name"];
			$email = $_POST["email"];
			$password = $_POST["password"];
			$con = mysqli_connect("mysql.liacs.leidenuniv.nl", "s1551396", "9sdu8kG09u", "s1551396");
			//$stmt = $con->prepare("INSERT INTO StageApp_Gebruikers VALUES (?,?,?,?);");
			//$stmt->bind_param("ssss", $email, 'student', $name, $password);
			if (mysqli_connect_errno())
				$_SESSION["accCreateErr"] = "Failed to connect to MySQL: " . mysqli_connect_error();
			else {
				//stmt->execute(); //ipv de regel hieronder
				$result = mysqli_query($con, "INSERT INTO StageApp_Gebruikers VALUES ('$email','Student','$name','$password');");
				if (mysqli_error($con) != "")
					$_SESSION["accCreateErr"] = "Unable to run query:" . mysqli_error($con);
			}
			mysqli_close($con);
			header("Location: main_page.php");
		}
	?>
</div>
