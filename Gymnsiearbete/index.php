<?php include 'header.php'; ?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-bg-image"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-badge">5-Star Luxury Experience</span>
        <h1 class="hero-title">Grand Aurora</h1>
        <p class="hero-subtitle">Where Luxury Meets Tranquility</p>
        <div class="hero-buttons">
            <a href="rooms.php" class="btn btn-gold btn-lg me-3">Explore Rooms</a>
            <a href="register.php" class="btn btn-outline-light btn-lg">Get Started</a>
        </div>
    </div>
    <div class="hero-scroll">
        <a href="#about"><i class="fas fa-chevron-down"></i></a>
    </div>
</div>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Luxury Rooms</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Happy Guests</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Guest Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800" alt="Grand Aurora Lobby">
                    <div class="about-badge">
                        <h3>25+</h3>
                        <p>Years of Excellence</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <h2 class="section-title">Experience Unparalleled Elegance</h2>
                <p class="text-muted mb-4">Grand Aurora redefines luxury hospitality with its modern metallic chic aesthetic and world-class amenities. Nestled in the heart of the city, our hotel offers a serene escape from the ordinary.</p>
                <p class="text-muted mb-4">Each space is thoughtfully designed to provide comfort, sophistication, and a sense of calm. From our elegantly appointed rooms to our personalized service, every detail reflects our commitment to excellence.</p>
                
                <div class="row mt-4">
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Premium Amenities</span>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>24/7 Room Service</span>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Spa & Wellness</span>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Fine Dining</span>
                        </div>
                    </div>
                </div>
                
                <a href="rooms.php" class="btn btn-primary mt-3">Discover More</a>
            </div>
        </div>
    </div>
</section>

<!-- Family Deal Banner -->
<section class="family-deal-banner">
    <div class="container">
        <div class="row align-items-center family-deal-content">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="mb-2">Special Family Packages!</h2>
                <p class="mb-0 opacity-90">Book our exclusive family rooms and enjoy complimentary breakfast, kids club access, and more. Perfect for creating unforgettable memories together.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="rooms.php#family" class="btn btn-light btn-lg">View Family Deals</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Rooms -->
<section class="section-padding bg-light-gradient">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-center">Featured Accommodations</h2>
            <p class="section-subtitle mx-auto">Choose from our carefully curated selection of rooms and suites designed for ultimate comfort</p>
        </div>
        
        <div class="row">
            <?php
            $conn = getDBConnection();
            $result = $conn->query("SELECT * FROM rooms ORDER BY price ASC LIMIT 6");
            
            while ($room = $result->fetch_assoc()):
                $isFamily = $room['room_type'] === 'Family';
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card room-card">
                    <div class="room-card-image">
                        <?php if ($room['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                        <?php endif; ?>
                        <span class="room-card-badge <?php echo $isFamily ? 'family-deal' : ''; ?>">
                            <?php echo $isFamily ? 'Family Deal' : htmlspecialchars($room['room_type']); ?>
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <p class="room-type"><?php echo htmlspecialchars($room['room_type']); ?></p>
                        <h5 class="card-title mb-3"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($room['description'], 0, 80)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="room-price">$<?php echo number_format($room['price'], 0); ?> <span>/night</span></div>
                            <a href="rooms.php" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php $conn->close(); ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="rooms.php" class="btn btn-outline-primary btn-lg">View All Rooms</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-center">Why Choose Grand Aurora</h2>
            <p class="section-subtitle mx-auto">Experience the finest in luxury hospitality with our exceptional services and amenities</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h4>24/7 Concierge</h4>
                    <p class="text-muted">Our dedicated team is available around the clock to cater to your every need and ensure a seamless stay.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h4>Spa & Wellness</h4>
                    <p class="text-muted">Rejuvenate your body and mind at our world-class spa with premium treatments and therapies.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4>Fine Dining</h4>
                    <p class="text-muted">Savor exquisite cuisines prepared by award-winning chefs at our signature restaurants.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-swimming-pool"></i>
                    </div>
                    <h4>Infinity Pool</h4>
                    <p class="text-muted">Take a refreshing dip in our stunning rooftop infinity pool with panoramic city views.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h4>Fitness Center</h4>
                    <p class="text-muted">Stay active with state-of-the-art equipment and personal training sessions.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h4>Kids Club</h4>
                    <p class="text-muted">A fun and safe environment for children with supervised activities and entertainment.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="section-padding bg-light-gradient">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-center">Hotel Gallery</h2>
            <p class="section-subtitle mx-auto">Take a glimpse into the luxurious world of Grand Aurora</p>
        </div>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600" alt="Hotel Exterior">
                <div class="gallery-overlay">
                    <h5>Hotel Exterior</h5>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600" alt="Hotel Lobby">
                <div class="gallery-overlay">
                    <h5>Grand Lobby</h5>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600" alt="Presidential Suite">
                <div class="gallery-overlay">
                    <h5>Presidential Suite</h5>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600" alt="Deluxe Room">
                <div class="gallery-overlay">
                    <h5>Deluxe Room</h5>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600" alt="Standard Room">
                <div class="gallery-overlay">
                    <h5>Standard Room</h5>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600" alt="Family Suite">
                <div class="gallery-overlay">
                    <h5>Family Suite</h5>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section-padding">
    <div class="container">
        <div class="cta-box">
            <h2 class="mb-3">Ready to Experience Grand Aurora?</h2>
            <p class="mb-4 opacity-90">Join us and discover a new standard of luxury hospitality</p>
            <div style="position: relative; z-index: 2;">
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-gold btn-lg me-3">Go to Dashboard</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-gold btn-lg me-3">Create Account</a>
                    <a href="login.php" class="btn btn-outline-light btn-lg">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Scroll to Top Button -->
<div class="scroll-top" id="scrollTop">
    <i class="fas fa-arrow-up"></i>
</div>

<script>
// Scroll to top functionality
window.addEventListener('scroll', function() {
    const scrollTop = document.getElementById('scrollTop');
    if (window.pageYOffset > 300) {
        scrollTop.classList.add('visible');
    } else {
        scrollTop.classList.remove('visible');
    }
});

document.getElementById('scrollTop').addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.pageYOffset > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
</script>

<?php include 'footer.php'; ?>
