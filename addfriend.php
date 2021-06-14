<?php
require "error.php";
require "connect.php";

session_start();
header("Access-Control-Allow-Origin: *");
if(!isset($_POST['user_id']) ||!isset($_POST['friend_id']))
	{
		error("Missing data",500);
		exit();
	}
	
	$user_id = $_POST['user_id'];
	$friend_id = $_POST['friend_id'];
	
	$successful_add = false;
	
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['db_user'], $GLOBALS['db_password'], $GLOBALS['db_name']);
	
	if ($conn->connect_errno!=0)
	{
		error($conn->error,500);
		exit();	
	}
	
	$sql = "SELECT * FROM friends WHERE (user_id =".$user_id." AND friend_id =".$friend_id.") 
		OR (user_id =".$friend_id." AND friend_id =".$user_id.")";
	if($result = $conn->query($sql)){
		
		$num = $result->num_rows;
		if($num>0)
		{
			error("There already is a friendship like this",500);
			exit();
		}
		
		$sql = "INSERT INTO friends VALUES(NULL,".$user_id.",".$friend_id.",'pending')";
	
		if($conn->query($sql) == true){
			$successful_add = true;
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
	exit();
?>
