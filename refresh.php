<?php
header("Access-Control-Allow-Origin: *");
require "save.php";
session_start();
$type = htmlspecialchars($_POST["type"]);
$userID = htmlspecialchars($_POST["user"]);
if(strcmp($type,"Spotify")==0)
{
	define('CLIENT_ID', getenv("spotifyID"));
	define('CLIENT_SECRET', getenv("spotifySecret"));
}
if(strcmp($type,"Genius")==0)
{
	define('CLIENT_ID', getenv("geniusID"));
	define('CLIENT_SECRET', getenv("geniusSecret"));
}

require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_errno!=0)
{
	error($conn->error(),500);
	exit();	
}

$sql = "SELECT refresh_token FROM tokens WHERE user_id ='".$userID."' AND type = '".$type."';";

$result = $conn->query($sql);
if($result == false)
{
	error($conn->error(),404);
	exit();	
}
$conn->close();
$refresh_token = $result->fetch_row()[0];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=".$refresh_token);
$authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
$headers = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$tokenResult = curl_exec($ch);
$resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($tokenResult === false || $resultCode !== 200) {
		error($tokenResult,$resultCode);
		exit();
    }
curl_close($ch);
save($userID,$type,json_decode($tokenResult)->access_token,$refresh_token);
exit();
?>
