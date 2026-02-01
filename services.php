<?php
/**
 * Services Listing Page - Local Service Finder
 */
$pageTitle = 'Our Services';
require_once __DIR__ . '/includes/header.php';

$db = Database::getInstance()->getConnection();

// Get filter parameters
$categorySlug = sanitize($_GET['category'] ?? '');
$search = sanitize($_GET['q'] ?? '');
$priceMin = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;
$page = max(1, intval($_GET['page'] ?? 1));
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Get all categories for filter
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();

// Build query
$where = ["s.is_active = 1"];
$params = [];

if ($categorySlug) {
    $where[] = "c.slug = ?";
    $params[] = $categorySlug;
}

if ($search) {
    $where[] = "(s.name LIKE ? OR s.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($priceMin > 0) {
    $where[] = "s.base_price >= ?";
    $params[] = $priceMin;
}

if ($priceMax > 0) {
    $where[] = "s.base_price <= ?";
    $params[] = $priceMax;
}

$whereClause = implode(' AND ', $where);

// Get total count
$countQuery = "SELECT COUNT(*) FROM services s LEFT JOIN categories c ON s.category_id = c.id WHERE $whereClause";
$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Get services
$query = "
    SELECT s.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
           (SELECT COUNT(*) FROM provider_services ps WHERE ps.service_id = s.id) as provider_count
    FROM services s
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE $whereClause
    ORDER BY s.name ASC
    LIMIT $limit OFFSET $offset
";
$stmt = $db->prepare($query);
$stmt->execute($params);
$services = $stmt->fetchAll();

// Get current category info
$currentCategory = null;
if ($categorySlug) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug) {
            $currentCategory = $cat;
            break;
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Services</li>
                <?php if ($currentCategory): ?>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($currentCategory['name']); ?></li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1><?php echo $currentCategory ? htmlspecialchars($currentCategory['name']) : 'Our Services'; ?></h1>
        <p><?php echo $currentCategory ? htmlspecialchars($currentCategory['description']) : 'Browse our wide range of professional services'; ?></p>
    </div>
</section>

<section class="services-page py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="filter-sidebar" data-aos="fade-right">
                    <div class="filter-card">
                        <h5 class="filter-title">
                            <i class="fas fa-filter me-2"></i>Filter Services
                        </h5>
                        
                        <form action="" method="GET" id="filterForm">
                            <!-- Search -->
                            <div class="filter-group">
                                <label class="filter-label">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="q" class="form-control" placeholder="Search services..." 
                                           value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            
                            <!-- Categories -->
                            <div class="filter-group">
                                <label class="filter-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['slug']; ?>" <?php echo $categorySlug === $cat['slug'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Price Range -->
                            <div class="filter-group">
                                <label class="filter-label">Price Range</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="price_min" class="form-control" placeholder="Min" 
                                               value="<?php echo $priceMin > 0 ? $priceMin : ''; ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="price_max" class="form-control" placeholder="Max" 
                                               value="<?php echo $priceMax > 0 ? $priceMax : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Apply Filters
                                </button>
                                <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Categories Quick Links -->
                    <div class="filter-card mt-4">
                        <h5 class="filter-title">
                            <i class="fas fa-th-large me-2"></i>Categories
                        </h5>
                        <ul class="category-list">
                            <?php foreach ($categories as $cat): 
                                $serviceCount = $db->query("SELECT COUNT(*) FROM services WHERE category_id = {$cat['id']} AND is_active = 1")->fetchColumn();
                            ?>
                                <li>
                                    <a href="?category=<?php echo $cat['slug']; ?>" class="<?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                                        <i class="fas <?php echo $cat['icon']; ?>"></i>
                                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                        <span class="count"><?php echo $serviceCount; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Services Grid -->
            <div class="col-lg-9">
                <div class="services-header mb-4" data-aos="fade-up">
                    <div class="results-info">
                        <p>Showing <strong><?php echo count($services); ?></strong> of <strong><?php echo $totalItems; ?></strong> services</p>
                    </div>
                    <div class="sort-options">
                        <select class="form-select form-select-sm" id="sortSelect">
                            <option value="name">Sort by Name</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>
                
                <?php if (empty($services)): ?>
                    <div class="no-results" data-aos="fade-up">
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No Services Found</h3>
                        <p>We couldn't find any services matching your criteria. Try adjusting your filters.</p>
                        <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>Reset Filters
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($services as $index => $service): ?>
                            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo ($index % 6) * 50; ?>">
                                <div class="service-card">
                                    <div class="service-icon">
                                        <i class="fas <?php echo $service['category_icon'] ?: 'fa-cog'; ?>"></i>
                                    </div>
                                    <span class="service-category"><?php echo htmlspecialchars($service['category_name']); ?></span>
                                    <h4><?php echo htmlspecialchars($service['name']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($service['description'], 0, 80)); ?>...</p>
                                    <div class="service-meta">
                                        <div class="service-price">
                                            <span class="price-label">Starting from</span>
                                            <span class="price-value"><?php echo formatPrice($service['base_price']); ?></span>
                                        </div>
                                        <div class="service-providers">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo $service['provider_count']; ?> Providers</span>
                                        </div>
                                    </div>
                                    <div class="service-actions">
                                        <a href="<?php echo BASE_URL; ?>providers.php?service=<?php echo $service['slug']; ?>" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i>Find Providers
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-5" data-aos="fade-up">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Page Header */
.page-header {
    background: var(--gradient-primary);
    padding: 4rem 0 3rem;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.page-header .breadcrumb {
    margin-bottom: 1rem;
    background: transparent;
    padding: 0;
}

.page-header .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
}

.page-header .breadcrumb-item.active {
    color: white;
}

.page-header .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.5);
}

