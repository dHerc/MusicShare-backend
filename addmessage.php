<?php
require "error.php";
require "connect.php";

session_start();
header("Access-Control-Allow-Origin: *");
if(!isset($_POST['sender_id']) ||!isset($_POST['receiver_id']) ||
	!isset($_POST['song_id']))
	{
		error("Missing data",500);
		exit();
	}
	
	$sender_id = $_POST['sender_id'];
	$receiver_id = $_POST['receiver_id'];
	$song_id = $_POST['song_id'];
	
	$successful_add = false;
	
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['db_user'], $GLOBALS['db_password'], $GLOBALS['db_name']);
	
	if ($conn->connect_errno!=0)
	{
		error($conn->error,500);
		exit();	
	}
	
	$sql = "INSERT INTO messages VALUES(NULL,".$sender_id.",".$receiver_id.",'".$song_id."')";
	
	if($conn->query($sql) == true){
		$successful_add = true;
	}
	else{
		error($conn->error,404);
		exit();	
	}
	$conn->close();
	exit();
?>
