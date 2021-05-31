<?php

	session_start();
	
	if(!isset($_SESSION['userID']) ||!isset($_SESSION['type']) ||
		!isset($_SESSION['access_token']) ||!isset($_SESSION['refresh_token']))
		{
			header('Location: index.php');
		}
	
	$user = $_SESSION['userID'];
	$type = $_SESSION['type'];
	$access_token = $_SESSION['access_token'];
	$refresh_token = $_SESSION['refresh_token'];
	
	$successful_add = false;
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($conn->connect_errno!=0)
	{
		die("Connection failed: ".$conn->connect_error);
	}
	
	$sql = "INSERT INTO tokens VALUES (NULL,".
	$user.",".$type.",".$access_token.",".$refresh_token.")";
	
	if($conn->query($sql) == true){
		$successful_add = true;
	}
	else{}
	conn->close();

	header('Location: index.php');

?>