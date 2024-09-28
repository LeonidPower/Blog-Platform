<?php
session_start();
include 'config.php'; 
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle likes/dislikes
if (isset($_GET['action'], $_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
    $action = $_GET['action'];

    $stmt = $conn->prepare("SELECT * FROM likes_dislikes_posts WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['action'] === $action) {
            $stmt = $conn->prepare("DELETE FROM likes_dislikes_posts WHERE user_id = ? AND post_id = ?");
            $stmt->bind_param("ii", $user_id, $post_id);
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE posts SET " . ($action == 'like' ? 'likes' : 'dislikes') . " = " . ($action == 'like' ? 'likes' : 'dislikes') . " - 1 WHERE id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE likes_dislikes_posts SET action = ? WHERE user_id = ? AND post_id = ?");
            $stmt->bind_param("sii", $action, $user_id, $post_id);
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE posts SET likes = likes " . ($action == 'like' ? '+ 1, dislikes = dislikes - 1' : '- 1, dislikes = dislikes + 1') . " WHERE id = ?");
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO likes_dislikes_posts (user_id, post_id, action) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $post_id, $action);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE posts SET " . ($action == 'like' ? 'likes' : 'dislikes') . " = " . ($action == 'like' ? 'likes' : 'dislikes') . " + 1 WHERE id = ?");
    }
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
}

// Fetch top posts
$top_posts = $conn->query("SELECT id, title, content, author, image, created_at, likes, dislikes FROM posts ORDER BY likes DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Fetch other posts
$top_post_ids_str = implode(",", array_column($top_posts, 'id'));
$other_posts = $conn->query("SELECT id, title, content, author, image, created_at, likes, dislikes FROM posts WHERE id NOT IN ($top_post_ids_str) ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

// Fetch user's likes/dislikes
$user_likes = [];
$stmt = $conn->prepare("SELECT post_id, action FROM likes_dislikes_posts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $user_likes[$row['post_id']] = $row['action'];
?>

<!-- Trending Posts Section -->
<div class="container">
    <h2>Top Trending Posts</h2>
    <div class="posts-container">
        <?php foreach ($top_posts as $row) {
            $image_path = filter_var($row["image"], FILTER_VALIDATE_URL) ? $row["image"] : 'uploads/' . $row["image"];
            echo "<div class='post-card'><a href='post.php?id={$row["id"]}'><img src='$image_path' alt='Post Image' style='width: 100%; height: 180px; object-fit: cover; border-radius: 8px;'><h3>{$row["title"]}</h3></a><p>" . substr($row["content"], 0, 100) . "...</p><span>{$row["author"]} • {$row["created_at"]}</span><div style='margin-top: 10px;'>";
            $liked = isset($user_likes[$row["id"]]) && $user_likes[$row["id"]] == 'like';
            $disliked = isset($user_likes[$row["id"]]) && $user_likes[$row["id"]] == 'dislike';
            echo "<a href='?action=like&post_id={$row["id"]}' class='like-btn " . ($liked ? 'selected' : '') . "'><i class='fa fa-thumbs-up'></i> {$row["likes"]}</a>   <a href='?action=dislike&post_id={$row["id"]}' class='dislike-btn " . ($disliked ? 'selected' : '') . "'><i class='fa fa-thumbs-down'></i> {$row["dislikes"]}</a></div></div>";
        } ?>
    </div>
</div>

<!-- Other Posts Section -->
<section class="other-posts" style="margin: 20px 0;">
    <div class="container">
        <h2>More Posts</h2>
        <div class="posts-container">
            <?php foreach ($other_posts as $row) {
                $image_path = filter_var($row["image"], FILTER_VALIDATE_URL) ? $row["image"] : 'uploads/' . $row["image"];
                echo "<div class='post-card'><a href='post.php?id={$row["id"]}'><img src='$image_path' alt='Post Image' style='width: 100%; height: 180px; object-fit: cover; border-radius: 8px;'><h3>{$row["title"]}</h3></a><p>" . substr($row["content"], 0, 100) . "...</p><span>{$row["author"]} • {$row["created_at"]}</span><div style='margin-top: 10px;'>";
                $liked = isset($user_likes[$row["id"]]) && $user_likes[$row["id"]] == 'like';
                $disliked = isset($user_likes[$row["id"]]) && $user_likes[$row["id"]] == 'dislike';
                echo "<a href='?action=like&post_id={$row["id"]}' class='like-btn " . ($liked ? 'selected' : '') . "'><i class='fa fa-thumbs-up'></i> {$row["likes"]}</a>   <a href='?action=dislike&post_id={$row["id"]}' class='dislike-btn " . ($disliked ? 'selected' : '') . "'><i class='fa fa-thumbs-down'></i> {$row["dislikes"]}</a></div></div>";
            } ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; $conn->close(); ?>
