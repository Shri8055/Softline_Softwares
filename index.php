<?php
$counter_file = 'counter.txt';

if(file_exists($counter_file)) {
    $current_count = (int)file_get_contents($counter_file);
} else {
    $current_count = 0;
}

$current_count++;

file_put_contents($counter_file, $current_count);

$message = "";

if (isset($_POST['career-btn'])) {
    $name = $_POST['fname'];
    $email = $_POST['email'];
    $jobtype = $_POST['position'];
    $currstatus = $_POST['status'];

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed_ext = ['pdf', 'doc', 'docx'];
        $file_ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $upload_dir = "uploads/resumes/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = uniqid() . "." . $file_ext;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
                $conn = new mysqli("localhost", "root", "", "ss", 4306);
                $stmt = $conn->prepare("INSERT INTO career (name, email, jobtype, currstatus, resume_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $jobtype, $currstatus, $target_file);
                $stmt->execute();
                $stmt->close();
                $conn->close();

                header("Location: index.php?success=1");
                exit();
            } else {
                header("Location: index.php?error=upload");
                exit();
            }
        } else {
            header("Location: index.php?error=type");
            exit();
        }
    } else {
        header("Location: index.php?error=nofile");
        exit();
    }
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ss";

$conn = new mysqli($servername, $username, $password, $dbname, 4306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Track daily visitor
$today = date('Y-m-d');
$conn->query("
    INSERT INTO daily_visits (visit_date, count)
    VALUES ('$today', 1)
    ON DUPLICATE KEY UPDATE count = count + 1
");

// Handle Contact Form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact-btn'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $full_name = $first_name . ' ' . $last_name;
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $idea = $_POST['idea'];

    $conn = new mysqli("localhost", "root", "", "ss", 4306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO contact_queries (full_name, phone, email, idea) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $phone, $email, $idea);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: index.php?contact_success=1");
    exit();
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if ($user === "softlinesoftwares@gmail.com" && $pass === "102030") {
        echo "<div class='success'>Login Successful ✅</div>";
        echo "<script>window.open('admin.php', '_blank');</script>";
    } else {
        echo "<div class='error'>Invalid Credentials ❌</div>";
    }
}
?>


<script>
window.onload = function() {
    const params = new URLSearchParams(window.location.search);
    if(params.has('success')) {
        alert('Application submitted successfully!');
    }
    if(params.get('error') === 'upload') {
        alert('Failed to upload file.');
    }
    if(params.get('error') === 'type') {
        alert('Invalid file type. Only PDF, DOC, DOCX allowed.');
    }
    if(params.get('error') === 'nofile') {
        alert('Please upload your resume.');
    }
    if(params.has('contact_success')) {
        alert('Your query has been submitted successfully!');
    }
};
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Softline Softwares</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="nav" id="main-nav">
    <ul>
        <li><img src="SOFTLINE.png" alt=""></li>
        <div class="hamburger" id="hamburger">&#9776;</div>

        <div class="inr-li" id="nav-links">
            <a href="#home"><li>Home</li></a>
            <a href="#about"><li>About</li></a>
            <a href="#services"><li>Services</li></a>
            <a href="#careers"><li>Careers</li></a>
            <a href="#contact"><li>Contact</li></a>
            <a href="#" class="login-icon"><i class="fa-solid fa-user-astronaut"></i></a>
        </div>
    </ul>
</div>
<script>
    function handleResizeCanvas() {
  const canvas = document.getElementById('bg');
  if (window.innerWidth < 768) {
    canvas.style.display = 'none';
  } else {
    canvas.style.display = 'block';
  }
}

handleResizeCanvas();

window.addEventListener('resize', handleResizeCanvas);

</script>
<script>
    function handleResizeNav() {
  const navLinks = document.getElementById('nav-links');
  const hamburger = document.getElementById('hamburger');

  if (window.innerWidth < 768) {
    // Mobile view
    navLinks.style.display = 'none';
    hamburger.style.display = 'block';
  } else {
    // Desktop view
    navLinks.style.display = 'flex';
    hamburger.style.display = 'none';
  }
}

// Call once on load
handleResizeNav();

// Call every time window resizes
window.addEventListener('resize', handleResizeNav);
</script>
<script>
  const hamburger = document.getElementById("hamburger");
  const navLinks = document.getElementById("nav-links");

  hamburger.addEventListener("click", () => {
    navLinks.style.display = navLinks.style.display === "flex" ? "none" : "flex";
  });
</script>


    <div class="popup-overlay" id="loginPopup">
    <div class="popup-content">
      <div class="popup-left">
        <img src="logo.png" alt="Logo">
      </div>
      <div class="popup-right">
        <span class="close-btn" id="closePopup">&times;</span>
        <h2>Login</h2>
        <form method="POST" id="login">
          <input type="text" name="username" placeholder="Username" required><br><br>
          <input type="password" name="password" placeholder="Password" required><br><br>
          <button type="submit">Login</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const loginIcon = document.querySelector('.login-icon');
    const loginPopup = document.getElementById('loginPopup');
    const closePopup = document.getElementById('closePopup');

    loginIcon.addEventListener('click', (e) => {
      e.preventDefault();
      loginPopup.style.display = 'flex';
    });

    closePopup.addEventListener('click', () => {
      loginPopup.style.display = 'none';
    });

    loginPopup.addEventListener('click', (e) => {
      if (e.target === loginPopup) {
        loginPopup.style.display = 'none';
      }
    });
  </script>
    <script>
        const nav = document.getElementById('main-nav');
        let lastScroll = 0;
    
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
    
            if (currentScroll > 50) {
                nav.classList.add('show');
                nav.classList.add('animate-border');
            } else {
                nav.classList.remove('show');
                setTimeout(() => {
                    nav.classList.remove('animate-border');
                }, 300);
            }
    
            lastScroll = currentScroll;
        });
    </script>
    
    <div class="container" id="home">
        <canvas id="bg"></canvas>
        <h1>Optimize Everything,<br>Innovative Solutions, Simplifying Business.</h1>
    </div>
    
    <div class="diagonal-section" id="about">
        <h2>INNOVATION<br>STARTS<br>HERE.</h2>
        <img src="logo.png" data-src="logo.png" alt="Logo" class="floating-logo" />
        <p class="diagonal-section-p"><span class="highlight-head">Founded in 1999</span>
            <br><br>
            <span class="highlight-txt">With 25+ years of experience,</span> We craft scalable software that drives innovation. <br><br>
            From sleek interfaces to robust backends, we do it all. <br><br>
            Built with precision, tested for performance, deployed with confidence. <br><br>
            Your vision, transformed into digital reality.</p>
    </div>
    <script>
        const about = document.querySelectorAll('.diagonal-section');
        const images = document.querySelectorAll('img[data-src]');

        const aboutobserver = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        about.forEach(sec => aboutobserver.observe(sec));

        const imageObserver = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src; 
                    img.classList.add('loaded'); 
                    obs.unobserve(img);
                }
            });
        }, { threshold: 0.1 });

        images.forEach(img => imageObserver.observe(img));
    </script>
    <div class="service-container" id="services">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-newspaper"></i>Newspaper</h3>
            <p>We design and deliver content-rich newspaper platforms that enhance readability and accessibility.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-industry"></i>Industrial</h3>
            <p>We make tailor-made industrial software that tracks details from the first to last step of production.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-school"></i>Educational</h3>
            <p>We build education platforms that make learning intuitive, interactive, and engaging for all users.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-store"></i>E-commerce</h3>
            <p>We create scalable e-commerce platforms that ensure smooth user experience and optimized checkout flows.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-sitemap"></i>Distributor</h3>
            <p>Custom tools for tracking, managing, and optimizing product distribution and logistics pipelines.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-globe"></i>Web Services</h3>
            <p>We provide robust APIs and web integrations for seamless connectivity across platforms and applications.</p>
        </div>
    </div>
    <div class="service-container" >
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-cloud-arrow-up"></i>Cloud Computing</h3>
            <p>Our cloud solutions ensure scalable, secure, and flexible infrastructure for modern business needs.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-icons"></i>Social Media Automation</h3>
            <p>Automate content, schedule posts, and analyze performance with our smart social media tools.</p>
        </div>
    </div>
    <div class="service-container">
        <div class="inr-service-container">
            <h3><i class="fa-solid fa-file-shield"></i>Data Security</h3>
            <p>We offer encryption, access controls, and monitoring tools to secure your data end-to-end.</p>
        </div>
    </div>
    <form action="index.php" method="post" id="careers" enctype="multipart/form-data" class="hidden-form">
    <h2 class="sub-text">Fill out the form below for job opportunities</h2><hr><br>
    <table class="form-table">
        <tr>
            <td><input type="text" name="fname" placeholder="Full Name" required></td>
            <td><input type="email" name="email" placeholder="Email" required></td>
        </tr>
        <tr>
            <td colspan="2">What position are you applying for :</td>
        </tr>
        <tr>
            <td colspan="2">
                <select name="position" required>
                    <option value="">Select Position</option>
                    <option value="Full stack Web Dev">Full stack Web Dev</option>
                    <option value="Frontend Developer">Frontend Developer</option>
                    <option value="Backend Developer">Backend Developer</option>
                    <option value="Mobile App Development">Mobile App Development</option>
                    <option value="DevOps Engineer">DevOps Engineer</option>
                    <option value="QA Software tester">QA Software tester</option>
                    <option value="Cloud Engineer">Cloud Engineer</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">Specify your current employment status :</td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="radios" required>
                    <label><input type="radio" name="status" value="Employed" required> Employed</label>
                    <label><input type="radio" name="status" value="Unemployed"> Unemployed</label>
                    <label><input type="radio" name="status" value="Self-Employed"> Self-Employed</label>
                    <label><input type="radio" name="status" value="Student"> Student</label>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label> Upload your resume (.pdf, .doc, .docx) :</label><br><br>
                <input type="file" name="resume" required>
            </td>
        </tr>
    </table>
    <input class="submit-btn" type="submit" name="career-btn" value="Submit">
