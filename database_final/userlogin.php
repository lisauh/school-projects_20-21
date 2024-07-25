<?php
	session_start();

	// check if already logged in
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		header("location: home.php");
		exit;
	}

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// login form init variables
	$username = "";
	$password = "";
	$username_err = "";
	$password_err = "";

	// processing form data
	if($_SERVER["REQUEST_METHOD"] == "POST"){

		// check username empty
		if(empty(trim($_POST["username"]))){
			$username_err = "Please enter username";
		} else{
			$username = trim($_POST["username"]);
		}

		// check password empty
		if(empty(trim($_POST["password"]))){
			$password_err = "Please enter password";
		} else{
			$password = trim($_POST["password"]);
		}

		// check username & password
		if(empty($username_err) && empty($password_err)){
			// sql select
			$sql = "SELECT account_id, username, user_password, admin FROM accounts WHERE username = ?";

			if($stmt = mysqli_prepare($conn, $sql)){
				// bind variables to statement
				mysqli_stmt_bind_param($stmt,"s",$param_username);
				$param_username = $username;
				// try to execute statement
				if(mysqli_stmt_execute($stmt)){
					mysqli_stmt_store_result($stmt);
					// check username exists
					if(mysqli_stmt_num_rows($stmt)==1){
						mysqli_stmt_bind_result($stmt,$account_id,$username,$user_password,$admin);
						if(mysqli_stmt_fetch($stmt)){
							if($password == $user_password){
								// username, password match
								session_start();
								// session data
								$_SESSION["loggedin"] = true;
								$_SESSION["id"] = $account_id;
								$_SESSION["username"] = $username;
								$_SESSION["admin"] = $admin;
								// redirect home
								header("location: home.php");
							} else{
								// password incorrect
								$password_err = "The password you entered is not valid";
							}
						}
					} else{
						// username doesn't exist
						$username_err = "No account found with that username";
					}
				} else {
					// other error
					echo "Something went wrong (???)";
				}
			}
			// close statement
			mysqli_stmt_close($stmt);
		}
	}
	mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">

		<!-- bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

		<!-- My External CSS -->
		<link rel="stylesheet" href="myexternal.css">

		<title>mmmusicdb: user login</title>
	</head>

	<body>
		<div class="container-fluid">
			<div class="row my-5">

				<!--spacer-->
				<div class="col-2 col-lg-3"></div>

				<div class="col-8 col-lg-6">
					<h1>Login</h1>

					<!--login form-->
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
						<!--username field-->
						<div class="form-group <?php echo (!empty($username_err)) ? 'has-error': ''; ?>">
							<label>Username</label>
							<input type="text" name="username" class="form-control" value="<?php echo $username; ?>" pattern="[a-zA-Z0-9_-]+" maxlength="45">
							<span class="form-text error-text">
								<?php echo $username_err; ?>
							</span>
						</div>
						<!--password field-->
						<div class="form-group <?php echo (!empty($password_err)) ? 'has-error': ''; ?>">
							<label>Password</label>
							<input type="password" name="password" class="form-control" pattern="[^' ']+">
							<span class="form-text error-text">
								<?php echo $password_err;?>
							</span>
						</div>
						<!--submit button-->
						<div class="form-group">
							<input type="submit" class="btn btn-primary" value="Login">
						</div>
					</form>

					<p class="text-right">
						Don't have an account? <a href="register.php">Register here</a>.
					</p>
				</div>

				<!--spacer-->
				<div class="col-2 col-lg-3"></div>
	</body>

</html>