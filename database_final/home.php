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

	// init variables fav genres
	$song_genre = "";
	$album_genre = "";

	// sql select for song, album genres
	$sql_sg = "SELECT g.genre_name,COUNT(fs.song_id) FROM acc_favorite_songs fs INNER JOIN song_genres sg ON fs.song_id=sg.song_id INNER JOIN genres g ON g.genre_id=sg.genre_id WHERE fs.account_id=? GROUP BY g.genre_id ORDER BY COUNT(fs.song_id) DESC, g.genre_id LIMIT 3";
	$sql_ag = "SELECT g.genre_name,COUNT(fa.album_id) FROM acc_favorite_albums fa INNER JOIN album_genres ag ON fa.album_id=ag.album_id INNER JOIN genres g ON g.genre_id=ag.genre_id WHERE fa.account_id=? GROUP BY g.genre_id ORDER BY COUNT(fa.album_id) DESC, g.genre_id LIMIT 3";

	// for fav song genres
	if($stmt_sg = mysqli_prepare($conn, $sql_sg)){
		mysqli_stmt_bind_param($stmt_sg,"i",$_SESSION["id"]);
		if(mysqli_stmt_execute($stmt_sg)){
			mysqli_stmt_store_result($stmt_sg);
			if(mysqli_stmt_num_rows($stmt_sg)>0){
				mysqli_stmt_bind_result($stmt_sg, $genre_sg, $sg_count);
				$song_genre = "<ul>";
				while(mysqli_stmt_fetch($stmt_sg)){
					$song_genre .= "<li>" . $genre_sg . "</li>";
				}
				$song_genre .= "</ul>";
			} else {
				$song_genre = "You don't have any favorite song genres.";
			}
		}
	} mysqli_stmt_close($stmt_sg);
	// for fav album genres
	if($stmt_ag = mysqli_prepare($conn, $sql_ag)){
		mysqli_stmt_bind_param($stmt_ag,"i",$_SESSION["id"]);
		if(mysqli_stmt_execute($stmt_ag)){
			mysqli_stmt_store_result($stmt_ag);
			if(mysqli_stmt_num_rows($stmt_ag)>0){
				mysqli_stmt_bind_result($stmt_ag, $genre_ag, $ag_count);
				$album_genre = "<ul>";
				while(mysqli_stmt_fetch($stmt_ag)){
					$album_genre .= "<li>" . $genre_ag . "</li>";
				}
				$album_genre .= "</ul>";
			} else {
				$album_genre = "You don't have any favorite album genres.";
			}
		}
	} mysqli_stmt_close($stmt_ag);

	// init variables fav cards
	$favsongs = "";
	$favalbums = "";
	$favmusicians = "";

	// sql selects for fav list cards
	$sql_songs = "SELECT s.song_title FROM acc_favorite_songs fs INNER JOIN songs s ON s.song_id=fs.song_id WHERE fs.account_id = ? ORDER BY RAND() DESC LIMIT 5";
	$sql_albums = "SELECT a.album_title FROM acc_favorite_albums fa INNER JOIN albums a ON a.album_id=fa.album_id WHERE fa.account_id = ? ORDER BY RAND() DESC LIMIT 5";
	$sql_musicians = "SELECT m.professional_name FROM acc_favorite_musicians fm INNER JOIN musicians m ON m.musician_id=fm.musician_id WHERE fm.account_id = ? ORDER BY RAND() LIMIT 5";

	// for fav songs
	if($stmt_s = mysqli_prepare($conn,$sql_songs)){
		mysqli_stmt_bind_param($stmt_s,"i",$_SESSION["id"]);
		if(mysqli_stmt_execute($stmt_s)){
			mysqli_stmt_store_result($stmt_s);
			if(mysqli_stmt_num_rows($stmt_s)>0){
				mysqli_stmt_bind_result($stmt_s,$song_title);
				$favsongs = "<ul class='list-group list-group-flush'>";
				while(mysqli_stmt_fetch($stmt_s)){
					$favsongs .= "<li class='list-group-item'>" . $song_title . "</li>";
				}
				$favsongs .= "<li class='list-group-item'>
					<a href='favsongs.php'>View All Favorite Songs</a></li></ul>";
			} else {
				$favsongs = "You don't have any favorite songs";
			}
		}
	} mysqli_stmt_close($stmt_s);
	// for fav albums
	if($stmt_a = mysqli_prepare($conn,$sql_albums)){
		mysqli_stmt_bind_param($stmt_a,"i",$_SESSION["id"]);
		if(mysqli_stmt_execute($stmt_a)){
			mysqli_stmt_store_result($stmt_a);
			if(mysqli_stmt_num_rows($stmt_a)>0){
				mysqli_stmt_bind_result($stmt_a,$album_title);
				$favalbums = "<ul class='list-group list-group-flush'>";
				while(mysqli_stmt_fetch($stmt_a)){
					$favalbums .= "<li class='list-group-item'>" . $album_title . "</li>";
				}
				$favalbums .= "<li class='list-group-item'>
					<a href='favalbums.php'>View All Favorite albums</a></li></ul>";
			} else {
				$favalbums = "You don't have any favorite albums";
			}
		}
	} mysqli_stmt_close($stmt_a);
	// for fav musicians
	if($stmt_m = mysqli_prepare($conn,$sql_musicians)){
		mysqli_stmt_bind_param($stmt_m,"i",$_SESSION["id"]);
		if(mysqli_stmt_execute($stmt_m)){
			mysqli_stmt_store_result($stmt_m);
			if(mysqli_stmt_num_rows($stmt_m)>0){
				mysqli_stmt_bind_result($stmt_m,$musician_name);
				$favmusicians = "<ul class='list-group list-group-flush'>";
				while(mysqli_stmt_fetch($stmt_m)){
					$favmusicians .= "<li class='list-group-item'>" . $musician_name . "</li>";
				}
				$favmusicians .= "<li class='list-group-item'>
					<a href='favmusicians.php'>View All Favorite Musicians</a></li></ul>";
			} else {
				$favmusicians = "You don't have any favorite musicians";
			}
		}
	} mysqli_stmt_close($stmt_m);

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
		
		<title>mmmusicdb: home</title>
	</head>

	<body>
		<div class="container-fluid">

			<!--navigation-->
			<nav class="navbar navbar-expand-md navbar-light bg-light">
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar"><span class="navbar-toggler-icon"></span></button>
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link active" href="home.php">Home</a>
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

			<!--welcome message, summary-->
			<div class="row">
				<div class="col-12">
					<?php echo "<h1>Welcome, " . $_SESSION["username"] . "!</h1>";?>
					<h3>Favorite song genres</h3>
					<p>
						<?php echo $song_genre;?>
					</p>
					<h3>Favorite album genres</h3>
					<p>
						<?php echo $album_genre;?>
					</p>
				</div>
			</div>

			<!--favorites panels-->
			<div class="row">
				<div class="col-12 col-md-4">
					<div class="card">
						<div class="card-body text-center">
							<h3>Favorite Songs</h3>
							<?php echo $favsongs;?>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4">
					<div class="card">
						<div class="card-body text-center">
							<h3>Favorite Albums</h3>
							<?php echo $favalbums;?>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4">
					<div class="card">
						<div class="card-body text-center">
							<h3>Favorite Musicians</h3>
							<?php echo $favmusicians;?>
						</div>
					</div>
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
