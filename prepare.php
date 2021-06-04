<?php
$pwd =  htmlspecialchars($_POST["pass"]);
require_once "KeePassPHP/keepassphp/keepassphp.php";
use \KeePassPHP\KeePassPHP as KeePassPHP;
require_once "KeePassPHP/keepassphp/lib/filter.php";
use \KeePassPHP\AllFilter;
$ckey = KeePassPHP::masterKey();
KeePassPHP::addPassword($ckey, $pwd);
$error = null;
$file = "apis.kdbx";
$db = KeePassPHP::openDatabaseFile($file, $ckey, $error);
if($db!=null)
{
	$filter = new AllFilter();
	$arr = $db->toArray($filter)["Groups"][0]["Entries"];
	file_put_contents("auth.cred","");
	foreach($arr as $item)
	{
		$title = json_encode($item["StringFields"]["Title"]);
		$username = json_encode($item["StringFields"]["UserName"]);
		$password = json_encode($item["StringFields"]["Password"]);
		$title = substr($title,1,strlen($title)-2);
		$username = substr($username,1,strlen($username)-2);
		$password = substr($password,1,strlen($password)-2);
		file_put_contents("auth.cred",$title.";".$username.";".$password.";".PHP_EOL,FILE_APPEND);
	}
	echo "Prepared credentials successfully".PHP_EOL;
}
else
	echo "Wrong password".PHP_EOL;

	$DB_created = false;

	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($host, $db_user, $db_password);
	
	if ($conn->connect_errno!=0)
	{
		die("Connection failed: ".$conn->connect_error);
	}
	$reset = "DROP DATABASE music_api;";
	$conn->query($reset);
	$sql = "CREATE DATABASE music_api;";
	if($conn->query($sql) == TRUE){
		
		echo "Database created successfully </br>";
		$DB_created = true;
	}
	else{
		echo "Error creating database: ".$conn->error;
	}
	$conn->close();
	
	if($DB_created==true){
		
		$conn = new mysqli($host, $db_user, $db_password, $db_name);
	
		if ($conn->connect_errno!=0)
		{
			die("Connection failed: ".$conn->connect_error);
		}
		
		$sql = "CREATE TABLE users (
		id INT(6) AUTO_INCREMENT PRIMARY KEY,
		login TEXT NOT NULL,
		password TEXT NOT NULL,
		email TEXT NOT NULL
		);";
		if($conn->query($sql) == TRUE){
			echo "Table users created successfully </br>";
		}
		else{
			echo "Error creating table users: ".$conn->error;
		}
		
		$sql = "CREATE TABLE tokens (
		id INT(6) AUTO_INCREMENT PRIMARY KEY,
		user_id INT(6) NOT NULL,
		type TEXT NOT NULL,
		access_token TEXT NOT NULL,
		refresh_token TEXT,
		FOREIGN KEY (user_id) REFERENCES users(id)
		);";
		if($conn->query($sql) == TRUE){
			echo "Table tokens created successfully </br>";
		}
		else{
			echo "Error creating table tokens: ".$conn->error;
		}
		
		$conn->close();
	}
?>