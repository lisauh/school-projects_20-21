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

	// get post data
	$album_id = $_POST["album_id"];
	$song_title = $_POST["song_title"];
	$genre = $_POST["genre"];
	$sw = $_POST["song_writer"];
	$sp = $_POST["song_producer"];

	// init output
	$output = '';

	// init new id
	$s_id = 0;

	// manually increment id
	$sql_incr_id = "SELECT MAX(song_id) as max_id FROM songs";
	$incr_id = mysqli_query($conn,$sql_incr_id);
	if($idrow = mysqli_fetch_assoc($incr_id)){
		$s_id = $idrow["max_id"]+1;
		$output .= "New song id ".$s_id."<br>";
	} else {
		$output .= "Error getting new song id<br>";
	}

	// insert query songs
	$sql_sinsert = "INSERT INTO songs (song_id,song_title,album_id) VALUES ($s_id,'$song_title',$album_id)";
	if(mysqli_query($conn,$sql_sinsert)){
		$output .= "Successfully inserted new entry in songs<br>";
	} else {
		$output .= "Error inserting new entry in songs: ".mysqli_error($conn)."<br>";
	}

	// insert query song genres
	foreach($genre as $gval){
		$sql_sginsert = "INSERT INTO song_genres (song_id,genre_id) VALUES ($s_id,$gval)";
		if(mysqli_query($conn,$sql_sginsert)){
			$output .= $gval.": Successfully inserted new song genre entry<br>";
		} else {
			$output .= $gval.": Error inserting new song genre entry<br>";
		}
	}

	// insert query song writers
	foreach($sw as $swval){
		$sql_swinsert = "INSERT INTO song_writers (song_id,musician_id) VALUES ($s_id,$swval)";
		if(mysqli_query($conn,$sql_swinsert)){
			$output .= $swval.": Successfully inserted new song writer entry<br>";
		} else {
			$output .= $swval.": Error inserting new song writer entry<br>";
		}
	}

	// insert query song producers
	foreach($sp as $spval){
		$sql_spinsert = "INSERT INTO song_producers (song_id,musician_id) VALUES ($s_id,$spval)";
		if(mysqli_query($conn,$sql_spinsert)){
			$output .= $spval.": Successfully inserted new song producer entry<br>";
		} else {
			$output .= $swval.": Error inserting new song producer entry<br>";
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

		<title>mmmusicdb: add new song</title>
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