<?php
$page_title = 'Change Profile Photo';
include ('includes/header.html');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	require ('mysqli_connect.php');

	$errors = array(); // Initialize error array.

	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			
			$uploadOk = 1;
		} else {
			$errors[] = 'File is not an image.';
			$uploadOk = 0;
		}
	}

	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 5000000) {
		$errors[] = "Your file is too large.";
		$uploadOk = 0;
	}

	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		$errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		$errors[] = "Your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

			$id = $_SESSION['user_id'];
			
			$query = "UPDATE users SET profile_pic = '$target_file' WHERE user_id = '$id'";
			$result = mysqli_query ($dbc, $query);

			$_SESSION['profile_pic'] = $target_file;

			$success = 1;
			
		} else {
			$errors[] = "There was an error uploading your file.";
		}
	}

	if ($errors) {
		echo '<h1>Error!</h1>
		<div id ="errors">The following error(s) occurred:<br />';
		foreach ($errors as $msg) {
			echo " - $msg<br/>";
		}
		echo '</div>
		<div id = "errors">Please try again.</div>'; // Close div "errors"

	} elseif ($success == 1) {
		
		echo '<h1>Success!</h1>
		<div id ="success"><br/>';
		echo "Your new profile picture has been uploaded.<br/>";
		echo '</div><br>
		<a href="profile.php"><button align="right">Back to User Profile</button></a>
		<br><br><br>'; // Close div "success"
		exit();
	}
}
?>

<h1>Change Profile Picture</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
	<table style="font-size: 100%">
		<tr>
			<td><p style="size: 100%">Select image to upload</p></td>
			<td><p><input type="file" name="fileToUpload" id="fileToUpload"></p></td>
		</tr>
	</table>
	<p align="right"><input type="submit" value="Upload Image" name="submit"></p>
</form>

<?php
include ('includes/footer.html');
?>