<?php   session_start(); 
include 'header.php'; ?> <!-- Include the header.php file -->

<main style="flex: 1;">
    <div class="contact-container" style="max-width: 900px; margin: 0 auto; padding: 20px; font-size: 14px; color: #333;">
        <h2>Contact Us</h2>
        <p>If you have any questions or need further assistance, feel free to reach out to us. Fill out the form below, and we’ll get back to you as soon as possible.</p>

        <form action="send_email.php" method="POST" style="margin-top: 20px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="name" style="display: block; margin-bottom: 5px;">Your Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 5px;">Your Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="subject" style="display: block; margin-bottom: 5px;">Subject:</label>
                <input type="text" id="subject" name="subject" placeholder="Enter the subject" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="message" style="display: block; margin-bottom: 5px;">Your Message:</label>
                <textarea id="message" name="message" rows="5" placeholder="Enter your message here" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
            </div>

            <button type="submit" style="padding: 10px 20px; background-color: #007f88; color: white; border: none; border-radius: 5px; cursor: pointer;">Send Message</button>
        </form>

        <h3 style="margin-top: 30px;">Our Phone</h3>
        <p>Phone: 99722383</p>

        <h3 style="margin-top: 30px;">Our Email</h3>
        <p>Email: <a href="mailto:LeonidPower@hotmail.com" style="color: #007f88; text-decoration: none;">LeonidPower@hotmail.com</a></p>
        <h3 style="margin-top: 30px;">Our Location</h3>
        <!-- Optional Map Embed -->
        <div style="margin-top: 15px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3280.709957510404!2d33.046919276558614!3d34.679562674431736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14e735bfc6ae77e3%3A0x506f8f56d62b84c3!2sP.E.O.%20Student%20Calls!5e0!3m2!1sen!2scy!4v1695659631368!5m2!1sen!2scy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?> <!-- Include the footer.php file -->
