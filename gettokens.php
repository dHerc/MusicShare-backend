<?php
header("Access-Control-Allow-Origin: *");
$type = htmlspecialchars($_GET["type"]);
$userID = htmlspecialchars($_GET["user"]);
require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_errno!=0)
{
	header("Location: /error.php?error=".$conn->error());
	exit();	
}

$sql = "SELECT access_token FROM tokens WHERE user_id ='".$userID."' AND type = '".$type."';";

$result = $conn->query($sql);
if($result == false)
{
	header("Location: /error.php?error=".$conn->error());
	exit();	
}
$conn->close();
$access_token = $result->fetch_row()[0];
echo $access_token;
?>