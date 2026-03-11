<?php
include 'config.php';

// Redirect to login if not authenticated
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$room = null;

// Get room details
if (isset($_GET['room_id'])) {
    $room_id = intval($_GET['room_id']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $room = $result->fetch_assoc();
    } else {
        $error = 'Room not found.';
    }
    $stmt->close();
    $conn->close();
} else {
    header('Location: rooms.php');
    exit();
}

// Process booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $room) {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    // Validation
    if (empty($check_in) || empty($check_out)) {
        $error = 'Please select check-in and check-out dates.';
    } else {
        $checkin_date = new DateTime($check_in);
        $checkout_date = new DateTime($check_out);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if ($checkin_date < $today) {
            $error = 'Check-in date cannot be in the past.';
        } elseif ($checkout_date <= $checkin_date) {
            $error = 'Check-out date must be after check-in date.';
        } else {
            $nights = $checkout_date->diff($checkin_date)->days;
            $total_price = $nights * $room['price'];
            
            $conn = getDBConnection();
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iissd", $_SESSION['user_id'], $room['id'], $check_in, $check_out, $total_price);
            
            if ($stmt->execute()) {
                $success = 'Booking confirmed successfully!';
            } else {
                $error = 'Booking failed. Please try again.';
            }
            
            $stmt->close();
            $conn->close();
        }
    }
}

include 'header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Complete Your Booking</h1>
        <p>Reserve your luxury experience</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <?php if ($error && !$room): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
            <a href="rooms.php" class="alert-link">Return to Rooms</a>
        </div>
        <?php elseif ($room): ?>
        
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Booking Details</h3>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <a href="dashboard.php" class="alert-link">View your bookings</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="booking.php?room_id=<?php echo $room['id']; ?>" id="bookingForm">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="check_in" class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="check_out" class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control" id="check_out" name="check_out" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                </div>
                            </div>
                            
                            <div id="booking-summary" class="alert alert-info" style="display: none;">
                                <h6>Booking Summary</h6>
                                <div class="d-flex justify-content-between">
                                    <span>Number of nights:</span>
                                    <strong id="nights-count">0</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Price per night:</span>
                                    <strong>$<?php echo number_format($room['price'], 2); ?></strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h6">Total Price:</span>
                                    <strong class="h6 text-primary" id="total-price">$0.00</strong>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                        <span class="badge room-badge mb-3"><?php echo htmlspecialchars($room['room_type']); ?></span>
                        
                        <p class="text-muted"><?php echo htmlspecialchars($room['description']); ?></p>
                        
                        <div class="mb-3">
                            <h6>Amenities:</h6>
                            <?php
                            $amenities = explode(',', $room['amenities']);
                            foreach ($amenities as $amenity):
                            ?>
                            <span class="amenity-badge"><?php echo trim(htmlspecialchars($amenity)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Price per night:</span>
                            <h4 class="mb-0 text-primary">$<?php echo number_format($room['price'], 2); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<script>
// Calculate booking price
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const pricePerNight = <?php echo $room ? $room['price'] : 0; ?>;
    
    function calculatePrice() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (checkInInput.value && checkOutInput.value && checkOut > checkIn) {
            const nights = Math.floor((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const total = nights * pricePerNight;
            
            document.getElementById('nights-count').textContent = nights;
            document.getElementById('total-price').textContent = '$' + total.toFixed(2);
            document.getElementById('booking-summary').style.display = 'block';
        } else {
            document.getElementById('booking-summary').style.display = 'none';
        }
    }
    
    checkInInput.addEventListener('change', calculatePrice);
    checkOutInput.addEventListener('change', calculatePrice);
});
</script>

<?php include 'footer.php'; ?>
