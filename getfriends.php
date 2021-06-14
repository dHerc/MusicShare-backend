<?php
require "error.php";
require "connect.php";

session_start();
header("Access-Control-Allow-Origin: *");
if(!isset($_POST['user_id']))
	{
		error("No user_id",500);
		exit();
	}
	
	$user_id = $_POST['user_id'];
	
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['db_user'], $GLOBALS['db_password'], $GLOBALS['db_name']);
	
	if ($conn->connect_errno!=0)
	{
		error($conn->error,500);
		exit();	
	}
	
	$sql = "SELECT * FROM friends join users on friends.friend_id=users.id where user_id =".$user_id." and status = 'accepted'";
	$result = $conn->query($sql);
	if($result){
		$response = array();
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
			$response[$i]['friend_id']=$row['friend_id'];
			$response[$i]['friend_name']=$row['login'];
			$i=$i+1;
		}
		$result->free_result();
		header('Content-Type:application/json');
		$conn->close();
		echo json_encode($response);
	}
	else{
		error($conn->error,404);
		$conn->close();
		exit();	
	}
	exit();
?>
