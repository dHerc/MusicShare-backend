<?php
require "error.php";
require_once "connect.php";
header("Access-Control-Allow-Origin: *");

	if((!isset($_POST['user_id'])) || (!isset($_POST['new_password']) || (!isset($_POST['old_password']))))
	{
		error("No old_password, new_password or user_id set",400);
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
		$old_password = $_POST['old_password'];
		$new_password = $_POST['new_password'];
		$user_id = $_POST['user_id'];
		
		if($result = @$connection->query(sprintf("SELECT * FROM users WHERE id=%s",
			mysqli_real_escape_string($connection,$user_id))))
		{
			$ilu_userow = $result->num_rows;
			
			if($ilu_userow>0)
			{
				$row = $result->fetch_assoc();
				if (password_verify($old_password, $row['password']))
				{
					$new_password_hash=password_hash($new_password, PASSWORD_DEFAULT);
					if(!$connection->query("update users set password = '$new_password_hash' where id='$user_id'"))
						error($connection->error,500);
				}
				else
				{
					error("Provided password does not match account password",400);
				}
			}
			else
			{
				error("User with this user_id does not exist",400);
			}
		}
		else
		{
			error($polaczenie->error,500);
		}
		
		$connection->close();
	}
?>