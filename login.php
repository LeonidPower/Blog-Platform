<link rel="icon" href="favicon.ico" type="image/x-icon">
<?php
session_start();
include 'config.php'; // Contains DB connection setup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Prepare the SQL query to get the user
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Store user info in session variables
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;

                // Redirect to dashboard or home page
                header("Location: index.php");
            } else {
                $login_error = "Incorrect password!";
            }
        } else {
            $login_error = "No account found with that username!";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS file -->
    <style>
        /* Styles for the login page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .login-container input {
            width: 90%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f8f9fa;
        }

        .login-container button {
            width: 50%;
            padding: 12px 15px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container p.error {
            color: #d9534f;
            font-size: 14px;
        }

        .login-container p.success {
            color: #5cb85c;
            font-size: 14px;
        }

        /* Responsive design */
        @media (max-width: 500px) {
            .login-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login to Your Account</h2>

        <!-- Display error messages -->
        <?php if (isset($login_error)): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Sign up</a></p>
    </div>

</body>
</html>
