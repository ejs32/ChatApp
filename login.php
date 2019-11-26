<?php

//include('database_connection.php');

session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html>  
    <head>  
        <title>Chat Application</title>  
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>  
    <body>  
        <div class="container">
			<br />
			
			<h3 align="center">Chat Application</h3><br />
			<br />
			<div class="panel panel-default">
  				<div class="panel-heading">Chat Application Login</div>
				<div class="panel-body">

					<form method="post">
						<div class="form-group">
							<label>Enter Username</label>
							<input type="text" name="username" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Enter Password</label>
							<input type="password" name="password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" class="btn btn-info" value="Login" />
						</div>
						<div align="center">
							<a href="registration.php">Register</a>
						</div>
					</form>
					<br />
					<br />
					<br />
					<br />
				</div>
			</div>
		</div>

    </body>  
</html>
<?php
	if(isset($_POST['username']) && isset($_POST['password'])){
		$user = $_POST['username'];
		$pass = $_POST['password'];
		//do further validation?
		try{
			require('database_connection.php');
			//$username, $password, $host, $database
			$query = "
				SELECT * FROM users 
				WHERE username = :username LIMIT 1
			";
			$stmt = $connect->prepare($query);
			$stmt->execute(array(":username"=>$user));
			print_r($stmt->errorInfo());
			$results = $stmt->fetch(PDO::FETCH_ASSOC);
			echo var_export($results, true);
			if($results && count($results) > 0){
				$hash = password_hash($pass, PASSWORD_DEFAULT);
				if(password_verify($pass, $hash)){
					echo "Welcome, " . $results["username"];
					echo "[" . $results["user_id"] . "]";
					$user = array("id"=> $results['id'],
								"name"=> $results['username']
								);
					$_SESSION['user'] = $user;
					$_SESSION['user_id'] = $results['user_id'];
					$_SESSION['username'] = $results['username'];
					$sub_query = "
						INSERT INTO login_details 
						(user_id) 
						VALUES ('".$results['user_id']."')
					";
					$statement = $connect->prepare($sub_query);
					$statement->execute();
					$_SESSION['login_details_id'] = $connect->lastInsertId();
					echo var_export($user, true);
					echo var_export($_SESSION, true);
					header("Location: landingpage.php");
					
				}
				else{
					echo "Invalid password";
				}
			}
			else{
					echo "Invalid username";
			}
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
	}
?>
