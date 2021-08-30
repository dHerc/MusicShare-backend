<?php
	require "save.php";
	header("Access-Control-Allow-Origin: *");
	
	if((!isset($_POST['email'])) || (!isset($_POST['google_user_id'])))
	{
		error("No email or google_user_id set",400);
		exit();
	}
	
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($polaczenie->connect_errno!=0)
	{
		error($polaczenie->connect_errno,500);
		exit();
	}
	else
	{
		$email = $_POST['email'];
		$google_user_id = $_POST['google_user_id'];
		
		if($rezultat = @$polaczenie->query(sprintf("SELECT * FROM users WHERE email='%s'",
			mysqli_real_escape_string($polaczenie,$email))))
		{
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0)
			{
				
				$wiersz = $rezultat->fetch_assoc();
				
				$google_id = $wiersz["google_user_id"];
				if(empty($google_id))
				{
					if(!$polaczenie->query("update users set google_user_id = '$google_user_id' where email='$email'"))
						error($polaczenie->error,500);
				}
				else
				{
					if(strcmp($google_id,$google_user_id)!=0)
						error("User with this email have different google user id attached",409);
				}
				$user_id = $wiersz['id'];
				save($user_id,"Google",$_POST["access_token"],"",false);
				$response = array();
				$response['user_id']=$user_id;
				$rezultat->free_result();
				header('Content-Type:application/json');
				echo json_encode($response);
			}
			else
			{
				error("User with this email does not exist",400);
				exit();
			}
		}
		
		$polaczenie->close();
	}
?>