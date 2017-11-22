<!-- student sidebar -->

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

</style>

<div class="sidebar">
    <div class="menu">
        <div class="menu-item">
            <h4><a href="main_page.php">Overview</a></h4>
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