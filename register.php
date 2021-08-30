<?php
	
	require "error.php";
	session_start();
	header("Access-Control-Allow-Origin: *");
	if (isset($_POST['email']))
	{
		//Udana walidacja? Załóżmy, że tak!
		$wszystko_OK=true;
		
		//Sprawdź poprawność nickname'a
		$nick = $_POST['nick'];
		
		//Sprawdzenie długości nicka
		if ((strlen($nick)<3) || (strlen($nick)>24))
		{
			error("Nickname musi posiadać od 3 do 24 znaków!",400);
			exit();
		}
		
		if (ctype_alnum($nick)==false)
		{
			error("Nickname może składać się tylko z liter i cyfr (bez polskich znaków)",400);
			exit();
		}
		
		// Sprawdź poprawność adresu email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			error("Podaj poprawny adres e-mail!",400);
			exit();
		}
		
		//Sprawdź poprawność hasła
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		
		if ((strlen($password1)<8) || (strlen($password1)>24))
		{
			error("Hasło musi posiadać od 8 do 24 znaków!",400);
			exit();
		}
		
		if ($password1!=$password2)
		{
			error("Podane hasła nie są identyczne!",400);
			exit();
		}	

		$haslo_hash = password_hash($password1, PASSWORD_DEFAULT);
		
		//Czy zaakceptowano regulamin?
		if (!isset($_POST['consent']))
		{
			error("Potwierdź akceptację regulaminu!",400);
			exit();
		}				
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_nick'] = $nick;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_password1'] = $password1;
		$_SESSION['fr_password2'] = $password2;
		if (isset($_POST['consent'])) $_SESSION['fr_consent'] = true;
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//Czy email już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE email='$email'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					error("Istnieje już konto przypisane do tego adresu e-mail!",400);
					$polaczenie->close();
					exit();
				}		

				//Czy nick jest już zarezerwowany?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE login='$nick'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow>0)
				{
					error("Istnieje już gracz o takim nicku! Wybierz inny.",400);
					$polaczenie->close();
					exit();
				}
				
				if ($wszystko_OK==true)
				{
					//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
					
					if ($polaczenie->query("INSERT INTO users VALUES (NULL, '$nick', '$haslo_hash', '$email', NULL, NULL)"))
					{
						$rezultat = $polaczenie->query(sprintf("SELECT * FROM users WHERE login='$nick'"));
						$wiersz = $rezultat->fetch_assoc();
						$user_id = $wiersz['id'];
						$response = array();
						$response['user_id']=$user_id;
						$rezultat->free_result();
						header('Content-Type:application/json');
						echo json_encode($response);
					}
					else
					{
						throw new Exception($polaczenie->error);
					}
					
				}
				
				$polaczenie->close();
			}
			
		}
		catch(Exception $e)
		{
			error($e->getMessage(),400);
			exit();
		}
		
	}
	
	
?>
