<?php 
$page_title = 'Registration Form';
include ('includes/header.html');

if (!empty($_SESSION['user_id']) || !empty($_SESSION['admin_id'])) {
	echo '
		<script>
		window.alert("\nALREADY LOGGED IN!");
		setTimeout(function(){location.href="profile.php"},0);
		</script>';
}

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$errors = array(); // Initialize an error array.

	// Check for full name:
	if (empty($_POST['fullname'])) {
		$errors[] = 'You forgot to enter your full name.';
	} else {
		$fn = trim($_POST['fullname']);
	}
	
	// Check for username:
	if (empty($_POST['username'])) {
		$errors[] = 'You forgot to enter your username.';
	} else {
		$un = trim($_POST['username']);
	}
	
	// Check for an email address:
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email address.';
	} else {
		$e = trim($_POST['email']);
	}

	// Check the phone number:
	if (empty($_POST['phone_no'])) {
		
		$errors[] = 'You forgot to enter your phone number.';
	} else {
		$pn = trim($_POST['phone_no']);
	}

	// Check for platform select:
	if ($_POST['platform'] == "None"){
		$errors[] = 'You forgot to select your platform.';
	} else {
		$plt = trim($_POST['platform']);
	}

	// Check for a password and match against the confirmed password:
	if (!empty($_POST['pass1'])) {
		if ($_POST['pass1'] != $_POST['pass2']) {
			$errors[] = 'Your password did not match the confirmed password.';
		} else {
			$p = trim($_POST['pass1']);
		}
	} else {
		$errors[] = 'You forgot to enter your password.';
	}

	if (empty($errors)) { // If everything's OK. ($fn && $un && $e && $pn && $plt && $p)
		
		require ('mysqli_connect.php'); // Connect to the db.

		//  Test for unique username and email address
		$q = "SELECT username FROM users WHERE username='$un'";
		$r = @mysqli_query($dbc, $q);

		$q1 = "SELECT email FROM users WHERE email='$e'";
		$r1 = @mysqli_query($dbc, $q1);

		if(mysqli_num_rows($r) > 0){ // Check for unique username
			$errors[] = "Username has been taken.";
			if (mysqli_num_rows($r1) > 0) { // Check for unique email address
				$errors[] = "Email Address already exists.";
			}
		} elseif (mysqli_num_rows($r1) > 0) { // Check for unique email address
			$errors[] = "Email Address already exists.";
		} else { // Register the user in the database...

			//Insert default profile pic
			$dp = "includes/defaultpic.png";

			// Make the query:
			$q = "INSERT INTO users (fullname, username, email, phoneNo, platform, password, registration_date, profile_pic) VALUES ('$fn', '$un', '$e', '$pn', '$plt', SHA1('$p'), NOW(), '$dp' )";	
			$r = mysqli_query ($dbc, $q); // Run the query.
			if ($r) { // If it ran OK.

				// Print a message:
				echo '<h1>Thank you!</h1>
				<p id="success">You are now registered.</p>
				<br>
				<a href="login.php"><button align="right">Go to Login Page</button></a>
				<br><br><br>';	

			} else { // If it did not run OK.

				// Public message:
				echo '<h1>System Error</h1>
				<p id="errors">You could not be registered due to a system error. We apologize for any inconvenience.</p>';

				// Debugging message:
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
			} // End of if($r) IF.
			
			mysqli_close($dbc); // Close the database connection.
			
			// Include the footer and quit the script:
			include ('includes/footer.html'); 
			exit();	
		}
	} // End of if(empty($errors)) IF.

	if ($errors) { // Report the errors.
	echo '<h1>Error!</h1>
	<div id ="errors">The following error(s) occurred:<br />';
	foreach ($errors as $msg) { // Print each error.
		echo " - $msg<br/>";
	}
	echo '</div> 
	<div id = "errors">Please try again.</div>'; // Close div "errors"
	}

} // End of the main Submit conditional.
?>

<h1>SIGN UP FORM</h1>
<form action="register.php" method="post">
	<p>* Please complete the form below.</p>
	<table style="font-size: 100%">
		<tr>
			<td><p>Full Name</p></td>
			<td><p><input type="text" name="fullname" size="30" maxlength="30" value="<?php if (isset($_POST['fullname'])) echo $_POST['fullname']; ?>"/></p></td>
		</tr>
		<tr>
			<td><p>Username</p></td>
			<td><p><input type="text" name="username" size="30" maxlength="20" value="<?php if (isset($_POST['username'])) echo $_POST['username']; ?>"/></p></td>
		</tr>
		<tr>
			<td><p>Email Address</p></td>
			<td><p><input type="text" name="email" size="30" maxlength="60" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>"/></p></td>
		</tr>
		<tr>
			<td><p>Phone Number</p></td>
			<td><p><input type="text" name="phone_no" size="25" maxlength="40" value= "<?php if (isset($_POST['phone_no'])) echo $_POST['phone_no']; ?>"/></p></td>
		</tr>
		<tr>
			<td><p>Platform</p></td>
			<td><p><select name=platform>
					<option selected value="None">Select your platform...</option>
					<option value="Playstation">Playstation</option>
					<option value="Xbox">Xbox</option>
					<option value="PC">PC</option>
				</select></p></td>
		</tr>
		<tr>
			<td><p>Password</p></td>
			<td><p><input type="password" name="pass1" size="15" maxlength="20" value=""/></p></td>
		</tr>
		<tr>
			<td><p>Confirm Password</p></td>
			<td><p><input type="password" name="pass2" size="15" maxlength="20" value=""/></p></td>
		</tr>
	</table>
	<p align="right"><input type="submit" name="submit" value="Register" /></p>
</form>

<?php include ('includes/footer.html'); ?>