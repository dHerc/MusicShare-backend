<?php
session_start();
echo $_GET["error"];
if(isset($_SESSION["redirect_back"]))
{
	$redirect_url = $_SESSION["redirect_back"];
	unset($_SESSION["redirect_back"]);
	header("Location: ".$redirect_url."?error=".$_GET["error"]);
}
exit();
?>