<?php
header("Access-Control-Allow-Origin: *");
if(isset($_POST["type"]))
{
	$type = json_encode($_POST["type"]);
	$type = str_replace("\"","",$type);
}
else
	$type = htmlspecialchars($_GET["type"]);
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
    }
	else
		echo "error";
}
 

main();
 
?>
