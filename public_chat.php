<?php

//public_chat.php

include('database_connection.php');

session_start();

if($_POST["action"] == "insert_data")
{
	$data = array(
		':from_user_id'		=>	$_SESSION["user_id"],
		':chat_message'		=>	$_POST['chat_message'],
		':status'			    =>	'1'
	);
	//echo json_encode($data);
	$query = "
	INSERT INTO chat_message 
	(from_user_id, chat_message, status) 
	VALUES (:from_user_id, :chat_message, :status)
	";

	$statement = $connect->prepare($query);

	if($statement->execute($data))
	{
		//echo fetch_public_chat_history($connect);
		$data = array(
		'from_user_id'		=>	$_SESSION["user_id"],
		'chat_message'		=>	$_POST['chat_message'],
		'status'			    =>	'1'
	);
		echo json_encode($data);
	}
	else{
		$data = array(
		'from_user_id'		=>	-1,
		'chat_message'		=>	var_export($statement->errorInfo(),true),
		'status'			=>	'1'
	);
	echo json_encode($data);
	}

}

if($_POST["action"] == "fetch_data")
{
  fetch_public_chat_history();
}


?>