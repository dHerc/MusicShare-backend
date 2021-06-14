<?php
require "error.php";
require "connect.php";

session_start();
header("Access-Control-Allow-Origin: *");
if(!isset($_POST['friendship_id']) ||!isset($_POST['user_id']))
	{
		error("Missing data",500);
		exit();
	}
	
	$user_id = $_POST['user_id'];
	$friendship_id = $_POST['friendship_id'];
	
	$successful_add = false;
	
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['db_user'], $GLOBALS['db_password'], $GLOBALS['db_name']);
	
	if ($conn->connect_errno!=0)
	{
		error($conn->error,500);
		exit();	
	}
	
	$sql = "SELECT * FROM friends where id = ".$friendship_id;
	if($result = $conn->query($sql)){
		$row = mysqli_fetch_assoc($result);
		$friend_id = $row['user_id'];
		$result->free_result();
		$sql = "UPDATE friends SET status = 'accepted' WHERE id=".$friendship_id;
		if($conn->query($sql) == true){
			$sql = "INSERT INTO friends VALUES(NULL,".$user_id.",".$friend_id.",'accepted')";
			if($conn->query($sql) == true){
				$conn->close();
				exit();
			}
			else{
				error($conn->error,500);
				exit();
			}
		}
		else{
			error($conn->error,500);
			exit();
		}
	}
	else{
		error($conn->error,500);
		exit();
	}
	$conn->close();
	exit();
?>
