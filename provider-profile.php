<?php
/**
 * Provider Public Profile Page
 */
$pageTitle = 'Provider Profile';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance()->getConnection();

$providerId = intval($_GET['id'] ?? 0);

// Get provider info
$stmt = $db->prepare("
    SELECT pp.*, u.full_name, u.email, u.phone, u.profile_image, u.city, u.address
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id
    WHERE pp.id = ? AND pp.approval_status = 'approved'
");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

if (!$provider) {
    flashMessage('error', 'Provider not found.', 'danger');
    redirect('providers.php');
}

// Get provider's services
$stmt = $db->prepare("
    SELECT ps.*, s.name, s.description as service_desc, c.name as category_name
    FROM provider_services ps
    JOIN services s ON ps.service_id = s.id
    JOIN categories c ON s.category_id = c.id
    WHERE ps.provider_id = ? AND ps.is_active = 1
    ORDER BY s.name
");
$stmt->execute([$providerId]);
$services = $stmt->fetchAll();

// Get reviews
$stmt = $db->prepare("
    SELECT r.*, u.full_name, u.profile_image
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.provider_id = ? AND r.is_approved = 1
    ORDER BY r.created_at DESC
    LIMIT 10
");
$stmt->execute([$providerId]);
$reviews = $stmt->fetchAll();
?>

<section class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>providers.php">Providers</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($provider['full_name']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="provider-profile py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="profile-card" data-aos="fade-right">
                    <div class="profile-header">
                        <img src="<?php echo UPLOADS_URL . ($provider['profile_image'] ?: 'profiles/default-avatar.png'); ?>" 
                             class="profile-avatar" alt="Provider">
                        <?php if ($provider['verified_badge']): ?>
                            <span class="verified-tag"><i class="fas fa-check-circle me-1"></i>Verified</span>
                        <?php endif; ?>
                    </div>
                    <div class="profile-body">
                        <h3><?php echo htmlspecialchars($provider['full_name']); ?></h3>
                        <p class="business-name"><?php echo htmlspecialchars($provider['business_name'] ?: 'Independent Professional'); ?></p>
                        
                        <div class="rating-display">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $provider['avg_rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span><?php echo number_format($provider['avg_rating'], 1); ?></span>
                            <small class="text-muted">(<?php echo $provider['total_reviews']; ?> reviews)</small>
                        </div>
                        
                        <div class="profile-stats">
                            <div class="stat">
                                <strong><?php echo $provider['experience_years']; ?>+</strong>
                                <span>Years Exp.</span>
                            </div>
                            <div class="stat">
                                <strong><?php echo $provider['total_bookings']; ?></strong>
                                <span>Jobs Done</span>
                            </div>
                            <div class="stat">
                                <strong><?php echo count($services); ?></strong>
                                <span>Services</span>
                            </div>
                        </div>
                        
                        <div class="profile-info">
                            <p><i class="fas fa-map-marker-alt"></i><?php echo htmlspecialchars($provider['city'] ?: 'Local'); ?></p>
                            <p><i class="fas fa-phone"></i><?php echo htmlspecialchars($provider['phone']); ?></p>
                        </div>
                        
                        <a href="<?php echo BASE_URL; ?>book-service.php?provider=<?php echo $provider['id']; ?>" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-calendar-check me-2"></i>Book Now
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- About -->
                <div class="content-card mb-4" data-aos="fade-up">
                    <h4><i class="fas fa-user me-2"></i>About</h4>
                    <p><?php echo nl2br(htmlspecialchars($provider['bio'] ?: 'This professional has not added a bio yet.')); ?></p>
                </div>
                
                <!-- Services -->
                <div class="content-card mb-4" data-aos="fade-up">
                    <h4><i class="fas fa-concierge-bell me-2"></i>Services Offered</h4>
                    <?php if (empty($services)): ?>
                        <p class="text-muted">No services listed yet.</p>
                    <?php else: ?>
                        <div class="services-list">
                            <?php foreach ($services as $service): ?>
                                <div class="service-item">
                                    <div class="service-info">
                                        <span class="category-tag"><?php echo htmlspecialchars($service['category_name']); ?></span>
                                        <h5><?php echo htmlspecialchars($service['name']); ?></h5>
                                        <p><?php echo htmlspecialchars($service['description'] ?: $service['service_desc']); ?></p>
                                    </div>
                                    <div class="service-price">
                                        <span class="price"><?php echo formatPrice($service['price']); ?></span>
                                        <a href="<?php echo BASE_URL; ?>book-service.php?provider=<?php echo $provider['id']; ?>&service=<?php echo $service['service_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">Book</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Reviews -->
                <div class="content-card" data-aos="fade-up">
                    <h4><i class="fas fa-star me-2"></i>Customer Reviews</h4>
                    <?php if (empty($reviews)): ?>
                        <p class="text-muted">No reviews yet. Be the first to review!</p>
                    <?php else: ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <img src="<?php echo UPLOADS_URL . ($review['profile_image'] ?: 'profiles/default-avatar.png'); ?>" 
                                             class="reviewer-avatar">
                                        <div>
                                            <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                            <div class="review-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <span class="review-date"><?php echo formatDate($review['created_at']); ?></span>
                                    </div>
                                    <p class="review-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.profile-card { background: var(--dark-800); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); overflow: hidden; }
.profile-header { position: relative; text-align: center; padding: 2rem 2rem 1rem; background: var(--gradient-primary); }
.profile-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; }
.verified-tag { position: absolute; top: 1rem; right: 1rem; background: var(--success); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.8rem; }
.profile-body { padding: 1.5rem; text-align: center; }
.profile-body h3 { margin-bottom: 0.25rem; }
.business-name { color: var(--primary-light); margin-bottom: 1rem; }
.rating-display { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1.5rem; }
.profile-stats { display: flex; justify-content: center; gap: 2rem; padding: 1rem 0; border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); margin-bottom: 1.5rem; }
.profile-stats .stat { text-align: center; }
.profile-stats .stat strong { display: block; font-size: 1.5rem; color: var(--primary-light); }
.profile-stats .stat span { font-size: 0.8rem; color: var(--light-300); }
.profile-info { margin-bottom: 1.5rem; }
.profile-info p { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin: 0.5rem 0; color: var(--light-300); }
.content-card { background: var(--dark-800); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 1.5rem; }
.content-card h4 { margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--glass-border); }
.service-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--glass-border); }
.service-item:last-child { border-bottom: none; }
.category-tag { background: rgba(var(--primary-rgb), 0.15); color: var(--primary-light); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.75rem; }
.service-info h5 { margin: 0.5rem 0 0.25rem; }
.service-info p { margin: 0; font-size: 0.875rem; color: var(--light-300); }
.service-price { text-align: right; }
.service-price .price { display: block; font-size: 1.25rem; font-weight: 700; color: var(--primary-light); margin-bottom: 0.5rem; }
.review-item { padding: 1rem 0; border-bottom: 1px solid var(--glass-border); }
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; }
.reviewer-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
.review-date { margin-left: auto; font-size: 0.8rem; color: var(--light-300); }
.review-stars i { font-size: 0.75rem; }
.review-text { margin: 0; color: var(--light-300); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
