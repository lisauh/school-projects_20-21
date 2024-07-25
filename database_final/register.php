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

	$email = "";
	$username = "";
	$password = "";
	$confirm_password = "";
	$email_err = "";
	$username_err = "";
	$password_err = "";
	$confirm_password_err = "";

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// validate username
		if(empty(trim($_POST["username"]))){
			$username_err = "Please enter a username.";
		} else {
			$sql_user = "SELECT account_id FROM accounts WHERE username=?";
			if($stmt_user = mysqli_prepare($conn,$sql_user)){
				mysqli_stmt_bind_param($stmt_user,"s",$param_username);
				$param_username = trim($_POST["username"]);
				if(mysqli_stmt_execute($stmt_user)){
					mysqli_stmt_store_result($stmt_user);
					if(mysqli_stmt_num_rows($stmt_user)==1){
						$username_err = "This username is already taken.";
					} else {
						$username = trim($_POST["username"]);
					}
				} else {
					echo "Something went wrong (???)";
				}
				mysqli_stmt_close($stmt_user);
			}
		}

		// validate email
		if(empty(trim($_POST["email"]))){
			$email_err = "Please enter an email address.";
		} else {
			$sql_email = "SELECT account_id FROM accounts WHERE email=?";
			if($stmt_email = mysqli_prepare($conn,$sql_email)){
				mysqli_stmt_bind_param($stmt_email,"s",$param_email);
				$param_email = trim($_POST["email"]);
				if(mysqli_stmt_execute($stmt_email)){
					mysqli_stmt_store_result($stmt_email);
					if(mysqli_stmt_num_rows($stmt_email)==1){
						$email_err = "There is already an account registered to this email.";
					} else {
						$email = trim($_POST["email"]);
					}
				} else {
					echo "Something went wrong (???)";
				}
				mysqli_stmt_close($stmt_email);
			}
		}

		// validate password
		if(empty(trim($_POST["password"]))){
			$password_err = "Please enter a password.";
		} elseif(strlen(trim($_POST["password"]))<6){
			$password_err = "Password must have at least 6 characters.";
		} else {
			$password = trim($_POST["password"]);
		}

		// validate confirm password
		if(empty(trim($_POST["confirm_password"]))){
			$confirm_password_err = "Please confirm password.";
		} else {
			$confirm_password = trim($_POST["confirm_password"]);
			if(empty($password_err) && ($password != $confirm_password)){
				$confirm_password_err = "Passwords did not match.";
			}
		}

		// insert
		if(empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
			// get new account_id (no auto incr b/c used as foreign key???)
			$sql_incr_id = "SELECT MAX(account_id) as max_account_id FROM accounts";
			$incr_id = mysqli_query($conn,$sql_incr_id);
			if($idrow = mysqli_fetch_assoc($incr_id)){
				$account_id = $idrow["max_account_id"]+1;
			} else {
				echo "Something went wrong (???)";
			}

			// insert
			$sql_insert = "INSERT INTO accounts (account_id,username,email,user_password,admin) VALUES (?,?,?,?,0)";
			if($stmt = mysqli_prepare($conn,$sql_insert)){
				mysqli_stmt_bind_param($stmt,"isss",$param_id,$param_username,$param_email,$param_password);
				$param_id = $account_id;
				$param_username = $username;
				$param_email = $email;
				$param_password = $password;

				if(mysqli_stmt_execute($stmt)){
					header("location: register-success.html");
				} else {
					echo "Something went wrong (???)";
				}
				mysqli_stmt_close($stmt);
			}

		}
		mysqli_close($conn);

	}


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

		<title>mmmusicdb: user registration</title>
	</head>

	<body>
		<div class="container-fluid">
			<div class="row my-5">

				<!--spacer-->
				<div class="col-2 col-lg-3"></div>

				<div class="col-8 col-lg-6">
					<h1>Registration</h1>

					<!-- registration form -->
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
						<div class="form-group <?php echo(!empty($username_err))? 'has-error' : '';?>">
							<label>Username</label>
							<input type="text" name="username" class="form-control" value="<?php echo $username;?>" pattern="[a-zA-Z0-9_-]+" maxlength="45">
							<span class="help-block error-text">
								<?php echo $username_err;?>
							</span>
						</div>

						<div class="form-group <?php echo(!empty($email_err)) ? 'has-error' : '';?>">
							<label>Email</label>
							<input type="email" name=email class="form-control" value="<?php echo $email;?>">
							<span class="help-block error-text">
								<?php echo $email_err;?>
							</span>
						</div>

						<div class="form-group <?php echo(!empty($password_err)) ? 'has-error' : '';?>">
							<label>Password</label>
							<input type="password" name="password" class="form-control" value="<?php echo $password;?>" pattern="[^' ']+" maxlength="45">
							<span class="help-block error-text">
								<?php echo $password_err;?>
							</span>
						</div>

						<div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error'  : '';?>">
							<label>Confirm Password</label>
							<input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password;?>" pattern="[^' ']+">
							<span class="help-block error-text">
								<?php echo $confirm_password_err;?>
							</span>
						</div>

						<div class="form-group">
							<input type="submit" class="btn btn-primary" value="Submit">
							<input type="reset" class="btn btn-light" value="Reset">
						</div>
					</form>

					<p class="text-right">
						Already have an account? <a href="userlogin.php">Login here</a>.
					</p>
				</div>

				<!--spacer-->
				<div class="col-2 col-lg-3"></div>
	</body>

</html>


