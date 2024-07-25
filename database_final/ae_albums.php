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

	// init output var
	$album_tbl = '';

	$sql = "SELECT a.album_id as album_id,a.album_title as album_title,a.release_year as release_year,GROUP_CONCAT(g.genre_name SEPARATOR ', ') as genre,m.professional_name as rec_artist FROM albums a INNER JOIN album_genres ag ON a.album_id=ag.album_id INNER JOIN genres g on ag.genre_id=g.genre_id INNER JOIN musicians m ON m.musician_id=a.musician_id GROUP BY album_id";

	$result = mysqli_query($conn,$sql);

	if(mysqli_num_rows($result)>0){
		$album_tbl = '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th></th>
			<th>Album Title</th>
			<th>Year</th>
			<th>Recording Artist</th>
			<th>Genre</th></tr>';
		while($row = mysqli_fetch_array($result)){
			$album_id = $row["album_id"];
			$album_title = $row["album_title"];
			$album_year = $row["release_year"];
			$rec_artist = $row["rec_artist"];
			$genre = $row["genre"];

			$album_tbl .='
				<tr><td><a class="btn btn-secondary btn-sm" href="ae_album-select.php?a_id='.$album_id.'" role="button">Edit</a></td>
				<td>'.$album_title.'</td>
				<td>'.$album_year.'</td>
				<td>'.$rec_artist.'</td>
				<td>'.$genre.'</td></tr>';
		}
		$album_tbl .= '</table></div>';
	} else {
		$album_tbl .= 'No albums found';
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

		<title>mmmusicdb: add/edit albums</title>
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
					<h1>Add/Edit Albums</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-center">
					<p><a href="ae_album-new.php" class="btn btn-primary btn-lg" role="button">Add New Album</a></p>
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-center">
					<?php echo $album_tbl;?>
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