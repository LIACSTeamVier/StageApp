
<?php
$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
$name = $_POST["time"];
$stmt = $con->prepare("UPDATE Project SET Time = ? WHERE ProjectName = '" . $_GET["prjct"] . "'");
$stmt->bind_param("s", $name);
$stmt->execute();
header( 'Location: SupEdit_projects.php' );
exit;
?>
