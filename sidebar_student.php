<!-- student sidebar -->

<?php
	$username = $_SESSION["username"];
?>

<div class="sidebar">
    <div class="menu">
		<div class="menu-item">
			Logged in as:<br>
			<?php echo $username ?>
		</div>
        <?php echo ($highlight == "Overview") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="main_page.php">Overview</a></h4>
        </div>
        <?php echo ($highlight == "Supervisors") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="supervisor_list.php">Supervisors</a></h4>
        </div>
        <?php echo ($highlight == "Projects") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="project_list.php">Projects</a></h4>
        </div>
        <?php echo ($highlight == "My profile") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="profile.php">My profile</a></h4>
        </div>  
		<?php echo ($highlight == "Contact") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="contact.php">Contact</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="logout.php">Logout</a></h4>
        </div>
    </div> 
</div>
