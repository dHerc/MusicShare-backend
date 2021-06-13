<?php
require "error.php";
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

$file = "db.kdbx";
$db = KeePassPHP::openDatabaseFile($file, $ckey, $error);
if($db!=null)
{
	$filter = new AllFilter();
	$arr = $db->toArray($filter)["Groups"][0]["Entries"];
	foreach($arr as $item)
	{
		$title = json_encode($item["StringFields"]["Title"]);
		$username = json_encode($item["StringFields"]["UserName"]);
		$password = json_encode($item["StringFields"]["Password"]);
		$url = json_encode($item["StringFields"]["URL"]);
		file_put_contents("connect.php",
		"<?php".PHP_EOL .
		"\$host = ".$url.";".PHP_EOL .
		"\$db_user = ".$username.";".PHP_EOL .
		"\$db_password = ".$password.";".PHP_EOL .
		"\$db_name = ".$title.";".PHP_EOL .
		"?>");
	}
	echo "Prepared db credentials successfully".PHP_EOL;
}
	echo $url;
	$host = substr($url,1,strlen($url)-2);
	$db_user = substr($username,1,strlen($username)-2);
	$db_password = substr($password,1,strlen($password)-2);
	$db_name = substr($title,1,strlen($title)-2);
	
	require('connect.php');
	mysqli_report(MYSQLI_REPORT_STRICT);
	
		
		$conn = new mysqli($host, $db_user, $db_password, $db_name);
	
		if ($conn->connect_errno!=0)
		{
			error($conn->connect_errno,500);
			exit();
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
		FOREIGN KEY (user_id) REFERENCES users(id) on delete cascade
		);";
		if($conn->query($sql) == TRUE){
			echo "Table tokens created successfully </br>";
		}
		else{
			echo "Error creating table tokens: ".$conn->error;
		}
		
		$sql = "CREATE TABLE messages (
		id INT(6) AUTO_INCREMENT PRIMARY KEY,
		sender_id INT(6) NOT NULL,
		receiver_id INT(6) NOT NULL,
		song_id TEXT NOT NULL,
		FOREIGN KEY (sender_id) REFERENCES users(id) on delete cascade,
		FOREIGN KEY (receiver_id) REFERENCES users(id) on delete cascade
		);";
		if($conn->query($sql) == TRUE){
			echo "Table messages created successfully </br>";
		}
		else{
			echo "Error creating table messages: ".$conn->error;
		}
		
		$sql = "CREATE TABLE friends (
		id INT(6) AUTO_INCREMENT PRIMARY KEY,
		user_id INT(6) NOT NULL,
		friend_id INT(6) NOT NULL,
		FOREIGN KEY (user_id) REFERENCES users(id) on delete cascade,
		FOREIGN KEY (friend_id) REFERENCES users(id) on delete cascade
		);";
		if($conn->query($sql) == TRUE){
			echo "Table friends created successfully </br>";
		}
		else{
			echo "Error creating table friends: ".$conn->error;
		}
		
		
		$conn->close();
?>
