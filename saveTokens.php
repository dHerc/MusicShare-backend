<?php
require "save.php";
if(!isset($_POST["user"])||!isset($_POST["type"]))
	error("You must specify user and type",400);
$access_token = "";
$refresh_token = "";
if(isset($_POST["access_token"]))
	$access_token = $_POST["access_token"];
if(isset($_POST["refresh_token"]))
	$refresh_token = $_POST["refresh_token"];	
save($_POST["user"],$_POST["type"],$access_token,$refresh_token,true);
?>