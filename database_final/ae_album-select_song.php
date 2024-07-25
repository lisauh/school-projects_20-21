<?php

	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	// set song_id from get
	$song_id = $_GET["s_id"];

	// if no song_id, redirect
	if(empty($song_id)){
		header("location: ae_albums.php");
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
		header("location: ae_albums.php");
		exit;
	}

	$sql_songinfo = "SELECT s.song_title as song_title, s.album_id as album_id, a.album_title as album_title, GROUP_CONCAT(DISTINCT(sg.genre_id) SEPARATOR ' ') as genre_id, GROUP_CONCAT(DISTINCT(sw.musician_id) SEPARATOR ' ') as song_writer_id, GROUP_CONCAT(DISTINCT(sp.musician_id) SEPARATOR ' ') as song_producer_id FROM songs s INNER JOIN albums a ON s.album_id=a.album_id INNER JOIN song_genres sg ON s.song_id=sg.song_id INNER JOIN song_writers sw ON sw.song_id=s.song_id INNER JOIN song_producers sp ON sp.song_id=s.song_id WHERE s.song_id=$song_id GROUP BY s.song_id";

	$songinfo = mysqli_query($conn,$sql_songinfo);

	if($row=mysqli_fetch_assoc($songinfo)){
		$song_title = $row["song_title"];
		$album_id = $row["album_id"];
		$album_title = $row["album_title"];
		// how many genres, sw, sp
		if(strpos($row["genre_id"],' ')){
			$genre_id_arr = explode(' ',$row["genre_id"]);
		} else {
			$genre_id_arr = [$row["genre_id"]];
		}
		if(strpos($row["song_writer_id"],' ')){
			$sw_id_arr = explode(' ',$row["song_writer_id"]);
		} else {
			$sw_id_arr = [$row["song_writer_id"]];
		}
		if(strpos($row["song_producer_id"],' ')){
			$sp_id_arr = explode(' ',$row["song_producer_id"]);
		} else {
			$sp_id_arr = [$row["song_producer_id"]];
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
		<!-- for bootstrap-select-->
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

		<!-- My External CSS -->
		<link rel="stylesheet" href="myexternal.css">

		<title>mmmusicdb: edit song</title>

		<!-- popup to confirm delete -->
		<script>
			function confirmDelete(){
				var agree=confirm("Are you sure you want to delete this song?");
				if(agree){
					return true;
				} else {
					return false;
				}
			}
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

			<div class="row">
				<div class="col-12">
					<h1>Edit Song</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!--placeholder-->
				</div>

				<div class="col-8">
					<form action="ae_album-select_song-update.php" method="POST">
						<!-- song id, genre ids, sw ids, sp ids -->
						<input type="hidden" name="song_id" value="<?php echo $song_id;?>">
						<?php
							foreach($genre_id_arr as $gval){
								echo '<input type="hidden" name="old_genre[]" value='.intval($gval).'>';
							}
							foreach($sw_id_arr as $swval){
								echo '<input type="hidden" name="old_sw[]" value='.intval($swval).'>'; 
							}
							foreach($sp_id_arr as $spval){
								echo '<input type="hidden" name="old_sp[]" value='.intval($spval).'>';
							}
						?>
						<!--song title-->
						<div class="form-group">
							<label>Song Title</label>
							<input type="text" name="song_title" class="form-control" value="<?php echo $song_title;?>" required>
							<span class="form-text mr-2">
								From the album <?php echo '<a href="ae_album-select.php?a_id='.$album_id.'">'.$album_title.'</a>';?>
							</span>
						</div>
						<!--genre-->
						<div class="form-group">
							<label for="genre">Genre</label>
							<select class="selectpicker form-control" name="genre[]" id="genre" multiple required>
								<?php
									$sql_genres = "SELECT genre_id, genre_name FROM genres";
									$genres = mysqli_query($conn,$sql_genres);
									while($grow = mysqli_fetch_array($genres)){
										if(in_array($grow["genre_id"],$genre_id_arr)){
											echo "<option value=".$grow["genre_id"]." selected>".$grow["genre_name"]."</option>";
										} else {
											echo "<option value=".$grow["genre_id"].">".$grow["genre_name"]."</option>";
										}
									}
								?>
							</select>
						</div>
						<!--songwriter-->
						<div class="form-group">
							<label for="song_writer">Song Writer(s)</label>
							<select class="selectpicker form-control" name="song_writer[]" id="song_writer" data-live-search="true" multiple required>
								<?php
									$sql_sw = "SELECT musician_id as sw_id, professional_name,last_name FROM musicians";
									$sw = mysqli_query($conn,$sql_sw);
									while($swrow = mysqli_fetch_array($sw)){
										if(in_array($swrow["sw_id"], $sw_id_arr)){
											echo "<option value=".$swrow["sw_id"]." selected>".$swrow["professional_name"]." (".$swrow["last_name"].")</option>";
										} else {
											echo "<option value=".$swrow["sw_id"].">".$swrow["professional_name"]." (".$swrow["last_name"].")</option>";
										}
									}
								?>
							</select>
							<span class="mr-2"><a href="ae_musician-new.php" target="_blank">Add new musician</a></span>
						</div>
						<!--songproducer-->
						<div class="form-group">
							<label for="song_producer">Song Producer(s)</label>
							<select class="selectpicker form-control" name="song_producer[]" id="song_producer" data-live-search="true" multiple required>
								<?php
									$sql_sp = "SELECT musician_id as sp_id, professional_name,last_name FROM musicians";
									$sp = mysqli_query($conn,$sql_sp);
									while($sprow = mysqli_fetch_array($sp)){
										if(in_array($sprow["sp_id"], $sp_id_arr)){
											echo "<option value=".$sprow["sp_id"]." selected>".$sprow["professional_name"]." (".$sprow["last_name"].")</option>";
										} else {
											echo "<option value=".$sprow["sp_id"].">".$sprow["professional_name"]." (".$sprow["last_name"].")</option>";
										}
									}
								?>
							</select>
							<span class="mr-2"><a href="ae_musician-new.php" target="_blank">Add new musician</a></span>
						</div>
						<!--submit-->
						<div class="form-group text-center">
							<input type="submit" class="btn btn-primary" value="Update">
						</div>
					</form>
				</div>

				<div class="col-2">
					<!--placeholder-->
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-center">
					<hr>
					<form action="ae_album-select_song-delete.php" method="POST">
						<input type="hidden" name="song_id" value="<?php echo $song_id;?>">
						<div class="form-group">
							<input type="submit" class="btn btn-danger btn-lg" onClick="return confirmDelete()" value="Delete Song">
						</div>
					</form>
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