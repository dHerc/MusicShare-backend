<?php

	session_start();
	
	if((!isset($_POST['login'])) || (!isset($_POST['password'])))
	{
		header('Location: index.php');
		exit();
	}

	require_once"connect.php";
	
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($polaczenie->connect_errno!=0)
	{
		echo "Error".$polaczenie->connect_errno;
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
					$_SESSION['zalogowany'] = true;
					$_SESSION['id'] = $wiersz['id'];
					$_SESSION['login'] = $wiersz['login'];
					$_SESSION['email'] = $wiersz['email'];
					
					$rezultat->free_result();
					header('Location: main.php');
				}
				else 
				{
					$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: index.php');
				}
			}
			else
			{
				$_SESSION['blad'] = '<span style = "color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: index.php');
			}
		}
		
		$polaczenie->close();
	}
	
	

?>