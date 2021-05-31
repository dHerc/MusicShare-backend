<?php
require_once "KeePassPHP/keepassphp/keepassphp.php";
use \KeePassPHP\KeePassPHP as KeePassPHP;
require_once "KeePassPHP/keepassphp/lib/filter.php";
use \KeePassPHP\AllFilter;
$pwd = "W5RkMdnMKPN9B7aC";
$ckey = KeePassPHP::masterKey();
KeePassPHP::addPassword($ckey, $pwd);
$error = null;
$file = "apis.kdbx";
$db = KeePassPHP::openDatabaseFile($file, $ckey, $error);
$filter = new AllFilter();
$arr = $db->toArray($filter)["Groups"][0]["Entries"];
foreach($arr as $item)
{
	echo json_encode($item["StringFields"]);
	echo PHP_EOL;
}
?>