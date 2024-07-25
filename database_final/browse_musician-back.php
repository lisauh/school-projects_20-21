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

		$sql_output = "SELECT musician_id, professional_name, CONCAT(first_name, ' ', middle_initial, ' ', last_name) as full_name, middle_initial, CONCAT(first_name, ' ',last_name) as full_name_nomi, date_of_birth FROM musicians WHERE (professional_name LIKE '%".$search."%' OR CONCAT(first_name, ' ', middle_initial, ' ', last_name) LIKE '%".$search."%' OR CONCAT(first_name, ' ',last_name) LIKE '%".$search."%') ORDER BY professional_name";
	} else {
		$sql_output = "SELECT musician_id, professional_name, CONCAT(first_name, ' ', middle_initial, ' ', last_name) as full_name, middle_initial, CONCAT(first_name, ' ',last_name) as full_name_nomi, date_of_birth FROM musicians ORDER BY professional_name";
	}
		
	// fetch results
	$result_output = mysqli_query($conn,$sql_output);
	if(mysqli_num_rows($result_output)>0){
		$song_id_prev = '';
		$output .= '
			<div class="table-responsive">
			<table class="table table-bordered">
			<tr><th></th><th>Professional Name</th><th>Full Name</th><th>Date of Birth (YYYY-MM-DD)</th></tr>';
		while($row = mysqli_fetch_array($result_output)){
			$musician_id = $row["musician_id"];
			$pro_name = $row["professional_name"];
			if(empty($row["middle_initial"])){
				$full_name = $row["full_name_nomi"];
			} else {
				$full_name = $row["full_name"];
			}
			$dob = $row["date_of_birth"];

			$output .= '
				<tr><td><a class="btn btn-outline-dark btn-sm" href="browse_musician-view.php?m_id='.$musician_id.'" role="button">View</a></td>
				<td>'.$pro_name.'</td>
				<td>'.$full_name.'</td>
				<td>'.$dob.'</td></tr>';
		}
		$output .= '</table></div>';
		echo $output;

	} else {
		$output = "No results found";
		echo $output;
	}
	
?>