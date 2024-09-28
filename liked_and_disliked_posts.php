<?php
session_start(); // Start the session
include 'config.php'; // Include the config.php file for DB connection
include 'header.php'; // Include the header.php file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the current user ID
$user_id = $_SESSION['user_id'];

// Fetch the liked posts by the current user
$liked_posts_sql = "SELECT p.id, p.title, p.content, p.author, p.image, p.created_at, p.likes, p.dislikes 
                    FROM posts p 
                    JOIN likes_dislikes_posts ld ON p.id = ld.post_id 
                    WHERE ld.user_id = ? AND ld.action = 'like'";

$stmt = $conn->prepare($liked_posts_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$liked_posts_result = $stmt->get_result();

// Fetch the disliked posts by the current user
$disliked_posts_sql = "SELECT p.id, p.title, p.content, p.author, p.image, p.created_at, p.likes, p.dislikes 
                       FROM posts p 
                       JOIN likes_dislikes_posts ld ON p.id = ld.post_id 
                       WHERE ld.user_id = ? AND ld.action = 'dislike'";

$stmt = $conn->prepare($disliked_posts_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$disliked_posts_result = $stmt->get_result();
?>

<!-- Styling for the liked and disliked posts section -->
<style>
    .container {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        text-align: center;
    }
    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
        text-align: left;
        padding-left: 15px;
    }
    .posts-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .post-card {
        width: 280px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }
    .post-card:hover {
        transform: scale(1.05);
    }
    .post-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
    }
    .post-card h3 {
        font-size: 20px;
        margin-top: 10px;
        color: #007bff;
    }
    .post-card p {
        font-size: 14px;
        color: #666;
        margin-top: 10px;
        margin-bottom: 15px;
    }
    .post-card span {
        font-size: 12px;
        color: #888;
    }
    .post-card div {
        margin-top: 10px;
    }
</style>

<!-- Liked Posts Section -->
<section class="liked-posts">
    <div class="container">
        <h2>Your Liked Posts</h2>
        <div class="posts-container">
            <?php
            if ($liked_posts_result->num_rows > 0) {
                while ($row = $liked_posts_result->fetch_assoc()) {
                    $image_path = filter_var($row["image"], FILTER_VALIDATE_URL) ? $row["image"] : 'uploads/' . $row["image"];
                    echo '<div class="post-card">';
                    echo '<a href="post.php?id=' . $row["id"] . '">';
                    echo '<img src="' . $image_path . '" alt="Post Image">';
                    echo '<h3>' . $row["title"] . '</h3></a>';
                    echo '<p>' . substr($row["content"], 0, 100) . '...</p>';
                    echo '<span>' . $row["author"] . ' • ' . $row["created_at"] . '</span>';
                    echo '<div>';
                    echo '<a href="post.php?id=' . $row["id"] . '" class="like-btn"><i class="fa fa-thumbs-up"></i> ' . $row["likes"] . '</a>';
                    echo ' | ';
                    echo '<a href="post.php?id=' . $row["id"] . '" class="dislike-btn"><i class="fa fa-thumbs-down"></i> ' . $row["dislikes"] . '</a>';
                    echo '</div></div>';
                }
            } else {
                echo "<p>You haven't liked any posts yet.</p>";
            }
            ?>
        </div>
    </div>
</section>

<!-- Disliked Posts Section -->
<section class="disliked-posts">
    <div class="container">
        <h2>Your Disliked Posts</h2>
        <div class="posts-container">
            <?php
            if ($disliked_posts_result->num_rows > 0) {
                while ($row = $disliked_posts_result->fetch_assoc()) {
                    $image_path = filter_var($row["image"], FILTER_VALIDATE_URL) ? $row["image"] : 'uploads/' . $row["image"];
                    echo '<div class="post-card">';
                    echo '<a href="post.php?id=' . $row["id"] . '">';
                    echo '<img src="' . $image_path . '" alt="Post Image">';
                    echo '<h3>' . $row["title"] . '</h3></a>';
                    echo '<p>' . substr($row["content"], 0, 100) . '...</p>';
                    echo '<span>' . $row["author"] . ' • ' . $row["created_at"] . '</span>';
                    echo '<div>';
                    echo '<a href="post.php?id=' . $row["id"] . '" class="like-btn"><i class="fa fa-thumbs-up"></i> ' . $row["likes"] . '</a>';
                    echo ' | ';
                    echo '<a href="post.php?id=' . $row["id"] . '" class="dislike-btn"><i class="fa fa-thumbs-down"></i> ' . $row["dislikes"] . '</a>';
                    echo '</div></div>';
                }
            } else {
                echo "<p>You haven't disliked any posts yet.</p>";
            }
            ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?> <!-- Include the footer.php file -->

<?php
// Close the database connection
$conn->close();
?>