</form>

<form action="index.php#contact" method="post" id="contact" class="form-table">
    <h2 class="sub-text">Have a project in mind? Let's talk!</h2><hr><br>
    <table>
        <tr>
            <td><input type="text" name="first_name" placeholder="First Name" required></td>
            <td><input type="text" name="last_name" placeholder="Last Name" required></td>
        </tr>
        <tr>
            <td><input type="number" name="phone" placeholder="Phone Number" required></td>
            <td><input type="email" name="email" placeholder="Email" required></td>
        </tr>
        <tr>
            <td colspan="2"><textarea name="idea" rows="5" placeholder="Tell us about your project, idea, or inquiry..." required></textarea></td>
        </tr>
    </table>
    <input class="contactbtn" type="submit" name="contact-btn" value="Submit">
</form>
<hr class="footer-hr">
<footer>
    <div class="logo-footer">
        <img src="SOFTLINE.png" alt="">
    </div>
    <div class="links-footer">
        <ul> 
            <a href="#home"><li>Home</li></a>
            <a href="#about"><li>About</li></a>
            <a href="#services"><li>Services</li></a>
            <a href="#careers"><li>Careers</li></a>
            <a href="#contact"><li>Contact</li></a>
        </ul>
    </div>
    <div class="maps-footer">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d643.8021007542108!2d74.20569724352394!3d16.66692237807372!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2sin!4v1747281905024!5m2!1sen!2sin" width="500" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</footer>
<p class="copyright">Copyright © , Softline Softwares 2025</p>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('careers');

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    form.classList.add('show-form');
                    observer.unobserve(form);
                }
            });
        }, { threshold: 0.3 });
        observer.observe(form);
    });
</script>
    <script>
        const sections = document.querySelectorAll('.service-container');
        const observer = new IntersectionObserver((entries, obs) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('in-view');
              obs.unobserve(entry.target);
            }
          });
        }, { threshold: 0.3 });
        sections.forEach(sec => observer.observe(sec));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.150.1/build/three.min.js"></script>
  <script src="script.js"></script>
</body>
</html>