<?php
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

$sql = "SELECT access_token,refresh_token FROM tokens WHERE user_id ='".$userID."' AND type = '".$type."';";

$result = $conn->query($sql);
if($result == false)
{
	header("Location: /error.php?error=".$conn->error());
	exit();	
}
$conn->close();
$row = $result->fetch_row();
$access_token = $row[0];
$refresh_token = $row[1];
$response = array();
$response["access_token"]=$access_token;
$response["refresh_token"]=$refresh_token;
echo json_encode($response);
?>