.page-header h1 {
    color: white;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
}

/* Filter Sidebar */
.filter-card {
    background: var(--dark-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
}

.filter-title {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--glass-border);
}

.filter-group {
    margin-bottom: 1.25rem;
}

.filter-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--light);
}

.filter-actions {
    margin-top: 1.5rem;
}

/* Category List */
.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-list li {
    margin-bottom: 0.5rem;
}

.category-list a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--dark-700);
    border-radius: var(--radius-md);
    color: var(--light-300);
    text-decoration: none;
    transition: var(--transition-base);
}

.category-list a:hover,
.category-list a.active {
    background: rgba(var(--primary-rgb), 0.15);
    color: var(--primary-light);
}

.category-list a i {
    width: 24px;
    text-align: center;
}

.category-list a span {
    flex: 1;
}

.category-list a .count {
    background: var(--dark-600);
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
}

/* Services Header */
.services-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.results-info p {
    margin: 0;
    color: var(--light-300);
}

.sort-options .form-select {
    width: auto;
}

/* Service Card */
.service-card {
    background: var(--dark-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: var(--transition-base);
}

.service-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    box-shadow: var(--shadow-glow);
}

.service-icon {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
}

.service-category {
    display: inline-block;
    background: rgba(var(--primary-rgb), 0.15);
    color: var(--primary-light);
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.service-card h4 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.service-card p {
    font-size: 0.875rem;
    margin-bottom: 1rem;
    flex: 1;
}

.service-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-top: 1px solid var(--glass-border);
    margin-bottom: 1rem;
}

.price-label {
    display: block;
    font-size: 0.75rem;
    color: var(--light-300);
}

.price-value {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-light);
}

.service-providers {
    font-size: 0.875rem;
    color: var(--light-300);
}

.service-providers i {
    margin-right: 0.25rem;
    color: var(--primary-light);
}

/* No Results */
.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--dark-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
}

.no-results-icon {
    width: 80px;
    height: 80px;
    background: var(--dark-700);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--dark-500);
    margin: 0 auto 1.5rem;
}

.no-results h3 {
    margin-bottom: 0.5rem;
}

.no-results p {
    color: var(--light-300);
    margin-bottom: 1.5rem;
}

@media (max-width: 991.98px) {
    .filter-sidebar {
        margin-bottom: 2rem;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
