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

	// set vars from form
	$song_id = $_POST["song_id"];
	$old_genre = $_POST["old_genre"];
	$old_sw = $_POST["old_sw"];
	$old_sp = $_POST["old_sp"];

	$new_title = $_POST["song_title"];
	$new_genre = $_POST["genre"];
	$new_sw = $_POST["song_writer"];
	$new_sp = $_POST["song_producer"];

	// genres no change
	$genre_intersect = array_intersect($old_genre,$new_genre);
	// genres cd
	$genre_delete = array_diff($old_genre,$genre_intersect);
	$genre_insert = array_diff($new_genre,$genre_intersect);
	// sw no change
	$sw_intersect = array_intersect($old_sw,$new_sw);
	// sw cd
	$sw_delete = array_diff($old_sw,$sw_intersect);
	$sw_insert = array_diff($new_sw,$sw_intersect);
	// sp no change
	$sp_intersect = array_intersect($old_sp,$new_sp);
	// sp cd
	$sp_delete = array_diff($old_sp,$sp_intersect);
	$sp_insert = array_diff($new_sp,$sp_intersect);

	// init output
	$output = '';

	// update for title
	$sql_alb = "UPDATE songs SET song_title='$new_title' WHERE song_id=$song_id";

	if(mysqli_query($conn,$sql_alb)){
		$output .= 'Song title successfully updated.<br>';
	} else {
		$output .= 'Error updating song title.<br>';
	}

	// insert, delete for genre
	foreach($genre_delete as $gdval){
		$sql_gdelete = "DELETE FROM song_genres WHERE(song_id=$song_id AND genre_id=$gdval)";
		if(mysqli_query($conn,$sql_gdelete)){
			$output .= $gdval.": ";
			$output .= 'Old genre successfully deleted.<br>';
		} else {
			$output .= $gdval.": ";
			$output .= 'Error deleting old genre.<br>';
		}
	}
	foreach($genre_insert as $gival){
		$sql_ginsert = "INSERT INTO song_genres (song_id,genre_id) VALUES($song_id,$gival)";
		if(mysqli_query($conn,$sql_ginsert)){
			$output .= $gival.": ";
			$output .= 'New genre successfully inserted.<br>';
		} else {
			$output .= $gival.": ";
			$output .= 'Error inserting new genre.<br>';
		}
	}

	// insert, delete for sw
	foreach($sw_delete as $swdval){
		$sql_swdelete = "DELETE FROM song_writers WHERE(song_id=$song_id AND musician_id=$swdval)";
		if(mysqli_query($conn,$sql_swdelete)){
			$output .= $swdval.": ";
			$output .= 'Old song writer successfully deleted.<br>';
		} else {
			$output .= $swdval.": ";
			$output .= 'Error deleting old song writer.<br>';
		}
	}
	foreach($sw_insert as $swival){
		$sql_swinsert = "INSERT INTO song_writers (song_id,musician_id) VALUES($song_id,$swival)";
		if(mysqli_query($conn,$sql_swinsert)){
			$output .= $swival.": ";
			$output .= 'New song writer successfully inserted.<br>';
		} else {
			$output .= $swival.": ";
			$output .= 'Error inserting new song writer.<br>';
		}
	}

	// insert, delete for sp
	foreach($sp_delete as $spdval){
		$sql_spdelete = "DELETE FROM song_producers WHERE(song_id=$song_id AND musician_id=$spdval)";
		if(mysqli_query($conn,$sql_spdelete)){
			$output .= $spdval.": ";
			$output .= 'Old song producer successfully deleted.<br>';
		} else {
			$output .= $spdval.": ";
			$output .= 'Error deleting old song producer.<br>';
		}
	}
	foreach($sp_insert as $spival){
		$sql_spinsert = "INSERT INTO song_producers (song_id,musician_id) VALUES($song_id,$spival)";
		if(mysqli_query($conn,$sql_spinsert)){
			$output .= $spival.": ";
			$output .= 'New song producer successfully inserted.<br>';
		} else {
			$output .= $swival.": ";
			$output .= 'Error inserting new song producer.<br>';
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

		<title>mmmusicdb: edit song</title>
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













