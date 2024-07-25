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

	// init print var
	$output = '';

	// this is so big ;_;
	if(isset($_POST["query"])){
		$search = mysqli_real_escape_string($conn, $_POST["query"]);

		$sql_output = "SELECT a.album_id as album_id, a.album_title as album_title, a.musician_id as rec_artist_id, m.professional_name as rec_artist, a.release_year as release_year, GROUP_CONCAT(g.genre_name SEPARATOR ', ') as genre FROM albums a INNER JOIN musicians m ON a.musician_id=m.musician_id INNER JOIN album_genres ag ON a.album_id=ag.album_id INNER JOIN genres g ON g.genre_id=ag.genre_id WHERE a.album_title LIKE '%".$search."%' GROUP BY album_id ORDER BY album_title";
	} else {
		$sql_output = "SELECT a.album_id as album_id, a.album_title as album_title, a.musician_id as rec_artist_id, m.professional_name as rec_artist, a.release_year as release_year, GROUP_CONCAT(g.genre_name SEPARATOR ', ') as genre FROM albums a INNER JOIN musicians m ON a.musician_id=m.musician_id INNER JOIN album_genres ag ON a.album_id=ag.album_id INNER JOIN genres g ON g.genre_id=ag.genre_id GROUP BY album_id ORDER BY album_title";
	}
		
	// fetch results
	$result_output = mysqli_query($conn,$sql_output);
	if(mysqli_num_rows($result_output)>0){
		$song_id_prev = '';
		$output .= '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th></th><th>Album Title</th><th>Recording Artist</th><th>Release Year</th><th>Genre</th></tr>';
		while($row = mysqli_fetch_array($result_output)){
			$album_id = $row["album_id"];
			$album_title = $row["album_title"];
			$rec_artist_id = $row["rec_artist_id"];
			$rec_artist = $row["rec_artist"];
			$release_year = $row["release_year"];
			$song_genre = $row["genre"];

			$output .= '
				<tr><td><a class="btn btn-outline-dark btn-sm" href="browse_album-view.php?a_id='.$album_id.'" role="button">View</a></td>
				<td>'.$album_title.'</td>
				<td><a href="browse_musician-view.php?m_id='.$rec_artist_id.'" class="text-secondary">'.$rec_artist.'</a></td>
				<td>'.$release_year.'</td>
				<td>'.$song_genre.'</td></tr>';
		}
		$output .= '</table></div>';
		echo $output;

	} else {
		$output = "No results found";
		echo $output;
	}
	
?>