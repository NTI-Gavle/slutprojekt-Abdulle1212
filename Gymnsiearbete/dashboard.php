<?php
include 'config.php';

// Redirect to login if not authenticated
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();

// Fetch user's bookings
$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT b.*, r.room_name, r.room_type, r.price 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result();

include 'header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
        <p>Manage your bookings and account</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-bed fa-3x mb-3 text-primary"></i>
                        <h5>Book a Room</h5>
                        <p class="text-muted mb-3">Browse and reserve your perfect room</p>
                        <a href="rooms.php" class="btn btn-primary">View Rooms</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-3x mb-3 text-success"></i>
                        <h5>My Bookings</h5>
                        <p class="text-muted mb-3">View your current and past reservations</p>
                        <a href="#bookings" class="btn btn-outline-primary">View Below</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-user fa-3x mb-3 text-info"></i>
                        <h5>Account</h5>
                        <p class="text-muted mb-3">Manage your profile and settings</p>
                        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="bookings">
            <h2 class="section-title mb-4">Your Bookings</h2>
            
            <?php if ($bookings->num_rows > 0): ?>
            <div class="row">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card booking-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($booking['room_name']); ?></h5>
                                    <span class="badge room-badge"><?php echo htmlspecialchars($booking['room_type']); ?></span>
                                </div>
                                <span class="badge bg-success"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></span>
                            </div>
                            
                            <div class="booking-details">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Check-in:</span>
                                    <strong><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Check-out:</span>
                                    <strong><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fas fa-moon me-2"></i>Nights:</span>
                                    <strong>
                                        <?php
                                        $checkin = new DateTime($booking['check_in']);
                                        $checkout = new DateTime($booking['check_out']);
                                        $nights = $checkout->diff($checkin)->days;
                                        echo $nights;
                                        ?>
                                    </strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Price:</span>
                                    <h5 class="mb-0 text-primary">$<?php echo number_format($booking['total_price'], 2); ?></h5>
                                </div>
                            </div>
                            
                            <div class="text-muted mt-3">
                                <small><i class="fas fa-clock me-2"></i>Booked on <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h4>No Bookings Yet</h4>
                <p class="text-muted mb-4">Start your journey by booking your first room</p>
                <a href="rooms.php" class="btn btn-primary">Browse Rooms</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$stmt->close();
$conn->close();
include 'footer.php';
?>
