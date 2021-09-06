<?php
	require "save.php";
	header("Access-Control-Allow-Origin: *");
	
	if((!isset($_POST['email'])) || (!isset($_POST['fb_user_id'])))
	{
		error("No email or fb_user_id set",400);
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
		$email = $_POST['email'];
		$fb_user_id = $_POST['fb_user_id'];
		
		if($result = @$connection->query(sprintf("SELECT * FROM users WHERE email='%s'",
			mysqli_real_escape_string($connection,$email))))
		{
			$ilu_userow = $result->num_rows;
			if($ilu_userow>0)
			{
				
				$row = $result->fetch_assoc();
				
				$fb_id = $row["fb_user_id"];
				if(empty($fb_id))
				{
					if(!$connection->query("update users set fb_user_id = '$fb_user_id' where email='$email'"))
						error($connection->error,500);
				}
				else
				{
					if(strcmp($fb_id,$fb_user_id)!=0)
						error("User with this email have different facebook user id attached",409);
				}
				$user_id = $row['id'];
				save($user_id,"Facebook",$_POST["access_token"],"",false);
				$response = array();
				$response['user_id']=$user_id;
				$result->free_result();
				header('Content-Type:application/json');
				echo json_encode($response);
			}
			else
			{
				error("User with this email does not exist",400);
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