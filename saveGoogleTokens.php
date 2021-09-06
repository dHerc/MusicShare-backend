<?php
require "save.php";
if(!isset($_POST["user"])||!isset($_POST["google_user_id"]))
	error("You must specify user and google_user_id",400);
$access_token = "";
$refresh_token = "";
if(isset($_POST["access_token"]))
	$access_token = $_POST["access_token"];
if(isset($_POST["refresh_token"]))
	$refresh_token = $_POST["refresh_token"];
$connection = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($connection->connect_errno!=0)
	{
		error($connection->connect_errno,500);
		exit();
	}
	else
	{
		$user = $_POST["user"];
		$google_user_id = $_POST["google_user_id"];
		if(!$connection->query("update users set google_user_id = '$google_user_id' where id='$user'"))
			error($connection->error,500);
		$connection->close();
	}
save($user,"Google",$access_token,$refresh_token,true);
?>