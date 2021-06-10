<?php
function error($error,$errorCode)
{
	header("Access-Control-Allow-Origin: *");
	http_response_code($errorCode);
	$response = array();
	$response["error"]=$error;
	echo stripslashes(json_encode($response));
	exit();
}
?>
