<?php

	require "error.php";

	header("Access-Control-Allow-Origin: *");

	session_start();
	
	if((!isset($_POST['login'])) || (!isset($_POST['password'])))
	{
		error("No login or password set",400);
		exit();
	}

	require_once "connect.php";
	
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($polaczenie->connect_errno!=0)
	{
		error($polaczenie->connect_errno,500);
		exit();
	}
	else
	{
		$login = $_POST['login'];
		$password = $_POST['password'];
		
		$login = htmlentities($login,ENT_QUOTES,"UTF-8");
		
		if($rezultat = @$polaczenie->query(sprintf("SELECT * FROM users WHERE login='%s'",
			mysqli_real_escape_string($polaczenie,$login))))
		{
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0)
			{
				
				$wiersz = $rezultat->fetch_assoc();
				
				
				if (password_verify($password, $wiersz['password']))
				{
					$user_id = $wiersz['id'];
					$response = array();
					$response['user_id']=$user_id;
					$rezultat->free_result();
					header('Content-Type:application/json');
					echo json_encode($response);
				}
				else 
				{
					error("Nieprawidłowy login lub hasło!",400);
					exit();
				}
			}
			else
			{
				error("Nieprawidłowy login lub hasło!",400);
				exit();
			}
		}
		else
		{
			error($polaczenie->error,500);
		}
		
		$polaczenie->close();
	}
	
	

?>
