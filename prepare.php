<?php
require "error.php";
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
		email TEXT NOT NULL,
		fb_user_id TEXT,
		google_user_id TEXT
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
		status TEXT NOT NULL,
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
