<?php
session_start(); // Start the session
include 'config.php'; // Include database connection
include 'header.php'; // Include the header

// Fetch all posts with images from the database
$sql = "SELECT title, image FROM posts WHERE image IS NOT NULL ORDER BY created_at DESC";
$posts = [];
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .gallery-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 32px;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }

        .gallery-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .gallery-item {
            position: relative;
            width: 200px;
            height: 300px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-item-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="gallery-container">
        <h2>Image Gallery</h2>

        <!-- Gallery Grid -->
        <div class="gallery-grid">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="gallery-item">

                       <img src="<?php echo filter_var($post['image'], FILTER_VALIDATE_URL) ? $post['image'] : 'uploads/' . $post['image']; ?>" alt="Post Image">

                        <div class="gallery-item-title"><?php echo $post['title']; ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">No images available.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
<?php include 'footer.php'; ?>
</html>
