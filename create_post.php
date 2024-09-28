<?php
ob_start(); // Start output buffering
session_start(); // Start the session
include 'config.php';
include 'header.php'; // Make sure header.php does not have unintended output

// Rest of your code...

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $author = $_SESSION['username'];
    $created_at = date('Y-m-d H:i:s');

    // File upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];
        $upload_dir = 'uploads/';
        $image_folder = $upload_dir . $image;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($image_tmp, $image_folder)) {
            $sql = "INSERT INTO posts (title, content, author, image, created_at) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssss", $title, $content, $author, $image, $created_at);

                if ($stmt->execute()) {
                    header("Location: index.php"); // Redirect after successful post creation
                    exit();
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
            } else {
                echo "<p>Database error: Could not prepare statement.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Failed to upload image.</p>";
        }
    } else {
        echo "<p>No image uploaded or file upload error.</p>";
    }
}

ob_end_flush(); // Send the output buffer and turn off buffering
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="styles.css">
    <style>
         body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

/* Center the form container only */
.form-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: calc(100vh - 100px); /* Adjust based on your header height */
    padding: 20px;
}

.create-post-container {
    background-color: white;
    padding: 60px; /* Increased padding */
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px; /* Increased max-width */
    text-align: center;
}

h2 {
    font-size: 28px; /* Increased font size */
    margin-bottom: 30px; /* More space below the title */
    color: #333;
    text-align: center;
}

form input, form textarea {
    width: 100%;
    padding: 15px; /* Increased padding inside inputs */
    margin: 10px 0; /* More margin between elements */
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 18px; /* Increased font size for inputs */
}

form button {
    width: 30%;
    background-color: #007bff;
    color: white;
    padding: 15px; /* Increased button padding */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px; /* Increased font size for button */
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #0056b3;
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    font-size: 16px; /* Slightly larger link */
}

.back-link a {
    color: #007bff;
    text-decoration: none;
}

.back-link a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
<div class="form-wrapper">
    <div class="create-post-container">
        <h2>Create a New Post</h2>

        <form action="create_post.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Post Title" required>
            <textarea name="content" rows="5" placeholder="Post Content" required></textarea>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Create Post</button>
        </form>

        <div class="back-link">
            <a href="index.php">Go back to homepage</a>
        </div>
    </div>
    </div>
<?php include 'footer.php'; ?> <!-- Include footer -->
</body>
</html>
