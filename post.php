<?php
session_start();
ob_start(); // Start output buffering to prevent "headers already sent" issues

include 'config.php';
include 'header.php'; // Ensure this file has no output before PHP logic

// Check if logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get post ID
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
} else {
    header("Location: index.php");
    exit();
}

// Fetch post
$stmt = $conn->prepare("SELECT title, content, image, created_at, likes, dislikes, author FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc() ?: (print "<p>Post not found.</p>" && exit());

$user_id = $_SESSION['user_id'];

// Handle delete post action
if (isset($_POST['delete_post']) && $post['author'] == $_SESSION['username']) {
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    header("Location: index.php"); // Redirect to homepage after deletion
    exit();
}

// Handle likes/dislikes and actions for comments and posts
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if (isset($_GET['comment_id'])) {
        $comment_id = intval($_GET['comment_id']);

        if ($action == 'pin_comment' && $post['author'] == $_SESSION['username']) {
            // Pin or unpin the comment
            $stmt = $conn->prepare("SELECT is_pinned FROM comments WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            $stmt->bind_result($is_pinned);
            $stmt->fetch();
            $stmt->close();

            if ($is_pinned) {
                // Unpin the comment
                $stmt = $conn->prepare("UPDATE comments SET is_pinned = 0 WHERE id = ?");
            } else {
                // Pin the comment
                $stmt = $conn->prepare("UPDATE comments SET is_pinned = 1 WHERE id = ?");
            }
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
        } elseif ($action == 'remove_comment' && $post['author'] == $_SESSION['username']) {
            // Remove the comment
            $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            header("Location: post.php?id=$post_id");
            exit();
        } elseif ($action == 'like_comment' || $action == 'dislike_comment') {
            // Handle like/dislike for comments
            $stmt = $conn->prepare("SELECT * FROM likes_dislikes_comments WHERE user_id = ? AND comment_id = ?");
            $stmt->bind_param("ii", $user_id, $comment_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['action'] === $action) {
                    $stmt = $conn->prepare("DELETE FROM likes_dislikes_comments WHERE user_id = ? AND comment_id = ?");
                    $stmt->bind_param("ii", $user_id, $comment_id);
                    $stmt->execute();
                    $stmt = $conn->prepare("UPDATE comments SET " . ($action == 'like_comment' ? 'likes' : 'dislikes') . " = " . ($action == 'like_comment' ? 'likes' : 'dislikes') . " - 1 WHERE id = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE likes_dislikes_comments SET action = ? WHERE user_id = ? AND comment_id = ?");
                    $stmt->bind_param("sii", $action, $user_id, $comment_id);
                    $stmt->execute();
                    $stmt = $conn->prepare("UPDATE comments SET likes = likes " . ($action == 'like_comment' ? '+ 1, dislikes = dislikes - 1' : '- 1, dislikes = dislikes + 1') . " WHERE id = ?");
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO likes_dislikes_comments (user_id, comment_id, action) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $user_id, $comment_id, $action);
                $stmt->execute();
                $stmt = $conn->prepare("UPDATE comments SET " . ($action == 'like_comment' ? 'likes' : 'dislikes') . " = " . ($action == 'like_comment' ? 'likes' : 'dislikes') . " + 1 WHERE id = ?");
            }
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            header("Location: post.php?id=$post_id");
            exit();
        }
    } else {
        // Handle like/dislike for posts
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
        header("Location: post.php?id=$post_id");
        exit();
    }
}

// Add comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'], $_SESSION['user_id'])) {
    $comment_content = htmlspecialchars($_POST['comment']);
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $_SESSION['user_id'], $comment_content);
    $stmt->execute();
}

