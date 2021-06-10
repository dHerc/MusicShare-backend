<?php
require "error.php";
function save($user,$type,$access_token,$refresh_token)
{
	session_start();
	header("Access-Control-Allow-Origin: *");
	if(!isset($user) ||!isset($type) ||
		!isset($access_token) ||!isset($refresh_token))
		{
			error("cannot find tokens",500);
			exit();
		}
	
	$successful_add = false;
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($conn->connect_errno!=0)
	{
		error($conn->error,500);
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
		error($conn->error(),500);
		exit();	
	}
	
	if($conn->query($sql) == true){
		$successful_add = true;
	}
	else{
		error($conn->error,404);
		exit();	
	}
	$conn->close();
	$response = array();
	$response["access_token"]=$access_token;
	echo json_encode($response);
	exit();
}
?>
