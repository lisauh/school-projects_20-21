<?php

	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	$musician_id = $_GET["m_id"];

	// if no song, redirect back
	if(empty($musician_id)){
		header("location: browse_musician.php");
		exit;
	}

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// check song_id exists
	$sql_check = "SELECT * from musicians WHERE musician_id=$musician_id";
	$check = mysqli_query($conn,$sql_check);
	if(mysqli_num_rows($check)==0){
		header("location: browse_musician.php");
		exit;
	}

	$sql_musicianinfo = "SELECT professional_name, CONCAT(first_name, ' ', middle_initial, ' ', last_name) as full_name, middle_initial, CONCAT(first_name,' ',last_name) as full_name_nomi, date_of_birth FROM musicians WHERE musician_id=$musician_id";

	$musicianinfo = mysqli_query($conn,$sql_musicianinfo);

	if($row=mysqli_fetch_assoc($musicianinfo)){
		$pro_name = $row["professional_name"];
		if(empty($row["middle_initial"])){
			$full_name = $row["full_name_nomi"];
			if(empty($full_name)){
				$full_name = 'unknown';
			}
		} else {
			$full_name = $row["full_name"];
		}
		if(empty($row["date_of_birth"])){
			$dob = 'unknown';
		} else {
			$dob = $row["date_of_birth"];
		}
	}

	// summary info
	$alb_g = [];
	$sw_g = [];
	$sp_g = [];
	$alb_output = '';
	$sw_output = '';
	$sp_output = '';

	$sql_alb = "SELECT g.genre_name as genre_name, COUNT(ag.album_id) FROM musicians m INNER JOIN albums a ON a.musician_id=m.musician_id INNER JOIN album_genres ag ON ag.album_id=a.album_id INNER JOIN genres g ON g.genre_id=ag.genre_id WHERE m.musician_id=$musician_id GROUP BY ag.genre_id ORDER BY COUNT(ag.album_id) DESC LIMIT 3";
	$alb = mysqli_query($conn,$sql_alb);
	if(mysqli_num_rows($alb)>0){
		while($albrow = mysqli_fetch_assoc($alb)){
			array_push($alb_g, $albrow["genre_name"]);
		}
		$alb_output = implode(', ',$alb_g);
	} else {
		$alb_output = "not enough information in database";
	}

	$sql_sw = "SELECT g.genre_name as genre_name, COUNT(sw.song_id) FROM musicians m INNER JOIN song_writers sw ON sw.musician_id=m.musician_id INNER JOIN song_genres sg ON sg.song_id=sw.song_id INNER JOIN genres g ON g.genre_id=sg.genre_id WHERE m.musician_id=$musician_id GROUP BY g.genre_id ORDER BY COUNT(sw.song_id) DESC LIMIT 3";
	$sw = mysqli_query($conn,$sql_sw);
	if(mysqli_num_rows($sw)>0){
		while($swrow = mysqli_fetch_assoc($sw)){
			array_push($sw_g, $swrow["genre_name"]);
		}
		$sw_output = implode(', ',$sw_g);
	} else {
		$sw_output = "not enough information in database";
	}

	$sql_sp = "SELECT g.genre_name as genre_name, COUNT(sp.song_id) FROM musicians m INNER JOIN song_producers sp ON sp.musician_id=m.musician_id INNER JOIN song_genres sg ON sg.song_id=sp.song_id INNER JOIN genres g ON g.genre_id=sg.genre_id WHERE m.musician_id=$musician_id GROUP BY g.genre_id ORDER BY COUNT(sp.song_id) DESC LIMIT 3";
	$sp = mysqli_query($conn,$sql_sp);
	if(mysqli_num_rows($sp)>0){
		while($sprow = mysqli_fetch_assoc($sp)){
			array_push($sp_g, $sprow["genre_name"]);
		}
		$sp_output = implode(', ',$sp_g);
	} else {
		$sp_output = "not enough information in database";
	}


	// init table vars
	$album_tbl = '';
	$sw_tbl = '';
	$sp_tbl = '';

	// get album credits
	$sql_a = "SELECT album_id, album_title, release_year FROM albums WHERE musician_id=$musician_id";
	$result_a = mysqli_query($conn,$sql_a);
	if(mysqli_num_rows($result_a)>0){
		$album_tbl = '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th>Album Title</th>
			<th>Release Year</th></tr>';
		while($arow = mysqli_fetch_array($result_a)){
			$album_id = $arow["album_id"];
			$album_title = $arow["album_title"];
			$release_year = $arow["release_year"];

			$album_tbl .='
				<tr><td><a href="browse_album-view.php?a_id='.$album_id.'">'.$album_title.'</a></td>
				<td>'.$release_year.'</td></tr>';
		}
		$album_tbl .= '</table></div>';
	} else {
		$album_tbl = 'No albums found';
	}

	// get song writing credits
	$sql_swt = "SELECT sw.song_id as sw_song_id, s.song_title as sw_song_title, a.album_id as sw_album_id, a.album_title as sw_album_title FROM song_writers sw INNER JOIN songs s ON sw.song_id=s.song_id INNER JOIN albums a ON s.album_id=a.album_id WHERE sw.musician_id=$musician_id";
	$result_sw = mysqli_query($conn,$sql_swt);
	if(mysqli_num_rows($result_sw)>0){
		$sw_tbl .= '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th>Song Title</th>
			<th>Album</td></tr>';
		while($swrow = mysqli_fetch_array($result_sw)){
			$sw_song_id = $swrow["sw_song_id"];
			$sw_song_title = $swrow["sw_song_title"];
			$sw_album_id = $swrow["sw_album_id"];
			$sw_album_title = $swrow["sw_album_title"];

			$sw_tbl .= '
				<tr><td><a href="browse_song-view.php?s_id='.$sw_song_id.'">'.$sw_song_title.'</a></td>
				<td><a href="browse_album-view.php?a_id='.$sw_album_id.'">'.$sw_album_title.'</a></td></tr>';
		}
		$sw_tbl .= '</table></div>';
	} else {
		$sw_tbl = 'No songs found';
	}

	// get song producing credits
	$sql_spt = "SELECT sp.song_id as sp_song_id, s.song_title as sp_song_title, a.album_id as sp_album_id, a.album_title as sp_album_title FROM song_producers sp INNER JOIN songs s ON sp.song_id=s.song_id INNER JOIN albums a ON s.album_id=a.album_id WHERE sp.musician_id=$musician_id";
	$result_sp = mysqli_query($conn,$sql_spt);
	if(mysqli_num_rows($result_sp)>0){
		$sp_tbl .= '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th>Song Title</th>
			<th>Album</td></tr>';
		while($sprow = mysqli_fetch_array($result_sp)){
			$sp_song_id = $sprow["sp_song_id"];
			$sp_song_title = $sprow["sp_song_title"];
			$sp_album_id = $sprow["sp_album_id"];
			$sp_album_title = $sprow["sp_album_title"];

			$sp_tbl .= '
				<tr><td><a href="browse_song-view.php?s_id='.$sp_song_id.'">'.$sp_song_title.'</a></td>
				<td><a href="browse_album-view.php?a_id='.$sp_album_id.'">'.$sp_album_title.'</a></td></tr>';
		}
		$sp_tbl .= '</table></div>';
	} else {
		$sp_tbl = 'No songs found';
	}


	// musician in favs
	// check if in favs
	$sql_check = "SELECT * FROM acc_favorite_musicians WHERE (account_id=".intval($_SESSION['id'])." AND musician_id=".intval($musician_id).")";
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
		
		<title>mmmusicdb: browse musicians - view</title>

		<script>
			$(document).ready(function(){
				$("button").click(function(){
					$.ajax({
						url: "browse_musician-view-back.php?m_id=<?php echo $musician_id;?>",
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
								<a class="dropdown-item" href="browse_album.php">Browse Albums</a>
								<a class="dropdown-item active" href="browse_musician.php">Browse Musicians</a>
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
					<h1>View Musician</h1>
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

					<h3><?php echo $pro_name;?></h3>
					<p>
						<b>Full name: </b>
						<?php echo $full_name;?>
						<br>
						<b>Date of Birth (YYYY-MM-DD): </b>
						<?php echo $dob;?>
						<br>
						<b>Top genres for albums released: </b>
						<?php echo $alb_output;?>
						<br>
						<b>Top genres for songs written: </b>
						<?php echo $sw_output;?>
						<br>
						<b>Top genres for songs produced: </b>
						<?php echo $sp_output;?>
					</p>

					<h3>Albums Released (as recording artist)</h3>
					<?php echo $album_tbl;?>
					<br>

					<h3>Songs Written</h3>
					<?php echo $sw_tbl;?>
					<br>

					<h3>Songs Produced</h3>
					<?php echo $sp_tbl;?>
					<br>

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