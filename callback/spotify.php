<?php
if(isset($_GET["code"]))
{
	$code = htmlspecialchars($_GET["code"]);
	header("Location: /callback/authorize.php?code=".$code."&type=Spotify");
	exit();
}
if(isset($_GET["error"]))
{
	header("Location: /error.php?error=".htmlspecialchars($_GET["error"]));
	exit();
}
?>