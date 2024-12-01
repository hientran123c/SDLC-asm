<?php
include "connect.php";

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Check if username is empty
if (empty(trim($_POST["username"]))) {
  $username_err = "Please enter username.";
} else {
  $username = trim($_POST["username"]);
}

// Check if password is empty
if (empty(trim($_POST["password"]))) {
  $password_err = "Please enter your password.";
} else {
  $password = trim($_POST["password"]);
}

if (empty($username_err) && empty($password_err)) {
  // Prepare a select statement
  $sql = "SELECT id, username, password FROM users WHERE username = ?";
  // Execute the prepared statement
  $stmt = mysqli_prepare($conn, $sql);
  
  if ($stmt) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_username);

    // Set parameters
    $param_username = $username;
    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
      // Store result
      mysqli_stmt_store_result($stmt);

      // Check if username exists, then verify password
      if (mysqli_stmt_num_rows($stmt) == 1) {
        //Bind result variables
        mysqli_stmt_bind_result($stmt, $id, $username, $password_db);

        // Fetch the results
        if (mysqli_stmt_fetch($stmt)) {
          // Verify the password with the hashed password from the database
          if ($password == $password_db) {
            // Redirect user to welcome page
            echo "<script>
            alert('Sign up successfully!');
            window.location.href = ' main.php';
        </script>";
            // header("location: main.php");
            exit();
          }  else {
            // Display an error message if password is not valid
            $password_err = "The password you entered was not valid.";
          }
        }
      } else {
        // Display an error message if username doesn't exist
        $username_err = "No account found with that username.";
      }
    } else {
      echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);
  }
}
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapped">
<h2>Login</h2>
<form action="" method="post">
    <div class="container">
      <label for="username"><b>Username</b></label>
      <input type="text" placeholder="Enter Username" id="username" name="username" required>
      <span class="text-danger"><?php echo $username_err; ?></span>
     
      <label for="password"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" id="password" name="password" required>
      <span class="text-danger"><?php echo $password_err; ?></span>
    
      <button type="submit">Login</button>
  
       <p>Don't have account. <a href="./register1.php">Register now</a> </p> 
    </div>
    </div>
  </form>
</div>
</body>
</html>