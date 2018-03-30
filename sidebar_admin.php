<!-- admin sidebar -->

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
        <div class="dropdown" id="dropdown">
                <div class="dropdown_item"><a href="create_intern_contact_account.php">Internship contact</a></div>
                <div class="dropdown_item"><a href="create_supervisor_account.php">Supervisor</a></div>
                <div class="dropdown_item"><a href="create_student_account.php">Student</a></div>
                <div class="dropdown_item"><a href="create_admin_account.php">Administrator</a></div>
        </div>
        <?php echo ($highlight == "Create account") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="#" onclick="toggleDropdown()">Create account</a></h4>
        </div>
        <?php echo ($highlight == "Students") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="student_list.php">Students</a></h4>
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
        <div class="menu-item">
            <h4><a href="logout.php">Logout</a></h4>
        </div>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}
</script>  
