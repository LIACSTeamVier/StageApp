
<?php
$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
$name =  date('l jS \of F Y h:i:s A') . ": " . $_POST["name"] ."<br>" ;
$stmt = $con->prepare("UPDATE Project SET Progress = CONCAT(Progress, ?) WHERE ProjectName = '" . $_GET["prjct"] . "'");
$stmt->bind_param("s", $name);
$stmt->execute();
header( 'Location: SupEdit_projects.php' );
exit;
?>
