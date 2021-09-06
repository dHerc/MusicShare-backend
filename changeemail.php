<?php
require "error.php";
require_once "connect.php";
header("Access-Control-Allow-Origin: *");

	if((!isset($_POST['user_id'])) || (!isset($_POST['new_email'])))
	{
		error("No new_email or user_id set",400);
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
		$email = $_POST['new_email'];
		$user_id = $_POST['user_id'];
		
		if($result = @$connection->query(sprintf("SELECT * FROM users WHERE id=%s",
			mysqli_real_escape_string($connection,$user_id))))
		{
			$ilu_userow = $result->num_rows;
			if($ilu_userow>0)
			{
				if(!$connection->query("update users set email = '$email' where id='$user_id'"))
					error($connection->error,500);
			}
			else
			{
				error("User with this user_id does not exist",400);
				exit();
			}
		}
		else
		{
			error($polaczenie->error,500);
		}
		
		$connection->close();
	}
?>