<?php
session_start();
$type = htmlspecialchars($_GET["type"]);
if(isset($_GET["user"]))
{
	$userID = htmlspecialchars($_GET["user"]);
	$_SESSION["userID"]=$userID;
}
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
if(strcmp($type,"Spotify")==0)
{
	define('REDIRECT_URI', 'http://localhost/callback/spotify.php'); // wprowadź redirect_uri
	define('AUTH_URL', 'https://accounts.spotify.com/authorize');
	define('TOKEN_URL', 'https://accounts.spotify.com/api/token');
}
if(strcmp($type,"Genius")==0)
{
	define('REDIRECT_URI', 'http://localhost/callback/genius.php'); // wprowadź redirect_uri
	define('AUTH_URL', 'https://api.genius.com/oauth/authorize');
	define('TOKEN_URL', 'https://api.genius.com/oauth/token');
}
 
 
function getAuthorizationCode() {
    $authorization_redirect_url = AUTH_URL . "?response_type=code&client_id=" 
    . CLIENT_ID . "&redirect_uri=" . REDIRECT_URI . "&prompt=confirm";
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
	$_SESSION["access_token"]=json_decode($tokenResult)->access_token;
	$_SESSION["refresh_token"]=json_decode($tokenResult)->refresh_token;
	$_SESSION["type"] = htmlspecialchars($_GET["type"]);
    return;
}
 

function main(){
    if ($_GET["code"]) {
        $tokens = getAccessToken($_GET["code"]);
		header("Location:/save.php?mode=redirect");
		exit();
    } else {    
        getAuthorizationCode();
    }
}
 

main();
 
?>