<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterHive - Internship Search</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f4f7fa;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        body, html {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.7);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
        }

        .logo-wrapper img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo {
            font-size: 26px;
            font-weight: 700;
            color: rgb(52, 2, 9);
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        nav ul li a {
            text-decoration: none;
            font-size: 16px;
            color: #4a4a4a;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: rgb(52, 2, 9);
        }

        .login-button {
            background: rgb(52, 2, 9);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 16px;
        }

        .login-button:hover {
            background: rgb(30, 1, 6);
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100vh;
            background: url('images/hb2.jpg') center/cover no-repeat;
            color: white;
            padding: 20px;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .btn {
            background: rgb(52, 2, 9);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:hover {
            background: rgb(30, 1, 6);
            transform: translateY(-2px);
        }

        section {
            padding: 80px 0;
            text-align: center;
        }

        .about {
            background: #eef1f5;
        }

        .about h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: rgb(52, 2, 9);
        }

        .about p {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
            color: #555;
        }

        .internship-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .internship {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
            transition: transform 0.3s ease-in-out;
        }

        .internship:hover {
            transform: scale(1.02);
        }

        .internship-logo {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .internship h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: rgb(52, 2, 9);
        }

        .details {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }

        .footer {
            background: #222;
            color: white;
            text-align: center;
            padding: 25px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo-wrapper">
                    <img src="images/logo.png" alt="Logo">
                    <div class="logo">InterHive</div>
                </div>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#internships">Internships</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#FAQs">FAQs</a></li>
                    <li><button class="login-button" onclick="window.location.href='login.php'">Login</button></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div>
            <h1>Find Your Dream Internship</h1>
            <p>Connecting students with top companies for the best internship opportunities.</p>
        </div>
    </section>

    <!-- ABOUT US Section -->
    <section class="about-section" id="about">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

    .about-section {
      background-color: #fdf6ec;
      padding: 60px 20px;
      font-family: 'Roboto', sans-serif;
      color: rgb(52, 2, 9);
    }

    .about-section h1 {
      text-align: center;
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 50px;
      color: rgb(52, 2, 9);
    }

    .intro-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 40px;
      max-width: 1100px;
      margin: auto;
    }

    .intro-text {
      flex: 1;
      min-width: 280px;
    }

    .intro-text h2 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 15px;
    }

    .intro-text p {
      font-size: 16px;
      line-height: 1.6;
    }

    .intro-img {
      flex: 1;
      min-width: 280px;
      text-align: center;
    }

    .intro-img img {
      max-width: 100%;
      border-radius: 20px;
      object-fit: cover;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .section-title {
      text-align: center;
      font-size: 24px;
      font-weight: 700;
      margin: 60px 0 30px;
    }

    .core-values {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      text-align: center;
    }

    .value-box {
      width: 200px;
    }

    .value-box img {
      height: 40px;
      margin-bottom: 10px;
    }

    .value-box h4 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 5px;
    }

    .value-box p {
      font-size: 14px;
    }

    .audience {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 60px;
      text-align: center;
    }

    .audience-box img {
      height: 80px;
      margin-bottom: 10px;
    }

    .audience-box h4 {
      font-size: 16px;
      font-weight: 700;
    }

    .audience-box p {
      font-size: 14px;
      max-width: 200px;
      margin: 0 auto;
    }

    .quotes {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 60px;
      margin-top: 40px;
    }

    .quote-box {
      max-width: 400px;
    }

    .quote-box h4 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .quote-box p {
      font-size: 15px;
      font-style: italic;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      .intro-row {
        flex-direction: column;
        text-align: center;
      }

      .core-values,
      .audience,
      .quotes {
        flex-direction: column;
        gap: 30px;
      }

      .value-box,
      .audience-box,
      .quote-box {
        margin: 0 auto;
      }
    }
  </style>

  <h1>About us</h1>

  <div class="intro-row">
    <div class="intro-text">
      <h2>We make internship discovery simple, trusted & efficient.</h2>
      <p>
        At InterHive, we bridge the gap between talented individuals and top-tier internship opportunities.
        Whether you’re a student eager to gain hands-on experience or a company seeking fresh minds, we
        ensure a seamless and secure connection.
      </p>
    </div>
    <div class="intro-img">
      <img src='images/hb.jpg' alt="Internship Image">
    </div>
  </div>

  <h2 class="section-title">Our Core Values</h2>
  <div class="core-values">
    <div class="value-box">
      <img src="https://img.icons8.com/ios-filled/50/000000/checked--v1.png" alt="Reliable Platform">
      <h4>Reliable Platform</h4>
      <p>Admin-approved companies for a secure experience</p>
    </div>
    <div class="value-box">
      <img src="https://img.icons8.com/ios-filled/50/000000/user.png" alt="User Focused">
      <h4>User-Focused</h4>
      <p>Simple login, instant access, and easy apply</p>
    </div>
    <div class="value-box">
      <img src="https://img.icons8.com/ios-filled/50/000000/combo-chart--v1.png" alt="Growth-Oriented">
      <h4>Growth-Oriented</h4>
      <p>Helping students build real-world skills</p>
    </div>
  </div>

  <h2 class="section-title">Who We Serve</h2>
  <div class="audience">
    <div class="audience-box">
      <img src="https://img.icons8.com/ios-filled/100/000000/student-male.png" alt="Students">
      <h4>Students & Graduates</h4>
      <p>Explore internships suited to your field and interest</p>
    </div>
    <div class="audience-box">
      <img src="https://img.icons8.com/ios-filled/100/000000/manager--v2.png" alt="Companies">
      <h4>Companies</h4>
      <p>List and manage internships (with admin oversight)</p>
    </div>
  </div>

  <div class="quotes">
    <div class="quote-box">
      <h4>Hear From Us</h4>
      <p>
        “Creating opportunity means connecting talent with trust. That’s what InterHive stands for.”<br>
        – InterHive Team
      </p>
    </div>
    <div class="quote-box">
      <h4>Our Vision</h4>
      <p>
        To be the most trusted and widely used internship platform, making internships more accessible and
        meaningful for everyone.
      </p>
    </div>
  </div>
</section>

<section id="internships" class="internships">
    <div class="container">
        <h2>Our Internships</h2>
        <div class="internship-list">
            <?php
            // Database connection
            $conn = new mysqli("localhost", "root", "", "internship_platform");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch internships
            $sql = "SELECT * FROM internships";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($intern = $result->fetch_assoc()) {
                    echo '
                    <div class="internship">
                        <img src="' . htmlspecialchars($intern['logo']) . '" alt="' . htmlspecialchars($intern['course_name']) . '" class="internship-logo" onerror="this.onerror=null;this.src=\'images/default.png\';">
                        <h3>' . htmlspecialchars($intern['course_name']) . '</h3>
                        <button class="btn" onclick="toggleDetails(this)">View Details</button>
                        <div class="details" style="display: none;">
                            <p><strong>Description:</strong> ' . htmlspecialchars($intern['course_description']) . '</p>
                            <p><strong>Requirements:</strong> ' . htmlspecialchars($intern['requirements']) . '</p>
                            <p><strong>Location:</strong> ' . htmlspecialchars($intern['location']) . '</p>
                            <p><strong>Duration:</strong> ' . htmlspecialchars($intern['course_duration']) . '</p>
                            <p><strong>Start Date:</strong> ' . htmlspecialchars($intern['start_date']) . '</p>
                        </div>
                        <button class="btn" onclick="applyNow()">Apply Now</button>
                    </div>';
                }
            } else {
                echo "<p>No internships available at the moment.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</section>

<script>
function toggleDetails(btn) {
    const details = btn.nextElementSibling;
    if (details.style.display === "none") {
        details.style.display = "block";
        btn.textContent = "Hide Details";
    } else {
        details.style.display = "none";
        btn.textContent = "View Details";
    }
}

function applyNow() {
    alert("Redirecting to application form..."); // replace with actual redirection
}
</script>



    <style>
    body {
      font-family: 'Lato', sans-serif;
      margin: 0;
      padding: 0;
    }
    #contact {
      width: 100%;
    }
    .contact-header {
      background-image: url('images/home.jpg');
      background-size: cover;
      background-position: center;
      padding: 100px 20px 50px;
      text-align: center;
      color: white;
    }
    .contact-header h2 {
      font-size: 40px;
      margin: 0;
    }
    .contact-icons {
      display: flex;
      justify-content: center;
      gap: 30px;
      background: #fff;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(179, 235, 11, 0.1);
      background-color: #fdf6ec;
    }
    .contact-icons div {
      text-align: center;
      max-width: 200px;
    }
    .contact-icons i {
      font-size: 24px;
      color: #d62828;
      margin-bottom: 10px;
    }
    .contact-body {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 40px 20px;
     background-color: #fdf6ec;
    }
    .contact-form, .map {
      flex: 1;
      min-width: 300px;
      max-width: 450px;
      margin: 20px;
    }
    .contact-form h3 {
      margin-bottom: 20px;
    }
    .contact-form input, .contact-form textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .contact-form button {
      background-color: #d62828;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<section id="contact">
  <div class="contact-header">
    <h2>Contact Us</h2>
  </div>

  <div class="contact-icons">
    <div>
      <i class="fa fa-phone"></i>
      <p><strong>Call Us</strong><br />+91 9876543210
      <br />+91 6238731519</p>
    </div>
    <div>
      <i class="fa fa-map-marker"></i>
      <p><strong>Address</strong><br />CN Tower
      290 Bremner Blvd,
      Toronto, ON M5V 3L9,
      Canada </p>
    </div>
    <div>
      <i class="fa fa-envelope"></i>
      <p><strong>Email</strong><br />info@interhive.com</p>
    </div>
    <div>
      <i class="fa fa-clock-o"></i>
      <p><strong>Office Hours</strong><br />Mon–Fri: 9AM – 6PM</p>
    </div>
  </div>
  <div class="contact-body" style="display: flex; gap: 20px; align-items: flex-start;">
  <!-- Contact Form -->
  <div class="contact-form" style="flex: 1;">
    <h3>We are here to help you!</h3>
    <form method="post" action="" onsubmit="return showPopup()">
      <input type="text" name="name" placeholder="Your Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="text" name="subject" placeholder="Subject" required />
      <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
      <button type="submit" name="send">Send Message</button>
    </form>
  </div>

  <!-- Image / Map -->
  <div class="map" style="flex: 1;">
    <img src='images/hb5.jpg' alt="Map" style="width: 100%; border-radius: 4px;">
  </div>

  <!-- Popup Script -->
  <script>
    function showPopup() {
      alert("We will touch with you within 24 hrs through email.");
      return true; // Continue with form submission
    }
  </script>
</div>




  <style>
    body {
      font-family: 'Lato', sans-serif;
      background-color: #fdf6ec;
      padding: 0px;
    }

    .faq {
      max-width: 1400px;
      margin: auto;
    }

    .faq h3 {
      font-size: 1rem;
      font-weight: 800;
      margin-bottom: 30px;
    }

    .faq-item {
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .faq-question {
      font-weight: bold;
      font-size: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
    }

    .faq-answer {
      margin-top: 10px;
      color: #444;
      display: none;
    }

    .faq-item.active .faq-answer {
      display: block;
    }

    .plus {
      font-size: 1rem;
      transition: transform 0.3s ease;
    }

    .faq-item.active .plus {
      transform: rotate(45deg); /* '+' turns to 'x' style */
    }
  </style>
</head>
<body>
<section class="question-section" id="FAQs">
  <div class="faq">
    <h2>Frequently asked questions</h2>

    <div class="faq-item">
      <div class="faq-question">
        <span>How do I apply for InterHive?</span>
        <span class="plus">+</span>
      </div>
      <div class="faq-answer">
        Sign up and browse internships in your domain of interest.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>How long does it take to get a response?</span>
        <span class="plus">+</span>
      </div>
      <div class="faq-answer">
        Most companies reply within 3–5 working days.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>Can I contact the company directly?</span>
        <span class="plus">+</span>
      </div>
      <div class="faq-answer">
        Use the InterHive dashboard's contact information after selection.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">
        <span>Do I get a certificate?</span>
        <span class="plus">+</span>
      </div>
      <div class="faq-answer">
        Yes! After completion you’ll receive a verified certificate.
      </div>
    </div>
  </div>

  <script>
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
      const question = item.querySelector('.faq-question');
      question.addEventListener('click', () => {
        item.classList.toggle('active');
      });
    });
  </script>
  </section>
<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "internship_platform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid form resubmission on refresh
    
    exit();
}

// Optional success message
if (isset($_GET['message']) && $_GET['message'] == 'sent') {
    echo "<script>alert('Message sent successfully!,You will receive a reply within 24 hours at your registered email address');</script>";
}
?>


<!-- Font Awesome (for icons) -->
<script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" crossorigin="anonymous"></script>


<footer class="footer">
        <p>&copy; 2025 InterHive. All rights reserved.</p>
    </footer>

    <script>
        function toggleDetails(button) {
            const details = button.nextElementSibling;
            if (details.style.display === "none") {
                details.style.display = "block";
                button.textContent = "Hide Details";
            } else {
                details.style.display = "none";
                button.textContent = "View Details";
            }
        }

        function applyNow() {
            alert("Please log in to apply for this internship.");
            window.location.href = 'login.php';
        }
    </script>
</body>
</html>
