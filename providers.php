<?php
/**
 * Service Providers Listing - Local Service Finder
 */
$pageTitle = 'Service Providers';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance()->getConnection();

// Get filter parameters
$categorySlug = sanitize($_GET['category'] ?? '');
$search = sanitize($_GET['q'] ?? '');
$location = sanitize($_GET['location'] ?? '');
$rating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;
$sortBy = sanitize($_GET['sort'] ?? 'rating');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Get categories
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();

// Build query
$where = ["pp.approval_status = 'approved'", "u.is_active = 1"];
$params = [];

if ($search) {
    $where[] = "(u.full_name LIKE ? OR pp.business_name LIKE ? OR pp.bio LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($location) {
    $where[] = "u.city LIKE ?";
    $params[] = "%$location%";
}

if ($rating > 0) {
    $where[] = "pp.avg_rating >= ?";
    $params[] = $rating;
}

if ($categorySlug) {
    $where[] = "EXISTS (SELECT 1 FROM provider_services ps JOIN services s ON ps.service_id = s.id JOIN categories c ON s.category_id = c.id WHERE ps.provider_id = pp.id AND c.slug = ?)";
    $params[] = $categorySlug;
}

$whereClause = implode(' AND ', $where);

// Sort
$orderBy = "pp.avg_rating DESC";
if ($sortBy === 'bookings') $orderBy = "pp.total_bookings DESC";
if ($sortBy === 'experience') $orderBy = "pp.experience_years DESC";
if ($sortBy === 'newest') $orderBy = "pp.created_at DESC";

// Get total count
$countQuery = "SELECT COUNT(*) FROM provider_profiles pp JOIN users u ON pp.user_id = u.id WHERE $whereClause";
$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get providers
$query = "
    SELECT pp.*, u.full_name, u.profile_image, u.city, u.phone
    FROM provider_profiles pp
    JOIN users u ON pp.user_id = u.id 
    WHERE $whereClause
    ORDER BY $orderBy 
    LIMIT $limit OFFSET $offset
";
$stmt = $db->prepare($query);
$stmt->execute($params);
$providers = $stmt->fetchAll();
?>

<section class="page-header">
    <div class="container">
        <h1>Service Providers</h1>
        <p>Find trusted professionals in your area</p>
    </div>
</section>

<section class="providers-section py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Filters Sidebar -->
            <div class="col-lg-3" data-aos="fade-right">
                <div class="filter-sidebar">
                    <h5><i class="fas fa-filter me-2"></i>Filters</h5>
                    <form method="GET" id="filterForm">
                        <div class="filter-group">
                            <label class="form-label">Search</label>
                            <input type="text" name="q" class="form-control" placeholder="Name or business..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="City..." value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['slug']; ?>" <?php echo $categorySlug === $cat['slug'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="form-label">Minimum Rating</label>
                            <select name="rating" class="form-select">
                                <option value="">Any Rating</option>
                                <option value="4" <?php echo $rating == 4 ? 'selected' : ''; ?>>4+ Stars</option>
                                <option value="3" <?php echo $rating == 3 ? 'selected' : ''; ?>>3+ Stars</option>
                                <option value="2" <?php echo $rating == 2 ? 'selected' : ''; ?>>2+ Stars</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="rating" <?php echo $sortBy === 'rating' ? 'selected' : ''; ?>>Top Rated</option>
                                <option value="bookings" <?php echo $sortBy === 'bookings' ? 'selected' : ''; ?>>Most Booked</option>
                                <option value="experience" <?php echo $sortBy === 'experience' ? 'selected' : ''; ?>>Most Experienced</option>
                                <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">Apply Filters</button>
                        <a href="providers.php" class="btn btn-outline-secondary w-100">Clear Filters</a>
                    </form>
                </div>
            </div>
            
            <!-- Providers Grid -->
            <div class="col-lg-9">
                <div class="results-header mb-4">
                    <span class="results-count"><strong><?php echo $totalItems; ?></strong> providers found</span>
                </div>
                
                <?php if (empty($providers)): ?>
                    <div class="no-results text-center py-5">
                        <i class="fas fa-user-slash fa-4x mb-3 text-muted"></i>
                        <h4>No Providers Found</h4>
                        <p class="text-muted">Try adjusting your filters</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($providers as $provider): ?>
                            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                                <div class="provider-card">
                                    <div class="provider-header">
                                        <img src="<?php echo UPLOADS_URL . ($provider['profile_image'] ?: 'profiles/default-avatar.png'); ?>" 
                                             class="provider-avatar" alt="<?php echo htmlspecialchars($provider['full_name']); ?>">
                                        <?php if ($provider['verified_badge']): ?>
                                            <span class="verified-badge" title="Verified"><i class="fas fa-check-circle"></i></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="provider-content">
                                        <h4><?php echo htmlspecialchars($provider['full_name']); ?></h4>
                                        <p class="provider-business"><?php echo htmlspecialchars($provider['business_name'] ?: 'Professional'); ?></p>
                                        
                                        <div class="provider-meta">
                                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($provider['city'] ?: 'Local'); ?></span>
                                            <span><i class="fas fa-briefcase"></i> <?php echo $provider['experience_years']; ?>+ years</span>
                                        </div>
                                        
                                        <div class="provider-rating">
                                            <div class="stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= round($provider['avg_rating']) ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="rating-value"><?php echo number_format($provider['avg_rating'], 1); ?></span>
                                            <span class="rating-count">(<?php echo $provider['total_reviews']; ?>)</span>
                                        </div>
                                        
                                        <div class="provider-stats">
                                            <span><i class="fas fa-calendar-check"></i> <?php echo $provider['total_bookings']; ?> jobs</span>
                                        </div>
                                    </div>
                                    <div class="provider-footer">
                                        <a href="provider-profile.php?id=<?php echo $provider['id']; ?>" class="btn btn-outline-primary">View Profile</a>
                                        <a href="book-service.php?provider=<?php echo $provider['id']; ?>" class="btn btn-primary">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-5">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.provider-card { background: var(--dark-800); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); overflow: hidden; transition: var(--transition-base); height: 100%; display: flex; flex-direction: column; }
.provider-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--primary); }
.provider-header { position: relative; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, var(--dark-700), var(--dark-800)); }
.provider-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); }
.verified-badge { position: absolute; top: 1rem; right: 1rem; color: var(--success); font-size: 1.25rem; }
.provider-content { padding: 1rem 1.5rem; flex-grow: 1; }
.provider-content h4 { margin-bottom: 0.25rem; font-size: 1.1rem; }
.provider-business { color: var(--primary-light); font-size: 0.9rem; margin-bottom: 0.75rem; }
.provider-meta { display: flex; gap: 1rem; font-size: 0.8rem; color: var(--light-300); margin-bottom: 0.75rem; flex-wrap: wrap; }
.provider-rating { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
.provider-rating .stars i { font-size: 0.75rem; }
.rating-value { font-weight: 600; }
.rating-count { font-size: 0.8rem; color: var(--light-300); }
.provider-stats { font-size: 0.85rem; color: var(--light-300); }
.provider-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--glass-border); display: flex; gap: 0.5rem; }
.provider-footer .btn { flex: 1; font-size: 0.85rem; padding: 0.5rem; }
.results-header { display: flex; justify-content: space-between; align-items: center; }
.results-count { color: var(--light-300); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
