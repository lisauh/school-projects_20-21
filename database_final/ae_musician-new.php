<?php
	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	// access restricted
	if($_SESSION["admin"]==0){
		header("location: add-edit.php");
		exit;
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

		<title>mmmusicdb: add new musician</title>
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
							<a class="nav-link" href="accountinfo.php">Account Information</a>
						</li>
						<li>
							<a class="nav-link active" href="add-edit.php">Add/Edit Music</a>
						</li>
					</ul>
				</div>
			</nav>
			<!-- status -->
			<div class="row">
				<div class="col-12 text-right user-status">
					<?php echo "Currently logged in as " . $_SESSION["username"];?> | 
					<a href="userlogout.php">Log out</a>
				</div>
			</div>

			<!--main-->
			<div class="row">
				<div class="col-12">
					<h1>Add New Musician</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!-- placeholder -->
				</div>

				<div class="col-8">
					<form action="ae_musician-new-insert.php" method="POST">
						<div class="form-group">
							<label>Professional Name</label>
							<input type="text" name="pro_name" class="form-control" maxlength="45" required>
						</div>
						<div class="form-group">
							<label>First Name</label>
							<input type="text" name="fname" class="form-control" maxlength="45">
						</div>
						<div class="form-group">
							<label>Middle Initial</label>
							<input type="text" name="mi" class="form-control" maxlength="5">
						</div>
						<div class="form-group">
							<label>Last Name</label>
							<input type="text" name="lname" class="form-control" maxlength="45">
						</div>
						<div class="form-group">
							<label>Date of Birth (YYYY-MM-DD)</label>
							<input type="text" name="dob" class="form-control" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))">
						</div>
						<!--submit button-->
						<div class="form-group text-center">
							<input type="submit" class="btn btn-primary" value="Add New Musician">
						</div>
					</form>
				</div>

				<div class="col-2">
					<!-- placeholder -->
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




