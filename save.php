<?php

	session_start();
	
	if(!isset($_SESSION['userID']) ||!isset($_SESSION['type']) ||
		!isset($_SESSION['access_token']) ||!isset($_SESSION['refresh_token']))
		{
			//header('Location: index.php');
			//exit();
		}
	
	$user = $_SESSION['userID'];
	$type = $_SESSION['type'];
	$access_token = $_SESSION['access_token'];
	$refresh_token = $_SESSION['refresh_token'];

	unset($_SESSION['userID']);
	unset($_SESSION['type']);
	unset($_SESSION['access_token']);
	unset($_SESSION['refresh_token']);
	
	$successful_add = false;
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($conn->connect_errno!=0)
	{
		header("Location: /error.php?error=".$conn->error);
		exit();	
	}
	
	$sql = "INSERT INTO tokens VALUES (NULL,'".
	$user."','".$type."','".$access_token."','".$refresh_token."')";
	echo $sql;
	if($conn->query($sql) == true){
		$successful_add = true;
	}
	else{
		header("Location: /error.php?error=".$conn->error);
		exit();	
	}
	$conn->close();
	if(strcmp(htmlspecialchars($_GET['mode']),"redirect")==0)
		header('Location: '.$_SESSION["redirect_back"]);
	exit();
?>
