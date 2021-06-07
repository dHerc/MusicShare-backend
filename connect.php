<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$host = $url["host"];
$db_user = $url["user"];
$db_password = $url["pass"];
$db_name = substr($url["path"], 1);
?>
