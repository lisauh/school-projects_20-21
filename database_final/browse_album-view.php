<?php

	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	$album_id = $_GET["a_id"];

	// if no album, redirect back
	if(empty($album_id)){
		header("location: browse_album.php");
		exit;
	}

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// check album_id exists
	$sql_check = "SELECT * from albums WHERE album_id=$album_id";
	$check = mysqli_query($conn,$sql_check);
	if(mysqli_num_rows($check)==0){
		header("location: browse_album.php");
		exit;
	}

	$sql_albuminfo = "SELECT a.album_title as album_title, a.musician_id as rec_artist_id, m.professional_name as rec_artist, a.release_year as release_year, GROUP_CONCAT(g.genre_name SEPARATOR ', ') as genre_name FROM albums a INNER JOIN musicians m ON m.musician_id=a.musician_id INNER JOIN album_genres ag ON ag.album_id=a.album_id INNER JOIN genres g ON ag.genre_id=g.genre_id WHERE a.album_id=".$album_id." GROUP BY a.album_id";

	$albuminfo = mysqli_query($conn,$sql_albuminfo);

	if($row=mysqli_fetch_assoc($albuminfo)){
		$album_title = $row["album_title"];
		$rec_artist_id = $row["rec_artist_id"];
		$rec_artist = $row["rec_artist"];
		$release_year = $row["release_year"];
		$genre = $row["genre_name"];
	}

	// init song tbl var
	$song_tbl = '';

	$sql_songs = "SELECT s.song_id as song_id, s.song_title as song_title, GROUP_CONCAT(DISTINCT(g.genre_name) SEPARATOR ', ') as genre_name FROM songs s INNER JOIN song_genres sg ON s.song_id = sg.song_id INNER JOIN genres g ON g.genre_id=sg.genre_id  WHERE s.album_id=".$album_id." GROUP BY s.song_id ORDER BY s.song_id";

	$result_songs = mysqli_query($conn,$sql_songs);
	if(mysqli_num_rows($result_songs)>0){
		$song_tbl = '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th>Song Title</th>
			<th>Genre</th></tr>';
		while($srow = mysqli_fetch_array($result_songs)){
			$song_id = $srow["song_id"];
			$song_title = $srow["song_title"];
			$song_genre = $srow["genre_name"];

			$song_tbl .='
				<tr><td><a href="browse_song-view.php?s_id='.$song_id.'">'.$song_title.'</a></td>
				<td>'.$song_genre.'</td></tr>';
		}
		$song_tbl .= '</table></div>';
	} else {
		$song_tbl = 'No songs found';
	}


	// album in favs
	// check if in favs
	$sql_check = "SELECT * FROM acc_favorite_albums WHERE (account_id=".intval($_SESSION['id'])." AND album_id=".intval($album_id).")";
	$check = mysqli_query($conn,$sql_check);
	$checkrows = mysqli_num_rows($check);

	if($checkrows == 0){
		// not fav
		$foutput = '<button type="button" class="btn btn-danger btn-lg" id="fav-refresh">
			♡</button>';
	} else {
		// fav
		$foutput = '<button type="button" class="btn btn-danger btn-lg" id="fav-refresh">
			♥</button>';
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
		<!-- for heart icon -->
		<script src='https://kit.fontawesome.com/a076d05399.js'></script>

		<!-- My External CSS -->
		<link rel="stylesheet" href="myexternal.css">
		
		<title>mmmusicdb: browse albums - view</title>

		<script>
			$(document).ready(function(){
				$("button").click(function(){
					$.ajax({
						url: "browse_album-view-back.php?a_id=<?php echo $album_id;?>",
						success: function(result){
							$("#favbox").html(result);
						}
					});
				});
			});
		</script>
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
							<a class="nav-link dropdown-toggle active" id="browsedrop" data-toggle="dropdown">Browse Music</a>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="browse_song.php">Browse Songs</a>
								<a class="dropdown-item active" href="browse_album.php">Browse Albums</a>
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
			<!-- status -->
			<div class="row">
				<div class="col-12 text-right user-status">
					<?php echo "Currently logged in as " . $_SESSION["username"];?> | 
					<a href="userlogout.php">Log out</a>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<h1>View Album</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!--placeholder-->
				</div>

				<div class="col-8">
					<div class="text-right" id="favbox">
						<?php echo $foutput;?>
					</div>

					<h3><?php echo $album_title;?></h3>
					<p>
						<b>Recording artist: </b>
						<?php echo '<a href="browse_musician-view.php?m_id='.$rec_artist_id.'">'.$rec_artist.'</a>';?>
						<br>
						<b>Release year: </b>
						<?php echo $release_year;?>
						<br>
						<b>Genres: </b>
						<?php echo $genre;?>
					</p>
					<h3>Tracklist</h3>
					<?php echo $song_tbl;?>
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