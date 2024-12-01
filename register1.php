<?php
include "connect.php";

$username = $password = $confirm_password = $email = $dob = $role = $address = "";
$username_err = $password_err = $confirm_password_err = $email_err = $dob_err = $role_err = $address_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Validate username
if(empty(trim($_POST["username"]))){
    $username_err = "Please enter a username.";
} elseif(!preg_match('/^[a-zA-Z0-9_ ]+$/', trim($_POST["username"]))){
    $username_err = "Username can only contain letters, numbers, and underscores.";
} else{
    // Check if the username is already taken
    $sql = "SELECT username FROM users WHERE username = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($_POST["username"]);
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1){
                $username_err = "This username is already taken.";
            } else{
                $username = trim($_POST["username"]);
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Validate email
if(empty($_POST["email"])){
    $email_err = "Please enter your email.";
} elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    $email_err = "Please enter a valid email address.";
} else{
    $email = $_POST["email"];
    
    // Check if the email is already taken
    $sql = "SELECT email FROM users WHERE email = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;  // No trim here, as we are checking the raw value
    
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1){
                // Email already exists
                $email_err = "This email is already taken.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
}



// Validate date of birth
if(empty(trim($_POST["dob"]))){
    $dob_err = "Please enter your date of birth.";
} else{
    $dob = trim($_POST["dob"]);
}

// Validate role
if(empty(trim($_POST["role"]))){
    $role_err = "Please select a role.";
} else{
    $role = trim($_POST["role"]);
}

// Validate address
if(empty(trim($_POST["address"]))){
    $address_err = "Please enter your address.";
} else{
    $address = trim($_POST["address"]);
}

// Validate password
if(empty(trim($_POST["password"]))){
    $password_err = "Please enter a password.";
} elseif(strlen(trim($_POST["password"])) < 6){
    $password_err = "Password must have at least 6 characters.";
} else{
    $password = trim($_POST["password"]);
}

// Validate confirm password
if(empty(trim($_POST["confirm_password"]))){
    $confirm_password_err = "Please confirm your password.";
} else{
    $confirm_password = trim($_POST["confirm_password"]);
    if($password != $confirm_password){
        $confirm_password_err = "Password did not match.";
    }
}

// Check input errors before inserting into the database
if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($dob_err) && empty($role_err) && empty($address_err)){
    // Prepare an insert statement
    $sql = "INSERT INTO users (username, email, dob, role, address, password) VALUES (?, ?, ?, ?, ?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_email, $param_dob, $param_role, $param_address, $param_password);

        $param_username = $username;
        $param_email = $email;
        $param_dob = $dob;
        $param_role = $role;
        $param_address = $address;
        $param_password = $password; 

        if(mysqli_stmt_execute($stmt)){
            // Redirect to login page
            echo "<script>
            alert('Sign up successfully!');
            window.location.href = 'login.php';
        </script>";
            // header("location: login.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }
}
}
mysqli_close($conn); 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<div class="wrapper">

<h2>Sign up</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>"> 
        <span class="invalid-feedback"><?php echo $username_err; ?></span>
    </div>  

    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" value="<?php echo $email; ?>"> 
        <span class="invalid-feedback"><?php echo $email_err; ?></span>
    </div>

    <div class="form-group">
        <label>Date of Birth:</label>
        <input type="date" name="dob" class="form-control" value="<?php echo $dob; ?>"> 
        <span class="invalid-feedback"><?php echo $dob_err; ?></span>
    </div>

    <div class="form-group">
        <label>Role:</label>
        <select name="role" class="form-control">
            <option value="student" <?php echo ($role == 'student' ? 'selected' : ''); ?>>Student</option>
            <option value="teacher" <?php echo ($role == 'teacher' ? 'selected' : ''); ?>>Teacher</option>
            <option value="admin" <?php echo ($role == 'admin' ? 'selected' : ''); ?>>Admin</option>
        </select>
        <span class="invalid-feedback"><?php echo $role_err; ?></span>
    </div>

    <div class="form-group">
        <label>Address:</label>
        <textarea name="address" class="form-control"><?php echo $address; ?></textarea>
        <span class="invalid-feedback"><?php echo $address_err; ?></span>
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>"> 
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
    </div>

    <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>"> 
        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
    </div>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>

</form>

</div>

</body>
</html>
