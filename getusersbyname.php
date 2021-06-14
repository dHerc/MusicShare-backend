<?php
require "error.php";
require "connect.php";

session_start();
header("Access-Control-Allow-Origin: *");
if(!isset($_POST['login']))
	{
		error("No login",500);
		exit();
	}
	
	$username = $_POST['login'];
	
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['db_user'], $GLOBALS['db_password'], $GLOBALS['db_name']);
	
	if ($conn->connect_errno!=0)
	{
		error($conn->error,500);
		exit();	
	}
	
	$sql = "SELECT * FROM users where login = '".$username."'";
	$result = $conn->query($sql);
	if($result){
		$response = array();
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
			$response[$i]['login']=$row['login'];
			$response[$i]['user_id']=$row['id'];
			$i=$i+1;
		}
		$result->free_result();
		header('Content-Type:application/json');
		$conn->close();
		echo json_encode($response);
	}
	else{
		error($conn->error,404);
		$conn->close();
		exit();	
	}
	exit();
?>
