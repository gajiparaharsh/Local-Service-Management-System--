<?php
/**
 * Book Service Page - Local Service Finder
 */
$pageTitle = 'Book Service';
require_once __DIR__ . '/includes/header.php';

// Require login
if (!isLoggedIn()) {
    flashMessage('error', 'Please login to book a service.', 'warning');
    redirect('auth/login.php');
}

$db = Database::getInstance()->getConnection();

$providerId = intval($_GET['provider'] ?? 0);
$serviceId = intval($_GET['service'] ?? 0);

// Get provider info
$stmt = $db->prepare("
    SELECT pp.*, u.full_name, u.email, u.phone, u.profile_image, u.city
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

// Get provider services
$stmt = $db->prepare("
    SELECT ps.*, s.name, s.slug, c.name as category_name
    FROM provider_services ps
    JOIN services s ON ps.service_id = s.id
    JOIN categories c ON s.category_id = c.id
    WHERE ps.provider_id = ? AND ps.is_active = 1
    ORDER BY s.name
");
$stmt->execute([$providerId]);
$providerServices = $stmt->fetchAll();

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = intval($_POST['service_id'] ?? 0);
    $bookingDate = sanitize($_POST['booking_date'] ?? '');
    $bookingTime = sanitize($_POST['booking_time'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    
    // Validation
    if (!$serviceId) $errors['service'] = 'Please select a service.';
    if (empty($bookingDate)) $errors['date'] = 'Please select a date.';
    if (empty($bookingTime)) $errors['time'] = 'Please select a time.';
    if (empty($address)) $errors['address'] = 'Please enter your address.';
    
    // Check date is in future
    if ($bookingDate && strtotime($bookingDate) < strtotime('today')) {
        $errors['date'] = 'Please select a future date.';
    }
    
    if (empty($errors)) {
        // Get service price
        $stmt = $db->prepare("SELECT price FROM provider_services WHERE provider_id = ? AND service_id = ?");
        $stmt->execute([$providerId, $serviceId]);
        $serviceInfo = $stmt->fetch();
        $price = $serviceInfo['price'] ?? 0;
        
        // Generate booking number
        $bookingNumber = generateBookingNumber();
        
        // Create booking
        $stmt = $db->prepare("
            INSERT INTO bookings (booking_number, user_id, provider_id, service_id, booking_date, booking_time, 
                                  address, city, pincode, notes, price, final_amount, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $result = $stmt->execute([
            $bookingNumber,
            getCurrentUserId(),
            $providerId,
            $serviceId,
            $bookingDate,
            $bookingTime,
            $address,
            $city,
            $pincode,
            $notes,
            $price,
            $price
        ]);
        
        if ($result) {
            // Create notification
            $stmt = $db->prepare("
                INSERT INTO notifications (user_id, title, message, type, created_at)
                VALUES (?, 'Booking Confirmed', ?, 'booking', NOW())
            ");
            $stmt->execute([
                getCurrentUserId(),
                "Your booking #$bookingNumber has been placed successfully!"
            ]);
            
            flashMessage('success', 'Booking placed successfully! Booking Number: ' . $bookingNumber, 'success');
            redirect('user/bookings.php');
        } else {
            $errors['general'] = 'Failed to create booking. Please try again.';
        }
    }
}
?>

<section class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>providers.php">Providers</a></li>
                <li class="breadcrumb-item active">Book Service</li>
            </ol>
        </nav>
        <h1>Book a Service</h1>
        <p>Complete the form below to book your service</p>
    </div>
</section>

<section class="booking-section py-5">
    <div class="container">
        <?php displayFlashMessage('error'); ?>
        
        <div class="row g-4">
            <!-- Provider Info Card -->
            <div class="col-lg-4" data-aos="fade-right">
                <div class="card provider-info-card">
                    <div class="card-body text-center">
                        <img src="<?php echo UPLOADS_URL . ($provider['profile_image'] ?: 'profiles/default-avatar.png'); ?>" 
                             class="provider-img mb-3" alt="Provider">
                        <h4><?php echo htmlspecialchars($provider['full_name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($provider['business_name']); ?></p>
                        
                        <div class="provider-stats-mini">
                            <div class="stat">
                                <i class="fas fa-star text-warning"></i>
                                <span><?php echo number_format($provider['avg_rating'], 1); ?></span>
                            </div>
                            <div class="stat">
                                <i class="fas fa-briefcase"></i>
                                <span><?php echo $provider['experience_years']; ?>+ yrs</span>
                            </div>
                            <div class="stat">
                                <i class="fas fa-check-circle text-success"></i>
                                <span><?php echo $provider['total_bookings']; ?> jobs</span>
                            </div>
                        </div>
                        
                        <hr>
                        <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($provider['city']); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Booking Form -->
            <div class="col-lg-8" data-aos="fade-left">
                <div class="card booking-form-card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-check me-2"></i>Booking Details</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" id="bookingForm">
                            <div class="row g-3">
                                <!-- Service Selection -->
                                <div class="col-12">
                                    <label class="form-label">Select Service *</label>
                                    <select name="service_id" class="form-select <?php echo isset($errors['service']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">Choose a service...</option>
                                        <?php foreach ($providerServices as $service): ?>
                                            <option value="<?php echo $service['service_id']; ?>" 
                                                    data-price="<?php echo $service['price']; ?>"
                                                    <?php echo $serviceId == $service['service_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($service['name']); ?> - <?php echo formatPrice($service['price']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['service'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['service']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Date & Time -->
                                <div class="col-md-6">
                                    <label class="form-label">Date *</label>
                                    <input type="date" name="booking_date" class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                    <?php if (isset($errors['date'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['date']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Time *</label>
                                    <select name="booking_time" class="form-select <?php echo isset($errors['time']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">Select time...</option>
                                        <option value="09:00">09:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="14:00">02:00 PM</option>
                                        <option value="15:00">03:00 PM</option>
                                        <option value="16:00">04:00 PM</option>
                                        <option value="17:00">05:00 PM</option>
                                        <option value="18:00">06:00 PM</option>
                                    </select>
                                </div>
                                
                                <!-- Address -->
                                <div class="col-12">
                                    <label class="form-label">Service Address *</label>
                                    <textarea name="address" class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" 
                                              rows="2" placeholder="Enter complete address" required></textarea>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" placeholder="City">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control" placeholder="Pincode" maxlength="6">
                                </div>
                                
                                <!-- Notes -->
                                <div class="col-12">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" 
                                              placeholder="Any special instructions or requirements..."></textarea>
                                </div>
                                
                                <!-- Price Summary -->
                                <div class="col-12">
                                    <div class="price-summary">
                                        <div class="d-flex justify-content-between">
                                            <span>Service Charge:</span>
                                            <span id="servicePrice">₹0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between total">
                                            <strong>Total Amount:</strong>
                                            <strong id="totalAmount">₹0.00</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-check me-2"></i>Confirm Booking
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.provider-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); }
.provider-stats-mini { display: flex; justify-content: center; gap: 1.5rem; padding: 1rem 0; }
.provider-stats-mini .stat { display: flex; align-items: center; gap: 0.5rem; }
.price-summary { background: var(--dark-700); padding: 1.5rem; border-radius: var(--radius-lg); }
.price-summary .total { font-size: 1.25rem; color: var(--primary-light); }
</style>

<script>
document.querySelector('select[name="service_id"]').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const price = parseFloat(selected.dataset.price) || 0;
    document.getElementById('servicePrice').textContent = '₹' + price.toFixed(2);
    document.getElementById('totalAmount').textContent = '₹' + price.toFixed(2);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
