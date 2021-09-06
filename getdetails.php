<?php
require "error.php";
require_once "connect.php";
header("Access-Control-Allow-Origin: *");

	if((!isset($_GET['user_id'])))
	{
		error("No user_id set",400);
		exit();
	}
	
	$connection = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($connection->connect_errno!=0)
	{
		error($connection->connect_errno,500);
		exit();
	}
	else
	{
		$user_id = $_GET['user_id'];
		
		if($result = @$connection->query(sprintf("SELECT * FROM users WHERE id=%s",
			mysqli_real_escape_string($connection,$user_id))))
		{
			$user_amount = $result->num_rows;
			if($user_amount>0)
			{
				
				$row = $result->fetch_assoc();
				
				
				
				$response = array();
				$response['email']=$row['email'];
				$response['login']=$row['login'];
				$response['facebook']=!empty($row['fb_user_id']);
				$response['google']=!empty($row['google_user_id']);
				$result->free_result();
				header('Content-Type:application/json');
				echo json_encode($response);
			}
			else
			{
				error("User with this id does not exist",400);
				exit();
			}
		}
		
		$connection->close();
	}
?>