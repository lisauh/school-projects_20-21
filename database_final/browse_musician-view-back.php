<?php
	session_start();

	// check if logged in
	if(!isset($_SESSION["loggedin"])){
		header("location: userlogin.php");
		exit;
	}

	$musician_id = $_GET["m_id"];

	require_once "db_login.php";
	// mysql db connection
	$conn = mysqli_connect($db_hostname,$db_username,$db_password,$db_database);
	if (!$conn) die("Unable to connect to MySQL: " . mysqli_connect_error($conn));

	// init print var
	$output = '';

	// check if in favs
	$sql_check = "SELECT account_id FROM acc_favorite_musicians WHERE (account_id=".intval($_SESSION['id'])." AND musician_id=".intval($musician_id).")";
	$check = mysqli_query($conn,$sql_check);
	if(mysqli_num_rows($check)>0){
		$infavs = true;
		}

	if($infavs == false){
		// add
		$sql_insert = "INSERT INTO acc_favorite_musicians (account_id,musician_id) VALUES (".intval($_SESSION['id']).",".intval($musician_id).")";
		if(mysqli_query($conn,$sql_insert)){
			$output = '<button type="button" class="btn btn-danger btn-lg" id="fav-refresh">
				♥</button>';
			}
	} else {
		// remove
		$sql_delete = "DELETE FROM acc_favorite_musicians WHERE (account_id=".intval($_SESSION['id'])." AND musician_id=".intval($musician_id).")";
		if(mysqli_query($conn,$sql_delete)){
			$output = '<button type="button" class="btn btn-danger btn-lg" id="fav-refresh">
				♡</button>';
			}
	}
	echo $output;

?>