// Fetch comments
$stmt = $conn->prepare("SELECT c.id, c.content, c.created_at, c.likes, c.dislikes, c.is_pinned, u.username, ldc.action AS user_action 
                        FROM comments c 
                        JOIN users u ON c.user_id = u.id 
                        LEFT JOIN likes_dislikes_comments ldc ON ldc.comment_id = c.id AND ldc.user_id = ?
                        WHERE c.post_id = ? 
                        ORDER BY c.is_pinned DESC, c.likes DESC, c.created_at DESC");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Post Container Styling */
        .post-container { max-width: 800px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); }
        h1 { font-size: 32px; margin-bottom: 20px; color: #333; }
        .post-content img { max-width: 100%; margin-bottom: 20px; }
        
        /* Like/Dislike Button Styles */
        .like-dislike-container { display: flex; gap: 20px; margin-top: 20px; }
        .like-btn, .dislike-btn { 
            padding: 10px 20px; 
            border: none; 
            text-decoration: none; 
            border-radius: 5px; 
            color: #fff; 
            cursor: pointer; 
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        .like-btn { background-color: #28a745; }
        .like-btn.selected, .like-btn:hover { background-color: #218838; }
        .dislike-btn { background-color: #dc3545; }
        .dislike-btn.selected, .dislike-btn:hover { background-color: #c82333; }

        /* Comments Section Styling */
        .comment { 
            background: #f4f4f9; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        }
        .comment .like-btn, .comment .dislike-btn { 
            font-size: 14px; 
            padding: 8px; 
        }

        /* Styling for Pinned Comments */
        .pinned-badge { 
            background-color: #FFD700; 
            color: #333; 
            padding: 5px 10px; 
            font-size: 12px; 
            border-radius: 3px; 
            display: inline-block;
            margin-left: 10px;
        }
        .pinned-comment {
            border: 2px solid #FFD700;
            background-color: #fffbea;
        }

        /* Form Styling */
        .comment-form {
            margin-bottom: 30px;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .comment-form button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .comment-form button:hover {
            background-color: #0056b3;
        }

        /* Add some margin around elements */
        .comment p {
            margin-bottom: 10px;
        }

        /* Delete button styling */
        .delete-post-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .delete-post-btn:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        function confirmDelete() {
            if (confirm("Are you sure you want to delete this post?")) {
                document.getElementById('delete-post-form').submit();
            }
        }
    </script>
</head>
<body>
    <div class="post-container">
        <h1><?= htmlspecialchars($post['title']); ?></h1>
        <div><p>Posted by <?= htmlspecialchars($post['author']); ?> on <?= htmlspecialchars($post['created_at']); ?></p></div>
        <div class="post-content"><?= !empty($post['image']) ? "<img src='".(filter_var($post['image'], FILTER_VALIDATE_URL) ? $post['image'] : 'uploads/'.$post['image'])."' alt='Post Image'>" : ''; ?>
            <p><?= nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>

        <?php if ($_SESSION['username'] == $post['author']): ?>
            <!-- Delete Post Button, shown only if the user is the post author -->
            <form id="delete-post-form" method="POST">
                <input type="hidden" name="delete_post" value="1">
                <button type="button" class="delete-post-btn" onclick="confirmDelete()">Delete Post</button>
            </form>
        <?php endif; ?>

        <div class="like-dislike-container">
            <a href="post.php?id=<?= $post_id; ?>&action=like" class="like-btn <?= ($user_post_action == 'like') ? 'selected' : '' ?>">👍 <?= $post['likes']; ?></a>
            <a href="post.php?id=<?= $post_id; ?>&action=dislike" class="dislike-btn <?= ($user_post_action == 'dislike') ? 'selected' : '' ?>">👎 <?= $post['dislikes']; ?></a>
        </div>

        <!-- Comments Section -->
        <div class="comments-container">
            <h3>Comments</h3>
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $post_id; ?>" class="comment-form">
    <textarea name="comment" rows="4" placeholder="Add a comment..." required></textarea><br>
    <button type="submit">Submit</button>
</form>


            <?php foreach ($comments as $comment): ?>
                <div class="comment <?= $comment['is_pinned'] ? 'pinned-comment' : ''; ?>">
                    <p><?= nl2br(htmlspecialchars($comment['content'])); ?></p>
                    <div><span>By <?= htmlspecialchars($comment['username']); ?> on <?= htmlspecialchars($comment['created_at']); ?></span>
                        <?= $comment['is_pinned'] ? '<span class="pinned-badge">Pinned</span>' : ''; ?>
                    </div>
                    <div class="like-dislike-container">
                        <a href="post.php?id=<?= $post_id; ?>&comment_id=<?= $comment['id']; ?>&action=like_comment" class="like-btn <?= ($comment['user_action'] == 'like_comment') ? 'selected' : '' ?>">👍 <?= $comment['likes']; ?></a>
                        <a href="post.php?id=<?= $post_id; ?>&comment_id=<?= $comment['id']; ?>&action=dislike_comment" class="dislike-btn <?= ($comment['user_action'] == 'dislike_comment') ? 'selected' : '' ?>">👎 <?= $comment['dislikes']; ?></a>
                    </div>

                    <?php if ($post['author'] == $_SESSION['username']): ?>
                        <div><a href="post.php?id=<?= $post_id; ?>&comment_id=<?= $comment['id']; ?>&action=pin_comment"><?= $comment['is_pinned'] ? 'Unpin' : 'Pin'; ?></a> | 
                            <a href="post.php?id=<?= $post_id; ?>&comment_id=<?= $comment['id']; ?>&action=remove_comment">Remove</a></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
<?php include 'footer.php'; ?>
</html>

<?php
ob_end_flush(); // End output buffering to send headers properly
?>
