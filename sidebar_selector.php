<!-- sidebar -->

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

.highlight a {
    background-color: #a5accc;
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

<?php

if (isset($_SESSION["class"])) {

	if ($_SESSION["class"] == "Admin") $sidebar_type = 'sidebar_admin.php';

	else if ($_SESSION["class"] == "Internship Contact") $sidebar_type = 'sidebar_internshipcontact.php';

	else if ($_SESSION["class"] == "Supervisor") $sidebar_type = 'sidebar_supervisor.php';

	else $sidebar_type = 'sidebar_student.php';

}

else $sidebar_type = 'sidebar_main.php';



require_once $sidebar_type;

?>
