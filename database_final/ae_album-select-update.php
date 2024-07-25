<?php
	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	$album_id = $_POST["album_id"];
	$old_genre = $_POST["old_genre"];

	$new_title = $_POST["album_title"];
	$new_artist = $_POST["rec_artist"];
	$new_year = $_POST["release_year"];
	$new_genre = $_POST["genre"];

	// genres no change
	$genre_intersect = array_intersect($old_genre,$new_genre);
	// genres cd
	$genre_delete = array_diff($old_genre,$genre_intersect);
	$genre_insert = array_diff($new_genre,$genre_intersect);

	// init output msg
	$output = '';

	// update for title, artist, year
	$sql_alb = "UPDATE albums SET album_title='$new_title', release_year='$new_year', musician_id='$new_artist' WHERE album_id=$album_id";

	if(mysqli_query($conn,$sql_alb)){
		$output .= 'Album title, recording artist, & year successfully updated.<br>';
	} else {
		$output .= 'Error updating album title, recording artist, & year.<br>';
	}

	// insert, delete for genre
	foreach($genre_delete as $gdval){
		$sql_gdelete = "DELETE FROM album_genres WHERE(album_id=$album_id AND genre_id=$gdval)";
		if(mysqli_query($conn,$sql_gdelete)){
			$output .= $gdval.": ";
			$output .= 'Old genre successfully deleted.<br>';
		} else {
			$output .= $gdval.": ";
			$output .= 'Error deleting old genre.<br>';
		}
	}
	foreach($genre_insert as $gival){
		$sql_ginsert = "INSERT INTO album_genres (album_id,genre_id) VALUES($album_id,$gival)";
		if(mysqli_query($conn,$sql_ginsert)){
			$output .= $gival.": ";
			$output .= 'New genre successfully inserted.<br>';
		} else {
			$output .= $gival.": ";
			$output .= 'Error inserting new genre.<br>';
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

		<title>mmmusicdb: edit album</title>
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
