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

	$song_id = intval($_POST["song_id"]);

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// init output
	$output = '';

	// first delete from linking tables
	$sql_sg = "DELETE FROM song_genres WHERE song_id=$song_id";
	if(mysqli_query($conn,$sql_sg)){
		$output .= "Deleted entries from song genres table<br>";
	} else {
		$output .= "Error deleting entries in song genres table<br>";
	}
	$sql_sw = "DELETE FROM song_writers WHERE song_id=$song_id";
	if(mysqli_query($conn,$sql_sw)){
		$output .= "Deleted entries from song writers table<br>";
	} else {
		$output .= "Error deleting entries in song writers table<br>";
	}
	$sql_sp = "DELETE FROM song_producers WHERE song_id=$song_id";
	if(mysqli_query($conn,$sql_sp)){
		$output .= "Deleted entries from song producers table<br>";
	} else {
		$output .= "Error deleting entries in song producers table<br>";
	}

	// delete from songs
	$sql_s = "DELETE FROM songs WHERE song_id=$song_id";
	if(mysqli_query($conn,$sql_s)){
		$output .= $song_id.": Deleted from songs table";
	} else {
		$output .= $song_id.": Error deleting from songs table";
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

		<title>mmmusicdb: delete song</title>
	</head>

	<body>
		<!-- main page -->
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
				<div class="col-12 text-center">
					<?php echo $output;?>
					<p><a href="ae_albums.php">Return to Add/Edit Albums</a></p>
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

