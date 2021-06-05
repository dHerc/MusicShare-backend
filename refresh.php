<?php
session_start();
$type = htmlspecialchars($_GET["type"]);
$userID = htmlspecialchars($_GET["user"]);

$auth = fopen("auth.cred","r");
while(!feof($auth))
{
	$cred = fgets($auth);
	$data = explode(';',$cred);
	if(strcmp($type,$data[0])==0)
	{
		define('CLIENT_ID', $data[1]);
		define('CLIENT_SECRET', $data[2]);
	}
}
fclose($auth);

require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_errno!=0)
{
	header("Location: /error.php?error=".$conn->error());
	exit();	
}

$sql = "SELECT refresh_token FROM tokens WHERE user_id ='".$userID."' AND type = '".$type."';";

$result = $conn->query($sql);
if($result == false)
{
	header("Location: /error.php?error=".$conn->error());
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
		header("Location: /error.php?error=".$resultCode.$tokenResult);
		exit();
    }
curl_close($ch);
$_SESSION["access_token"]=json_decode($tokenResult)->access_token;
$_SESSION["refresh_token"]=$refresh_token;
$_SESSION["type"] = $type;
$_SESSION["userID"] = $userID;
header("Location:/save.php?mode=close");
exit();
?>