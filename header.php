<link rel="icon" href="favicon.ico" type="image/x-icon">
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already started
}

if (!isset($_SESSION['username'])) {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A blog platform to create and share posts.">
    <title>Blog Platform</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome 5 -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        Header {
    background-color: #222;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}
        /* Header styling */
        nav {
            background-color: #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
        }
        .logo h1 a {
            color: #fff;
            text-decoration: none;
            font-size: 24px;
            margin-right: 20px;
        }
        .nav-links {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .nav-links li {
            margin-right: 20px;
        }
        .nav-links a {
            text-decoration: none;
            color: #fff;
            padding: 10px;
            transition: background-color 0.3s;
        }
        .nav-links a:hover {
            background-color: #007bff;
            border-radius: 4px;
        }
        /* Dropdown styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            top: 100%;
            left: 0;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        /* Create Post Button Styling */
        .create-post-btn {
            background-color: #009688;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-left: 30px;
        }
        .create-post-btn:hover {
            background-color: #218838;
        }
        .right-section {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header>
    <nav>
        <div class="logo">
            <h1><a href="index.php">Blog Platform</a></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <div class="right-section">
            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="dropbtn"><?php echo $_SESSION['username']; // Get logged-in username?> <i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="liked_and_disliked_posts.php">Liked/Disliked Posts</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            <a href="create_post.php" class="create-post-btn">Create Post</a>
        </div>
    </nav>
</header>

</body>
</html>
