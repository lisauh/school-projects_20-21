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

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// album id
	$album_id = $_GET["a_id"];

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

		<title>mmmusicdb: add new song</title>
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
					<h1>Add New Song</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!-- placeholder -->
				</div>

				<div class="col-8">
					<form action="ae_album-select_song-new-insert.php" method="POST">
						<input type="hidden" name="album_id" value="<?php echo $album_id;?>">
						<!--song title-->
						<div class="form-group">
							<label>Song Title</label>
							<input type="text" name="song_title" class="form-control" required>
						</div>
						<!--genre-->
						<div class="form-group">
							<label for="genre">Genre</label>
							<select class="selectpicker form-control" name="genre[]" id="genre" multiple required>
								<?php
									$sql_genres = "SELECT genre_id, genre_name FROM genres";
									$genres = mysqli_query($conn,$sql_genres);
									while($grow = mysqli_fetch_array($genres)){
										echo "<option value=".$grow["genre_id"].">".$grow["genre_name"]."</option>";
									}
								?>
							</select>
						</div>
						<!--song writer-->
						<div class="form-group">
							<label for="song_writer">Song Writer(s)</label>
							<select class="selectpicker form-control" name="song_writer[]" id="song_writer" data-live-search="true" multiple required>
								<?php
									$sql_musicians = "SELECT musician_id, professional_name,last_name FROM musicians";
									$musicians = mysqli_query($conn,$sql_musicians);
									while($mrow = mysqli_fetch_array($musicians)){
										echo "<option value='".$mrow["musician_id"]."'>".$mrow["professional_name"]." (".$mrow["last_name"].")</option>";
									}
								?>
							</select>
							<span class="mr-2"><a href="ae_musician-new.php" target="_blank">Add new musician</a></span>
						</div>
						<!--song producer-->
						<div class="form-group">
							<label for="song_producer">Song Producer(s)</label>
							<select class="selectpicker form-control" name="song_producer[]" id="song_producer" data-live-search="true" multiple required>
								<?php
									$sql_musicians = "SELECT musician_id, professional_name,last_name FROM musicians";
									$musicians = mysqli_query($conn,$sql_musicians);
									while($mrow = mysqli_fetch_array($musicians)){
										echo "<option value='".$mrow["musician_id"]."'>".$mrow["professional_name"]." (".$mrow["last_name"].")</option>";
									}
								?>
							</select>
							<span class="mr-2"><a href="ae_musician-new.php" target="_blank">Add new musician</a></span>
						</div>
						<!--submit-->
						<div class="form-group text-center">
							<input type="submit" class="btn btn-primary" value="Add New Song">
						</div>
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

