<?php include 'header.php'; ?>

<div class="page-header">
    <div class="container" style="position: relative;">
        <h1>Our Rooms & Suites</h1>
        <p>Discover your perfect sanctuary</p>
    </div>
</div>

<!-- Family Deal Banner -->
<section id="family" class="family-deal-banner">
    <div class="container">
        <div class="row align-items-center family-deal-content">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="mb-2"><i class="fas fa-users me-2"></i> Family Packages Available!</h2>
                <p class="mb-0 opacity-90">Enjoy our special family rooms with complimentary breakfast, kids club access, connecting rooms, and more! Perfect for creating unforgettable memories together.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-danger fs-5 p-3">Save up to 20%</span>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php
        $conn = getDBConnection();
        $room_types = ['Family', 'Suite', 'Deluxe', 'Standard'];
        
        foreach ($room_types as $type):
            $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_type = ? ORDER BY price DESC");
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0):
                $isFamily = $type === 'Family';
        ?>
        
        <div class="mb-5" <?php if ($isFamily) echo 'id="family-rooms"'; ?>>
            <div class="d-flex align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <?php if ($isFamily): ?>
                        <i class="fas fa-heart text-danger me-2"></i>
                    <?php endif; ?>
                    <?php echo $type; ?> Rooms
                </h2>
                <?php if ($isFamily): ?>
                    <span class="badge bg-danger ms-3 p-2">Family Deal</span>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <?php while ($room = $result->fetch_assoc()): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card room-detail-card">
                        <div class="row g-0">
                            <div class="col-md-5">
                                <div class="room-detail-image h-100">
                                    <?php if ($room['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                                    <?php else: ?>
                                        <?php 
                                        $imgUrls = [
                                            'family' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600',
                                            'suite' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600',
                                            'deluxe' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600',
                                            'standard' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600'
                                        ];
                                        $imgUrl = $imgUrls[strtolower($type)] ?? $imgUrls['standard'];
                                        ?>
                                        <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h4 class="card-title mb-1"><?php echo htmlspecialchars($room['room_name']); ?></h4>
                                            <span class="badge room-badge <?php echo $isFamily ? 'family' : ''; ?>"><?php echo htmlspecialchars($room['room_type']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="my-3">
                                        <div class="room-price-large">$<?php echo number_format($room['price'], 0); ?></div>
                                        <small class="text-muted">per night</small>
                                    </div>
                                    
                                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($room['description']); ?></p>
                                    
                                    <div class="room-amenities mb-3">
                                        <?php
                                        $amenities = explode(',', $room['amenities']);
                                        $count = 0;
                                        foreach ($amenities as $amenity):
                                            if ($count < 4):
                                        ?>
                                        <span class="amenity-badge"><?php echo trim(htmlspecialchars($amenity)); ?></span>
                                        <?php 
                                            $count++;
                                            endif;
                                        endforeach; 
                                        if (count($amenities) > 4):
                                        ?>
                                        <span class="amenity-badge">+<?php echo count($amenities) - 4; ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small"><i class="fas fa-users me-1"></i> Up to <?php echo $room['capacity']; ?> guests</span>
                                        <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="btn <?php echo $isFamily ? 'btn-danger' : 'btn-primary'; ?> btn-sm">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <?php
            endif;
            $stmt->close();
        endforeach;
        $conn->close();
        ?>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding bg-light-gradient">
    <div class="container">
        <div class="cta-box">
            <h2 class="mb-3">Need Help Choosing?</h2>
            <p class="mb-4 opacity-90">Our concierge team is ready to help you find the perfect room for your stay</p>
            <div style="position: relative; z-index: 2;">
                <a href="tel:+1234567890" class="btn btn-gold btn-lg me-3"><i class="fas fa-phone me-2"></i>Call Us</a>
                <a href="mailto:info@grandaurora.com" class="btn btn-outline-light btn-lg"><i class="fas fa-envelope me-2"></i>Email Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
