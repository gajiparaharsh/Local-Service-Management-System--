<?php
/**
 * About Page - Local Service Finder
 */
$pageTitle = 'About Us';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance()->getConnection();

// Get stats
$totalProviders = $db->query("SELECT COUNT(*) FROM provider_profiles WHERE approval_status = 'approved'")->fetchColumn();
$totalServices = $db->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn();
$totalBookings = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn();
?>

<section class="page-header">
    <div class="container">
        <h1>About Us</h1>
        <p>Your trusted platform for local service professionals</p>
    </div>
</section>

<section class="about-section py-5">
    <div class="container">
        <!-- Mission -->
        <div class="row align-items-center mb-5" data-aos="fade-up">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600" alt="Our Team" class="img-fluid rounded-4">
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="mb-4">Our Mission</h2>
                <p class="lead">We connect you with skilled local professionals to handle all your service needs, from home repairs to personal care.</p>
                <p>Founded in 2024, Local Service Finder was created with a simple vision: to make finding reliable local service providers easy, quick, and trustworthy. We understand the frustration of searching for qualified professionals, which is why we've built a platform that thoroughly vets every service provider.</p>
                <div class="row g-3 mt-4">
                    <div class="col-6">
                        <div class="feature-box">
                            <i class="fas fa-shield-alt"></i>
                            <h5>Verified Providers</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-box">
                            <i class="fas fa-star"></i>
                            <h5>Quality Service</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-box">
                            <i class="fas fa-headset"></i>
                            <h5>24/7 Support</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-box">
                            <i class="fas fa-wallet"></i>
                            <h5>Fair Pricing</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-section mb-5" data-aos="fade-up">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <h2 class="counter"><?php echo $totalProviders; ?>+</h2>
                        <p>Verified Providers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <h2 class="counter"><?php echo $totalServices; ?>+</h2>
                        <p>Services Available</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <h2 class="counter"><?php echo $totalBookings; ?>+</h2>
                        <p>Jobs Completed</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <h2>4.8<small>/5</small></h2>
                        <p>Customer Rating</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Why Choose Us -->
        <div class="why-choose-section" data-aos="fade-up">
            <h2 class="text-center mb-5">Why Choose Us?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-user-check"></i></div>
                        <h4>Trusted Professionals</h4>
                        <p>All our service providers go through a rigorous verification process to ensure quality and reliability.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-bolt"></i></div>
                        <h4>Quick Booking</h4>
                        <p>Book your desired service in just a few clicks. Our streamlined process saves you time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-thumbs-up"></i></div>
                        <h4>Satisfaction Guaranteed</h4>
                        <p>We ensure customer satisfaction with our quality assurance and support system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.about-image img { box-shadow: var(--shadow-lg); }
.feature-box { background: var(--dark-800); padding: 1.5rem; border-radius: var(--radius-lg); text-align: center; }
.feature-box i { font-size: 2rem; color: var(--primary-light); margin-bottom: 0.75rem; display: block; }
.feature-box h5 { margin: 0; font-size: 0.9rem; }
.stats-section { background: var(--gradient-primary); padding: 3rem 2rem; border-radius: var(--radius-xl); }
.stat-box h2 { font-size: 2.5rem; margin-bottom: 0.5rem; color: white; }
.stat-box p { margin: 0; color: rgba(255,255,255,0.9); }
.why-card { background: var(--dark-800); padding: 2rem; border-radius: var(--radius-xl); text-align: center; height: 100%; border: 1px solid var(--glass-border); transition: var(--transition-base); }
.why-card:hover { transform: translateY(-5px); border-color: var(--primary); }
.why-icon { width: 80px; height: 80px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; color: white; }
.why-card h4 { margin-bottom: 1rem; }
.why-card p { color: var(--light-300); margin: 0; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
