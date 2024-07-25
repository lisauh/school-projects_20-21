<?php
	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	$account_id=$_SESSION["id"];
	$username=$_SESSION["username"];
	$admin = $_SESSION["admin"];

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	$message = '';

	$sql_userinfo = "SELECT user_password,email FROM accounts WHERE account_id=$account_id";

	$userinfo = mysqli_query($conn,$sql_userinfo);
	if($row=mysqli_fetch_assoc($userinfo)){
		$email = $row["email"];
		$user_password = $row["user_password"];
	}

	if(count($_POST)>0) {
		if(trim($_POST["current_password"]) == $user_password){
			if(strlen(trim($_POST["new_password"]))>=6){
				if(trim($_POST["new_password"]) == trim($_POST["confirm_password"])){
					$sql_change = "UPDATE accounts SET user_password='".$_POST["new_password"]."' WHERE account_id=$account_id";
					if(mysqli_query($conn,$sql_change)){
						$message = "Password changed successfully";
					} else {
						$message = "Error changing password";
					}
				} else {
	 				$message = "New passwords do not match";
	 			}
	 		} else {
	 			$message = "Password must have at least 6 characters";
	 		}
		} else {
			$message = "Current password incorrect";
		}
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
		
		<title>mmmusicdb: account information</title>
	</head>

	<body>
		<div class="container-fluid">

			<!--navigation-->
			<nav class="navbar navbar-expand-md navbar-light bg-light">
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar"><span class="navbar-toggler-icon"></span></button>
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="home.php">Home</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" id="favdrop" data-toggle="dropdown">Favorites</a>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="favsongs.php">Favorite Songs</a>
								<a class="dropdown-item" href="favalbums.php">Favorite Albums</a>
								<a class="dropdown-item" href="favmusicians.php">Favorite Musicians</a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" id="browsedrop" data-toggle="dropdown">Browse Music</a>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="browse_song.php">Browse Songs</a>
								<a class="dropdown-item" href="browse_album.php">Browse Albums</a>
								<a class="dropdown-item" href="browse_musician.php">Browse Musicians</a>
							</div>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="accountinfo.php">Account Information</a>
						</li>
						<li>
							<a class="nav-link" href="add-edit.php">Add/Edit Music</a>
						</li>
					</ul>
				</div>
			</nav>

			<div class="row">
				<div class="col-12 text-right user-status">
					<?php echo "Currently logged in as " . $_SESSION["username"];?> | 
					<a href="userlogout.php">Log out</a>
				</div>
			</div>

			<!--title-->
			<div class="row">
				<div class="col-12">
					<h1>Account Information</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!--placeholder-->
				</div>

				<div class="col-8">
					<form name="newpw" method="POST" action="">
						<!-- info -->
						<div class="form-group row">
							<div class="col-4">
								<b>Username:</b>
							</div>
							<div class="col-8">
								<?php echo $username;?>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-4">
								<b>Email Address:</b>
							</div>
							<div class="col-8">
								<?php echo $email;?>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-4">
								<b>Administrative Privileges:</b>
							</div>
							<div class="col-8">
								<?php
									if($admin){
										echo "Yes";
									} else {
										echo "No";
									}
								?>
							</div>
						</div>
						<!--edit pw-->
						<div class="form-group">
							<h3>Change Password</h3>
							<span class="form-text error-text">
								<?php
									if(isset($message)){
										echo $message;
									}
								?>
							</span>
						</div>
						<div class="form-group row">
							<label for="new_password" class="col-3 col-form-label">New Password</label>
							<div class="col-9">
      							<input type="password" class="form-control" id="new_password" name="new_password" pattern="[^' ']+" maxlength="45" required>
    						</div>
    					</div>
    					<div class="form-group row">
							<label for="confirm_password" class="col-3 col-form-label">Confirm Password</label>
							<div class="col-9">
      							<input type="password" class="form-control" id="confirm_password" name="confirm_password" pattern="[^' ']+" maxlength="45" required>
    						</div>
    					</div>
    					<div class="form-group row">
							<label for="current_password" class="col-3 col-form-label">Current Password</label>
							<div class="col-9">
      							<input type="password" class="form-control" id="current_password" name="current_password" pattern="[^' ']+" maxlength="45" required>
    						</div>
    					</div>
    					<div class="form-group text-center">
    						<input type="submit" name="submit" class="btn btn-primary" value="Change Password">
    					</div>
    				</form>

				</div>

				<div class="col-2">
					<!--placeholder-->
				</div>
			</div>

			<!--footer-->
			<footer class="footer">
				<div class="row mt-4">
					<div class="col-12 mb-3 text-center">
						<p>
							website and database by Lisa Hu
							<br>
							<a href="http://lh656.rutgers-sci.domains/database/dbproj/resources.php">resources consulted</a>
						</p>
					</div>
				</div>
			</footer>

		</div>
	</body>

</html>
