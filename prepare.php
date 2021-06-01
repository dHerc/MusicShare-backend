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
	echo "Prepared successfully";
}
else
	echo "Wrong password";
?>