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
		if (isset($_SESSION["username"])) {
			//TODO error page
			exit;
		}
		echo "<h5>Please fill in the form below<h5></br>";
		echo
			"<form action='add_student_account' method='post'>
			Name: <input type='text' name='name'><br>
			E-mail: <input type='text' name='email'><br>
			Password: <input type='password' name='password'><br>
			Repeat password: <input type='password' name='password2'><br>
			<input type='submit' value='Create account'>
			</form>"
	?>
</div>
	
</body>
</html>