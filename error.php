<?php
session_start();
echo $_GET["error"];
if(isset($_SESSION["redirect_back"]))
	header("Location: ".$_SESSION["redirect_back"]."?error=true");
exit();
?>