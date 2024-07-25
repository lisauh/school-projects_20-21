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

	// get musician id from url
	$musician_id = $_GET["m_id"];

	// if no album, redirect back
	if(empty($musician_id)){
		header("location: ae_musicians.php");
		exit;
	}

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// check musician_id exists
	$sql_check = "SELECT * from musicians WHERE musician_id=$musician_id";
	$check = mysqli_query($conn,$sql_check);
	if(mysqli_num_rows($check)==0){
		header("location: ae_musicians.php");
		exit;
	}

	// get musician info
	$sql_minfo = "SELECT professional_name, first_name, middle_initial, last_name, date_of_birth FROM musicians WHERE musician_id=$musician_id";
	$minfo = mysqli_query($conn,$sql_minfo);
	if($row=mysqli_fetch_assoc($minfo)){
		$pro_name = $row["professional_name"];
		$fname = $row["first_name"];
		$mi = $row["middle_initial"];
		$lname = $row["last_name"];
		$dob = $row["date_of_birth"];
	}

	// init table vars
	$album_tbl = '';
	$sw_tbl = '';
	$sp_tbl = '';

	// get album credits
	$sql_a = "SELECT a.album_id as album_id, a.album_title as album_title, a.release_year as release_year, GROUP_CONCAT(g.genre_name SEPARATOR ', ') as album_genre FROM albums a INNER JOIN album_genres ag ON a.album_id=ag.album_id INNER JOIN genres g on ag.genre_id=g.genre_id WHERE a.musician_id=$musician_id GROUP BY a.musician_id";
	$result_a = mysqli_query($conn,$sql_a);
	if(mysqli_num_rows($result_a)>0){
		$album_tbl = '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th></th>
			<th>Album Title</th>
			<th>Release Year</th>
			<th>Genre</th></tr>';
		while($arow = mysqli_fetch_array($result_a)){
			$album_id = $arow["album_id"];
			$album_title = $arow["album_title"];
			$release_year = $arow["release_year"];
			$album_genre = $arow["album_genre"];

			$album_tbl .='
				<tr><td><a class="btn btn-secondary btn-sm" href="ae_album-select.php?a_id='.$album_id.'" role="button">Edit</a></td>
				<td>'.$album_title.'</td>
				<td>'.$release_year.'</td>
				<td>'.$album_genre.'</td></tr>';
		}
		$album_tbl .= '</table></div>';
	} else {
		$album_tbl = 'No albums found';
	}

	// get song writing credits
	$sql_sw = "SELECT sw.song_id as sw_song_id, s.song_title as sw_song_title FROM song_writers sw INNER JOIN songs s ON sw.song_id=s.song_id WHERE sw.musician_id=$musician_id";
	$result_sw = mysqli_query($conn,$sql_sw);
	if(mysqli_num_rows($result_sw)>0){
		$sw_tbl .= '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th></th>
			<th>Song Title</th></tr>';
		while($swrow = mysqli_fetch_array($result_sw)){
			$sw_song_id = $swrow["sw_song_id"];
			$sw_song_title = $swrow["sw_song_title"];

			$sw_tbl .= '
				<tr><td><a class="btn btn-secondary btn-sm" href="ae_album-select_song.php?s_id='.$sw_song_id.'" role="button">Edit</a></td>
				<td>'.$sw_song_title.'</td></tr>';
		}
		$sw_tbl .= '</table></div>';
	} else {
		$sw_tbl = 'No songs found';
	}

	// get song producing credits
	$sql_sp = "SELECT sp.song_id as sp_song_id, s.song_title as sp_song_title FROM song_producers sp INNER JOIN songs s ON sp.song_id=s.song_id WHERE sp.musician_id=$musician_id";
	$result_sp = mysqli_query($conn,$sql_sp);
	if(mysqli_num_rows($result_sp)>0){
		$sp_tbl .= '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th></th>
			<th>Song Title</th></tr>';
		while($sprow = mysqli_fetch_array($result_sp)){
			$sp_song_id = $sprow["sp_song_id"];
			$sp_song_title = $sprow["sp_song_title"];

			$sp_tbl .= '
				<tr><td><a class="btn btn-secondary btn-sm" href="ae_album-select_song.php?s_id='.$sp_song_id.'" role="button">Edit</a></td>
				<td>'.$sp_song_title.'</td></tr>';
		}
		$sp_tbl .= '</table></div>';
	} else {
		$sp_tbl = 'No songs found';
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

		<title>mmmusicdb: edit musician</title>

		<script>
			function confirmDelete(){
				var agree=confirm("Are you sure you want to delete this musician?");
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
					<h1>Edit Musician</h1>
				</div>
			</div>

			<div class="row">
				<div class="col-2">
					<!--placeholder-->
				</div>

				<div class="col-8">
					<form action="ae_musician-select-update.php" method="POST">
						<!-- musician id -->
						<input type="hidden" name="musician_id" value="<?php echo $musician_id;?>">

						<div class="form-group">
							<label>Professional Name</label>
							<input type="text" name="pro_name" class="form-control" maxlength="45" value="<?php echo $pro_name;?>" required>
						</div>
						<div class="form-group">
							<label>First Name</label>
							<input type="text" name="fname" class="form-control" maxlength="45" value="<?php echo $fname;?>">
						</div>
						<div class="form-group">
							<label>Middle Initial</label>
							<input type="text" name="mi" class="form-control" maxlength="5" value="<?php echo $mi;?>">
						</div>
						<div class="form-group">
							<label>Last Name</label>
							<input type="text" name="lname" class="form-control" maxlength="45" value="<?php echo $lname;?>">
						</div>
						<div class="form-group">
							<label>Date of Birth (YYYY-MM-DD)</label>
							<input type="text" name="dob" class="form-control" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="<?php echo $dob;?>">
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
				<div class="col-12 mt-6">
					<h3>Albums Released (as recording artist)</h3>
					<div class="text-center">
						<?php echo $album_tbl;?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12 mt-6">
					<h3>Songs written</h3>
					<div class="text-center">
						<?php echo $sw_tbl;?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12 mt-6">
					<h3>Songs produced</h3>
					<div class="text-center">
						<?php echo $sp_tbl;?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-center">
					<hr>
					<form action="ae_musician-select-delete.php" method="POST">
						<input type="hidden" name="musician_id" value="<?php echo $musician_id;?>">
						<div class="form-group">
							<input type="submit" class="btn btn-danger btn-lg" onClick="return confirmDelete()" value="Delete Musician">
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




