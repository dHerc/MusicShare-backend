<?php
session_start();
header("Access-Control-Allow-Origin: *");
if(isset($_POST["type"]))
{
	$type = json_encode($_POST["type"]);
	$type = str_replace("\"","",$type);
}
else
	$type = htmlspecialchars($_GET["type"]);
<<<<<<< HEAD
$auth = fopen("../auth.cred","r");
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
=======
if(isset($_POST["user"]))
{
	$userID = json_encode($_POST["user"]);
	$userID = str_replace("\"","",$userID);
	$_SESSION["userID"]=$userID;
}
if(isset($_POST["redirect_back"]))
{
	$redirect = json_encode($_POST["redirect_back"]);
	$redirect = str_replace("\"","",$redirect);
	$_SESSION["redirect_back"]=$redirect;
}
>>>>>>> 6711c9279b78e1173a49d7ec8ee22c8b4c8c8a9d
if(strcmp($type,"Spotify")==0)
{
	if(isset($_POST["redirect_uri"]))
		define('REDIRECT_URI', $_POST["redirect_uri"]);
	else
		define('REDIRECT_URI', 'https://musicshare-backend.herokuapp.com/callback/spotify.php'); // wprowadź redirect_uri
	define('AUTH_URL', 'https://accounts.spotify.com/authorize');
	define('TOKEN_URL', 'https://accounts.spotify.com/api/token');
	define('CLIENT_ID', getenv("spotifyID"));
	define('CLIENT_SECRET', getenv("spotifySecret"));
}
if(strcmp($type,"Genius")==0)
{
	define('REDIRECT_URI', 'https://musicshare-backend.herokuapp.com/callback/genius.php'); // wprowadź redirect_uri
	define('AUTH_URL', 'https://api.genius.com/oauth/authorize');
	define('TOKEN_URL', 'https://api.genius.com/oauth/token');
	define('CLIENT_ID', getenv("geniusID"));
	define('CLIENT_SECRET', getenv("geniusSecret"));
}
 
 
function getAuthorizationCode() {
    $authorization_redirect_url = AUTH_URL . "?response_type=code&client_id=" 
    . CLIENT_ID . "&redirect_uri=" . REDIRECT_URI . "&prompt=confirm";
	header("Access-Control-Allow-Origin: *");
	header("Location:" . $authorization_redirect_url);
	exit();
}
 

function getCurl($headers, $content) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => TOKEN_URL,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $content
    ));
    return $ch;
}
 

function getAccessToken($authorization_code) {
    $authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
    $authorization_code = urlencode($authorization_code);
    $headers = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
    $content = "grant_type=authorization_code&code=${authorization_code}&redirect_uri=" . REDIRECT_URI;
    $ch = getCurl($headers, $content);
    $tokenResult = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
 
    if ($tokenResult === false || $resultCode !== 200) {
		header("Location: /error.php?error=".$resultCode.$tokenResult);
		exit();
    }
    return ("&access_token=".json_decode($tokenResult)->access_token."&refresh_token=".json_decode($tokenResult)->refresh_token)
}
 

function main(){
    if ($_POST["code"]) {
        $tokens = getAccessToken($_POST["code"]);
		$type = htmlspecialchars($_POST["type"]);
		$user = $_POST["user"];
		header("Location:/save.php?mode=close&user=".$user.$tokens."&type=".$type);
		exit();
    } else {    
        getAuthorizationCode();
    }
}
 

main();
 
?>
