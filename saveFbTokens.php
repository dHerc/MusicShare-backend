<?php
require "save.php";
if(!isset($_POST["user"])||!isset($_POST["fb_user_id"]))
	error("You must specify user and fb_user_id",400);
$access_token = "";
$refresh_token = "";
if(isset($_POST["access_token"]))
	$access_token = $_POST["access_token"];
if(isset($_POST["refresh_token"]))
	$refresh_token = $_POST["refresh_token"];
$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($polaczenie->connect_errno!=0)
	{
		error($polaczenie->connect_errno,500);
		exit();
	}
	else
	{
		$user = $_POST["user"];
		$fb_user_id = $_POST["fb_user_id"];
		if(!$polaczenie->query("update users set fb_user_id = '$fb_user_id' where id='$user'"))
			error($polaczenie->error,500);
		$polaczenie->close();
	}
save($user,"Facebook",$access_token,$refresh_token,true);
?>