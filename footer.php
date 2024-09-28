<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Ensuring that body and main sections take full height to push footer down */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1; /* This will make the footer go down if the content is smaller */
        }

        footer {
            background-color: #222; /* Dark background */
            color: white;
            padding: 10px 0;
            width: 100%; /* Make sure it spans the entire width of the page */
        }

        .footer-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .footer-about, .footer-links {
            margin: 20px;
            flex: 1;
        }

        .footer-about h3, .footer-links h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .footer-about p {
            margin-bottom: 15px;
            font-size: 14px;
            color: #ccc;
        }

        .footer-about i {
            color: #007f88;
        }

        .footer-about a {
            color: #007f88;
            text-decoration: none;
        }

        .footer-about a:hover {
            text-decoration: underline;
        }

        .footer-socials a {
            margin-right: 10px;
            color: white;
            font-size: 20px;
            text-decoration: none;
        }

        .footer-socials a:hover {
            color: #007f88; /* Icon hover color */
        }

        .footer-links ul {
            list-style-type: none;
            padding: 0;
        }

        .footer-links ul li {
            margin-bottom: 10px;
        }

        .footer-links ul li a {
            color: #ccc;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links ul li a:hover {
            color: #007f88;
            text-decoration: underline;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                align-items: center;
            }

            .footer-about, .footer-links {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Page Content wrapped in main -->
    <main>
        <div class="content">
            <!-- Your page content goes here -->
        </div>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <!-- About Section -->
            <div class="footer-about">
                <h3>Blog Platform</h3>
                <p>Blog Platform is a fictional blog created for the purpose of this tutorial. It’s an amazing place to find inspiring posts and content.</p>
                <p><i class="fas fa-phone-alt"></i> 99722383</p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:LeonidPower@hotmail.com">LeonidPower@hotmail.com</a></p>
                
                <!-- Social Media Icons -->
                <div class="footer-socials">
                    <a href="https://www.linkedin.com/in/leonidas-ttofari-30237a214/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://www.youtube.com/@Leonid_Power" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="https://x.com/LeonidPower" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.reddit.com/user/LeonidPower/" target="_blank"><i class="fab fa-reddit-alien"></i></a>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="terms.php">Terms and conditions</a></li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>
