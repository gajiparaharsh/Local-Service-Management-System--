<?php
/**
 * Home Page - Local Service Finder
 */
$pageTitle = 'Find Local Service Providers';
require_once __DIR__ . '/includes/header.php';

// Get database connection
$db = Database::getInstance()->getConnection();

// Fetch categories
$stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $stmt->fetchAll();

// Fetch featured providers
$providerQuery = "
    SELECT pp.*, u.full_name, u.profile_image, u.city,
           GROUP_CONCAT(DISTINCT c.name) as category_names
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id
    LEFT JOIN provider_services ps ON pp.id = ps.provider_id
    LEFT JOIN services s ON ps.service_id = s.id
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE pp.approval_status = 'approved' AND pp.is_available = 1 AND u.is_active = 1
    GROUP BY pp.id
    ORDER BY pp.avg_rating DESC, pp.total_bookings DESC
    LIMIT 8
";
$stmt = $db->query($providerQuery);
$featuredProviders = $stmt->fetchAll();

// Get stats
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'user' AND is_active = 1) as total_users,
        (SELECT COUNT(*) FROM provider_profiles WHERE approval_status = 'approved') as total_providers,
        (SELECT COUNT(*) FROM bookings WHERE status = 'completed') as total_bookings,
        (SELECT COUNT(*) FROM categories WHERE is_active = 1) as total_categories
