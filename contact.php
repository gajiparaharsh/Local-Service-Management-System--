<?php
/**
 * Contact Page - Local Service Finder
 */
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance()->getConnection();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
            $success = 'Thank you for your message! We will get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Get in touch with us.</p>
    </div>
</section>

<section class="contact-section py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Info -->
            <div class="col-lg-4" data-aos="fade-right">
                <div class="contact-info-card">
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h5>Our Address</h5>
                            <p>123 Service Street<br>Mumbai, Maharashtra 400001</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h5>Phone Number</h5>
                            <p><?php echo SITE_PHONE; ?></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h5>Email Address</h5>
                            <p><?php echo SITE_EMAIL; ?></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <h5>Working Hours</h5>
                            <p>Mon - Sat: 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
                
                <div class="social-links-card mt-4">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8" data-aos="fade-left">
                <div class="contact-form-card">
                    <h3 class="mb-4">Send us a Message</h3>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Your Name *</label>
                                <input type="text" name="name" class="form-control" required placeholder="John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91 98765 43210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="How can we help?">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Your Message *</label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="Write your message here..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-info-card, .contact-form-card, .social-links-card {
    background: var(--dark-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    padding: 2rem;
}
.info-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
.info-item:last-child { margin-bottom: 0; }
.info-icon { width: 50px; height: 50px; background: var(--gradient-primary); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white; flex-shrink: 0; }
.info-item h5 { margin-bottom: 0.25rem; font-size: 1rem; }
.info-item p { margin: 0; color: var(--light-300); font-size: 0.875rem; }
.social-icons { display: flex; gap: 0.75rem; }
.social-icon { width: 45px; height: 45px; background: var(--dark-700); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: var(--light-300); text-decoration: none; transition: var(--transition-base); }
.social-icon:hover { background: var(--primary); color: white; transform: translateY(-3px); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
