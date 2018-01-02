
<?php
$configs = include("config.php");
$con = mysqli_connect($configs["host"], $configs["username"], $configs["password"], $configs["dbname"]);
if(isset($_POST['Internship']) && $_POST['Internship'] == 'Yes'){
	$name = 1;
}
else{
	$name = 0;
}
echo "function incomplete <br>";
echo $name;
if($name){
	$sql ="UPDATE Project SET Internship = 1 WHERE ProjectName = '" . $_GET["prjct"] . "'";
}
else{
	$sql ="UPDATE Project SET Internship = 0 WHERE ProjectName = '" . $_GET["prjct"] . "'";
}
$con->query($sql);
//header( 'Location: SupEdit_projects.php' );
exit;
?>

