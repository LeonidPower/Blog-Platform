<?php
session_start(); // Start the session

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Include database connection
include 'header.php'; // Include header

// Fetch user details from the database
$username = $_SESSION['username'];
$sql = "SELECT username, email FROM users WHERE username = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
}

// Fetch user's posts from the database (including id, title, content, image, likes, and dislikes)
$sql = "SELECT id, title, content, image, created_at, likes, dislikes FROM posts WHERE author = ? ORDER BY created_at DESC";
$posts = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 32px;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }

        .profile-info {
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-info p {
            font-size: 18px;
            margin-bottom: 10px;
            color: #555;
        }

        .profile-posts {
            text-align: center;
        }

        .profile-posts h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-posts ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .profile-posts ul li {
            margin: 20px;
            display: flex;
            justify-content: center;
        }

        .profile-posts .post-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            max-width: 400px; /* Ensure a reasonable width */
            width: 100%;
        }

        .profile-posts .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .profile-posts .post-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .profile-posts .post-card strong {
            font-size: 20px;
            color: #333;
        }

        .profile-posts .post-card span {
            font-size: 14px;
            color: #777;
            margin-top: 5px;
            display: block;
        }

        .profile-posts .post-card p {
            font-size: 16px;
            color: #555;
            margin: 15px 0;
        }

        .post-card .like-dislike {
            font-size: 14px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        .post-card .like-dislike span {
            color: #007bff;
        }

        .back-link {
            text-align: center;
            margin-top: 40px;
            font-size: 16px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h2>Profile of <?php echo $username; ?></h2>

        <!-- User Info Section -->
        <div class="profile-info">
            <p><strong>Email:</strong> <?php echo $email; ?></p>
        </div>

        <!-- User's Posts Section -->
        <div class="profile-posts">
            <h3>Your Posts</h3>
            <?php if (!empty($posts)): ?>
                <ul>
                    <?php foreach ($posts as $post): ?>
                        <li>
                            <div class="post-card">
                                <!-- Only make the title and image clickable -->
                                <a href="post.php?id=<?php echo $post['id']; ?>" style="text-decoration: none;">
                                    <strong><?php echo $post['title']; ?></strong>
                                </a>
                                <span>Posted on: <?php echo $post['created_at']; ?></span>
                                <!-- Display image if available and make it clickable -->
                                <?php if (!empty($post['image'])): ?>
                                    <a href="post.php?id=<?php echo $post['id']; ?>">
                                        <img src="<?php echo filter_var($post['image'], FILTER_VALIDATE_URL) ? $post['image'] : 'uploads/' . $post['image']; ?>" alt="Post Image">
                                    </a>
                                <?php endif; ?>
                                <p><?php echo substr($post['content'], 0, 150); ?>...</p>
                                
                                <!-- Likes and Dislikes -->
                                <div class="like-dislike">
                                    <span>👍 <?php echo $post['likes']; ?></span>
                                    <span>👎 <?php echo $post['dislikes']; ?></span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="text-align: center;">You have not created any posts yet.</p>
            <?php endif; ?>
        </div>

        <!-- Back to homepage link -->
        <div class="back-link">
            <a href="index.php">↑</a>
        </div>
    </div>

</body>
<?php include 'footer.php'; ?>
</html>
