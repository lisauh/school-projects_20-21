<?php

	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	$account_id = $_SESSION["id"];

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));


	// init output
	$output = '';

	$sql = "SELECT a.album_id as album_id, a.album_title as album_title FROM albums a INNER JOIN acc_favorite_albums fa ON a.album_id=fa.album_id WHERE fa.account_id=$account_id ORDER BY a.album_title";
	
	if($result = mysqli_query($conn,$sql)){
		if(mysqli_num_rows($result)>0){
			$output = '<form action="favalbums-delete.php" method="POST">
				<div class="row"><div class="col-12 text-center border-bottom mb-3"><h5>Albums</h5></div></div>';
			while($row = mysqli_fetch_assoc($result)){
				$album_id = $row["album_id"];
				$album_title = $row["album_title"];

				$output .= '<div class="form-group text-left row"><div class="col-10"><div class="form-check">
  					<input class="form-check-input" type="checkbox" name="album_id_arr[]" value="'.$album_id.'" id="'.$album_id.'">
  					<label class="form-check-label ml-3" for="'.$album_id.'">'.$album_title.'</label></div></div>
  					<div class="col-2 text-center"><a class="btn btn-outline-dark btn-sm" href="browse_album-view.php?a_id='.$album_id.'" role="button">View</a></div></div>';
			}
			$output .= '<div class="form-group text-center border-top pt-3">
							<input type="submit" class="btn btn-danger" value="Remove From Favorites">
						</div></form>';
		} else {
			$output = "No albums found.";
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
		
		<title>mmmusicdb: favorite albums</title>
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
							<a class="nav-link dropdown-toggle active" id="favdrop" data-toggle="dropdown">Favorites</a>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="favsongs.php">Favorite Songs</a>
								<a class="dropdown-item active" href="favalbums.php">Favorite Albums</a>
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
					<h1>Favorite Albums</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!--placeholder-->
				</div>

				<div class="col-8 text-center">
					<?php echo $output;?>
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