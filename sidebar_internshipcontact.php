<!-- internship contact sidebar -->

<style>

.sidebar {
    position: fixed;
    margin-left: -10px;
    padding-bottom: -10px;
    min-height: 100vh;
    width: 140px;
    background-color: #001158;
}

.sidebar a:hover {
	background-color: #a5accc;
}

.menu {
    padding-top: 20px;
    padding-left: 10px;
}

.menu-item {
	text-align: center;
	text-decoration: none;
	text-shadow: 2px 2px 8px #000000;
    font-size: 12px;
	color: #ffffff;
	font-family: "Verdana";
}

.menu-item a {
    margin-left: -10px;
	padding: 6px 6px 6px 6px;
	text-align: center;
	text-decoration: none;
	text-shadow: 2px 2px 8px #000000;
	font-size: 14px;
	color: #ffffff;
	font-family: "Verdana";
	display: block;
}

</style>

<?php
	$username = $_SESSION["username"];
?>

<div class="sidebar">
    <div class="menu">
		<div class="menu-item">
			Logged in as:<br>
			<?php echo $username ?>
		</div>
        <div class="menu-item">
            <h4><a href="main_page.php">Overview</a></h4>
        </div>
		<div class="menu-item">
            <h4><a href="internship.php">Submit internship</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="internship_request_list.php">Internship join requests</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="profile.php">My profile</a></h4>
        </div>
		<div class="menu-item">
            <h4><a href="contact.php">Contact</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="logout.php">Logout</a></h4>
        </div>
    </div> 
</div> 
