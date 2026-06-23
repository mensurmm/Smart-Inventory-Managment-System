<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact - StockPilot</title>
  <!-- Global Styles -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- Header/Footer Styles -->
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
  <link rel="stylesheet" href="../assets/css/contact.css">
</head>
<body>

  

  <main class="contact-section">
    <div class="contact-container">
      <div class="contact-info">
        <h2>Get in Touch</h2>
        <p>We’d love to hear from you. Reach out for support, inquiries, or feedback.</p>
        <ul>
          <li><strong>Email:</strong> support@stockpilot.com</li>
          <li><strong>Phone:</strong> +251 911 234 567</li>
          <li><strong>Address:</strong> Addis Ababa, Ethiopia</li>
        </ul>
      </div>

      <div class="contact-form">
        <h3>Send Us a Message</h3>
        <form action="contact_process.php" method="POST">
          <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" required placeholder="Enter your name">
          </div>
          <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">
          </div>
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required placeholder="Write your message"></textarea>
          </div>
          <button type="submit" class="btn-submit">Send Message</button>
        </form>
      </div>
    </div>
  </main>

  <!-- Reusable Footer -->
  <?php include '../templates/footer.php'; ?>

</body>
</html>
