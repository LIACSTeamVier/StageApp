<!-- admin sidebar -->

<style>

.sidebar {
    position: fixed;
    margin-top: -10px;
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

.menu-item a {
    margin-left: -10px;
	padding: 6px 6px 6px 6px;
	text-align: center;
	text-decoration: none;
	text-shadow: 2px 2px 8px #000000
	font-size: 14px;
	color: #ffffff;
	font-family: "Verdana";
	display: block;
}

.dropdown {
	background-color: #22337a;
	min-width: 120px;
    display: none;
    position: absolute;
    margin-left: 130px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
}

.dropdown_item a {
	padding: 6px 6px 6px 6px;
	text-align: center;
	text-decoration: none;
	text-shadow: 2px 2px 8px #000000;
	font-size: 12px;
	color: #ffffff;
	font-family: "Verdana";
	display: block;
}

.show {
    display: block;
}

</style>

<div class="sidebar">
    <div class="menu">
        <div class="menu-item">
            <h4><a href="main_page.php">Overview</a></h4>
        </div>
        <div class="dropdown" id="dropdown">
                <div class="dropdown_item"><a href="create_intern_contact_account.php">Internship contact</a></div>
                <div class="dropdown_item"><a href="create_supervisor_account.php">Supervisor</a></div>
                <div class="dropdown_item"><a href="create_student_account.php">Student</a></div>
                <div class="dropdown_item"><a href="create_admin_account.php">Administrator</a></div>
        </div>
        <div class="menu-item">
            <h4><a href="#" onclick="toggleDropdown()">Create account</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="student_list.php">Students</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="supervisor_list.php">Supervisors</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="project_list.php">Projects</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="index.php">Logout</a></h4>
        </div>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}
</script>  