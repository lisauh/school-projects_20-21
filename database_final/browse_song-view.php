<?php

	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	$song_id = $_GET["s_id"];

	// if no song, redirect back
	if(empty($song_id)){
		header("location: browse_song.php");
		exit;
	}

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// check song_id exists
	$sql_check = "SELECT * from songs WHERE song_id=$song_id";
	$check = mysqli_query($conn,$sql_check);
	if(mysqli_num_rows($check)==0){
		header("location: browse_song.php");
		exit;
	}

	$sql_songinfo = "SELECT s.song_title as song_title, a.album_id as album_id, a.album_title as album_title, a.musician_id as rec_artist_id, m.professional_name as rec_artist, GROUP_CONCAT(DISTINCT(g.genre_name) SEPARATOR ', ') as genre_name, GROUP_CONCAT(DISTINCT(sw.musician_id) SEPARATOR ' ') as sw_id, GROUP_CONCAT(DISTINCT(sp.musician_id) SEPARATOR ' ') as sp_id FROM songs s INNER JOIN albums a ON s.album_id=a.album_id INNER JOIN musicians m ON a.musician_id=m.musician_id INNER JOIN song_genres sg ON s.song_id=sg.song_id INNER JOIN genres g ON g.genre_id=sg.genre_id INNER JOIN song_writers sw ON sw.song_id=s.song_id INNER JOIN song_producers sp ON sp.song_id=s.song_id WHERE s.song_id=$song_id GROUP BY s.song_id";

	$songinfo = mysqli_query($conn,$sql_songinfo);

	if($row=mysqli_fetch_assoc($songinfo)){
		$song_title = $row["song_title"];
		$album_id = $row["album_id"];
		$album_title = $row["album_title"];
		$rec_artist_id = $row["rec_artist_id"];
		$rec_artist = $row["rec_artist"];
		$genre = $row["genre_name"];
		// how many sw, sp
		if(strpos($row["sw_id"],' ')){
			$sw_id_arr = explode(' ',$row["sw_id"]);
		} else {
			$sw_id_arr = [$row["sw_id"]];
		}
		if(strpos($row["sp_id"],' ')){
			$sp_id_arr = explode(' ',$row["sp_id"]);
		} else {
			$sp_id_arr = [$row["sp_id"]];
		}
	}

	// init these
	$sw_arr = [];
	$sp_arr = [];

	foreach($sw_id_arr as $swval){
		$sql_sw = "SELECT m.professional_name as sw_name FROM musicians m INNER JOIN song_writers sw ON sw.musician_id=m.musician_id WHERE (sw.song_id=$song_id AND sw.musician_id=$swval)";
		$result_sw = mysqli_query($conn,$sql_sw);
		if($swrow = mysqli_fetch_assoc($result_sw)){
			$sw_name = $swrow["sw_name"];
			array_push($sw_arr, '<a href="browse_musician-view.php?m_id='.$swval.'">'.$sw_name.'</a>');
		}
	}
	$sw_output = implode(', ', $sw_arr);

	foreach($sp_id_arr as $spval){
		$sql_sp = "SELECT m.professional_name as sp_name FROM musicians m INNER JOIN song_producers sp ON sp.musician_id=m.musician_id WHERE (sp.song_id=$song_id AND sp.musician_id=$spval)";
		$result_sp = mysqli_query($conn,$sql_sp);
		if($sprow = mysqli_fetch_assoc($result_sp)){
			$sp_name = $sprow["sp_name"];
			array_push($sp_arr, '<a href="browse_musician-view.php?m_id='.$spval.'">'.$sp_name.'</a>');
		}
	}
	$sp_output = implode(', ', $sp_arr);


	// song in favs
	// check if in favs
	$sql_check = "SELECT * FROM acc_favorite_songs WHERE (account_id=".intval($_SESSION['id'])." AND song_id=".intval($song_id).")";
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
		
		<title>mmmusicdb: browse songs - view</title>

		<script>
			$(document).ready(function(){
				$("button").click(function(){
					$.ajax({
						url: "browse_song-view-back.php?s_id=<?php echo $song_id;?>",
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
								<a class="dropdown-item active" href="browse_song.php">Browse Songs</a>
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
			<!-- status -->
			<div class="row">
				<div class="col-12 text-right user-status">
					<?php echo "Currently logged in as " . $_SESSION["username"];?> | 
					<a href="userlogout.php">Log out</a>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<h1>View Song</h1>
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

					<h3><?php echo $song_title;?></h3>
					<p>
						<b>From album: </b>
						<?php echo '<a href="browse_album-view.php?a_id='.$album_id.'">'.$album_title.'</a>';?>
						<br>
						<b>Recording artist: </b>
						<?php echo '<a href="browse_musician-view.php?m_id='.$rec_artist_id.'">'.$rec_artist.'</a>';?>
						<br>
						<b>Genres: </b>
						<?php echo $genre;?>
						<br>
						<b>Song writers: </b>
						<?php echo $sw_output;?>
						<br>
						<b>Song producers: </b>
						<?php echo $sp_output;?>
					</p>
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