";
$stats = $db->query($statsQuery)->fetch();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-bg">
        <div class="hero-gradient hero-gradient-1"></div>
        <div class="hero-gradient hero-gradient-2"></div>
        <div class="hero-gradient hero-gradient-3"></div>
    </div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content">
                    <h1>Find Trusted <span class="text-gradient">Local Services</span> Near You</h1>
                    <p>Connect with verified service providers in your area. Book plumbers, electricians, carpenters, and more with just a few clicks.</p>
                    
                    <div class="hero-actions">
                        <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-primary btn-lg btn-glow">
                            <i class="fas fa-search me-2"></i>Explore Services
                        </a>
                        <a href="<?php echo BASE_URL; ?>auth/register.php?type=provider" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Join as Provider
                        </a>
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number" data-counter="<?php echo $stats['total_users'] ?: 1000; ?>">0</div>
                            <div class="stat-label">Happy Users</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-counter="<?php echo $stats['total_providers'] ?: 250; ?>">0</div>
                            <div class="stat-label">Verified Providers</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-counter="<?php echo $stats['total_bookings'] ?: 5000; ?>">0</div>
                            <div class="stat-label">Services Completed</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="hero-image">
                    <img src="<?php echo ASSETS_URL; ?>images/hero-illustration.svg" alt="Local Services">
                    
                    <div class="hero-card hero-card-1">
                        <div class="hero-card-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="hero-card-content">
                            <h4>Verified Pros</h4>
                            <p>Background checked</p>
                        </div>
                    </div>
                    
                    <div class="hero-card hero-card-2">
                        <div class="hero-card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="hero-card-content">
                            <h4>4.8 Rating</h4>
                            <p>Customer satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search Box -->
        <div class="search-box" data-aos="fade-up" data-aos-delay="200">
            <form action="<?php echo BASE_URL; ?>search.php" method="GET" class="search-form">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="What service do you need?" required>
                </div>
                
                <div class="search-input-group">
                    <i class="fas fa-th-large"></i>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-input-group position-relative">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" id="locationInput" placeholder="Enter your location">
                    <button type="button" id="useLocationBtn" class="btn btn-icon position-absolute end-0 top-50 translate-middle-y" style="right: 8px !important;" title="Use my location">
                        <i class="fas fa-crosshairs"></i>
                    </button>
                </div>
                
                <button type="submit" class="search-btn">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="badge">Our Services</span>
            <h2>Browse by <span class="text-gradient">Category</span></h2>
            <p>Choose from a wide range of professional services to meet all your home and office needs.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($categories as $index => $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <a href="<?php echo BASE_URL; ?>services.php?category=<?php echo $category['slug']; ?>" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas <?php echo $category['icon'] ?: 'fa-cog'; ?>"></i>
                            </div>
                            <h4><?php echo htmlspecialchars($category['name']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($category['description'], 0, 60)); ?>...</p>
                            <?php
                            $serviceCount = $db->query("SELECT COUNT(*) FROM services WHERE category_id = {$category['id']} AND is_active = 1")->fetchColumn();
                            ?>
                            <span class="category-count"><?php echo $serviceCount; ?> Services</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="badge">Simple Process</span>
            <h2>How It <span class="text-gradient">Works</span></h2>
            <p>Book your service in just 3 simple steps</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Search Service</h4>
                    <p>Enter the service you need and your location to find available providers near you.</p>
                    <div class="step-connector d-none d-lg-block"></div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h4>Choose Provider</h4>
                    <p>Compare ratings, reviews, and prices. Select the best provider for your needs.</p>
                    <div class="step-connector d-none d-lg-block"></div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Book & Relax</h4>
                    <p>Schedule at your convenience. Sit back while our verified professionals handle the rest.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Providers -->
<?php if (!empty($featuredProviders)): ?>
<section class="providers-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="badge">Top Rated</span>
            <h2>Featured <span class="text-gradient">Service Providers</span></h2>
            <p>Meet our highest-rated professionals trusted by thousands of customers.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredProviders as $index => $provider): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <div class="provider-card">
                        <div class="provider-header">
                            <img src="<?php echo UPLOADS_URL . ($provider['profile_image'] ?: 'profiles/default-avatar.png'); ?>" 
                                 alt="<?php echo htmlspecialchars($provider['full_name']); ?>" 
                                 class="provider-avatar">
                            <?php if ($provider['verified_badge']): ?>
                                <span class="provider-badge"><i class="fas fa-check me-1"></i>Verified</span>
                            <?php endif; ?>
                        </div>
                        <div class="provider-content">
                            <h4><?php echo htmlspecialchars($provider['full_name']); ?></h4>
                            <p class="provider-category">
                                <?php echo htmlspecialchars($provider['category_names'] ?: $provider['business_name']); ?>
                            </p>
                            <div class="provider-rating">
                                <div class="rating-stars">
                                    <?php 
                                    $rating = round($provider['avg_rating']);
                                    for ($i = 1; $i <= 5; $i++): 
                                    ?>
                                        <i class="fas fa-star<?php echo $i <= $rating ? '' : '-half-alt'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-count">(<?php echo $provider['total_reviews']; ?> reviews)</span>
                            </div>
                            <div class="provider-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($provider['city'] ?: 'Local'); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo $provider['experience_years']; ?>+ Yrs</span>
                                </div>
                            </div>
                        </div>
                        <div class="provider-footer">
                            <a href="<?php echo BASE_URL; ?>provider-profile.php?id=<?php echo $provider['id']; ?>" class="btn btn-outline-primary">
                                View Profile
                            </a>
                            <a href="<?php echo BASE_URL; ?>book-service.php?provider=<?php echo $provider['id']; ?>" class="btn btn-primary">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo BASE_URL; ?>providers.php" class="btn btn-primary btn-lg">
                <i class="fas fa-users me-2"></i>View All Providers
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="badge">Testimonials</span>
            <h2>What Our <span class="text-gradient">Customers Say</span></h2>
            <p>Real experiences from satisfied customers across the city.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="testimonial-card">
                    <div class="testimonial-quote">"</div>
                    <div class="testimonial-content">
                        <p>Amazing service! The plumber arrived on time and fixed my kitchen leak within an hour. Highly professional and reasonably priced.</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Customer">
                        <div class="author-info">
                            <h5>Priya Sharma</h5>
                            <p>Mumbai</p>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-quote">"</div>
                    <div class="testimonial-content">
                        <p>Found an excellent electrician through this platform. The booking process was super easy and the work quality was exceptional.</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Customer">
                        <div class="author-info">
                            <h5>Rahul Verma</h5>
                            <p>Delhi</p>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-quote">"</div>
                    <div class="testimonial-content">
                        <p>Best AC repair service ever! They diagnosed the problem quickly and the technician explained everything clearly. Will use again!</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Customer">
                        <div class="author-info">
                            <h5>Ananya Patel</h5>
                            <p>Bangalore</p>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="zoom-in">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of satisfied customers and find your perfect service provider today.</p>
            <div class="cta-buttons">
                <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
                <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-search me-2"></i>Browse Services
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
