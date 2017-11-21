<?php

if (isset($_SESSION["class"])) {
	if ($_SESSION["class"] == "Admin") $sidebar_type = 'sidebar_admin.php';
	else if ($_SESSION["class"] == "Internship Contact") $sidebar_type = 'sidebar_internshipcontact.php';
	else if ($_SESSION["class"] == "Supervisor") $sidebar_type = 'sidebar_supervisor.php';
	else $sidebar_type = 'sidebar_student.php';
}
else $sidebar_type = 'sidebar_main.php';

include $sidebar_type;

?>