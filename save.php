<?php

	session_start();
	header("Access-Control-Allow-Origin: *");
	if(!isset($_SESSION['userID']) ||!isset($_SESSION['type']) ||
		!isset($_SESSION['access_token']) ||!isset($_SESSION['refresh_token']))
		{
			header("Location: /error.php?error=cannot find tokens");
			exit();
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
	
	$sql = "SELECT * FROM tokens WHERE user_id = '".$user."' AND type = '".$type."'";
	if($conn->query($sql) == true){
		$result = $conn->query($sql);
		if($result->num_rows>0)
			$sql = "UPDATE tokens SET access_token = '".$access_token."', 
			refresh_token ='".$refresh_token."' WHERE user_id = '".$user."' AND type = '".$type."'";
		else
			$sql = "INSERT INTO tokens VALUES (NULL,'".
			$user."','".$type."','".$access_token."','".$refresh_token."')";
	}
	else{
		header("Location: /error.php?error=".$conn->error());
		exit();	
	}
	
	if($conn->query($sql) == true){
		$successful_add = true;
	}
	else{
		header("Location: /error.php?error=".$conn->error);
		exit();	
	}
	$conn->close();
	if(strcmp(htmlspecialchars($_GET['mode']),"redirect")==0)
	{
		$redirect_url = $_SESSION["redirect_back"];
		unset($_SESSION["redirect_back"]);
		header('Location: '.$redirect_url);
	}
	else
	{
		$response = array();
		$response["access_token"]=$access_token;
		echo json_encode($response);
	}
	exit();
?>
