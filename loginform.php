<?php
	session_start();
	
	if(isset(($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: main.php');
		exit();
	}
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf8"/>
	<meta http-equiv="X-UA-Compatible" content = "IE=edge,chrome=1"/>
	<title>Spotify - Api</title>
</head>

<body>

	Najlepsze nuty na rejonie<br/><br/>
	
	<a href="rejestracja.php">Rejestracja - załóż darmowe konto!</a>
	<br /><br />
	
	<form action="zaloguj.php" method="post">
	
		Login:<br/><input type="text" name="login"/><br/>
		Hasło:<br/><input type="password" name="password"/><br/><br/>
		<input type="submit" value="Zaloguj się"/>
		
	</form>

<?php
	if(isset($_SESSION['blad']))
	echo $_SESSION['blad'];
?>

</body>
</html>