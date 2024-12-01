<?php
    session_start();
	if(isset($_SESSION['error']))
	{
		echo "<p style=\"color:red;\">".$_SESSION['error']."</p>";
		unset($_SESSION['error']);
	}
    $_SESSION['user_authentication'] = '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Game Database Application</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
	<section id="login">
		<h2>Login</h2>
		<p>Don't have an account? <a href="home.php">Register Here</a>.</p>
		
		<form id="loginForm" action="login_submit.php" method="post">
			<label for="username">Username:</label>
			<input type="text" id="username" name="username" required>
			
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" required>
			
			<button type="submit">Log In</button>
		</form>
	</section>
</body>
