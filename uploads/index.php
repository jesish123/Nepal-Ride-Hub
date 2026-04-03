<<<<<<< HEAD
<?php 
include 'includes/header.php'; 
=======
<?php
include 'includes/header.php';
>>>>>>> origin/seraj
require_once 'includes/db_connect.php';

try {
    $stmtVehicles = $pdo->query("SELECT * FROM vehicles WHERE status = 'available' ORDER BY created_at DESC LIMIT 3");
    $featuredVehicles = $stmtVehicles->fetchAll();
} catch (PDOException $e) {
    $featuredVehicles = [];
}
?>
<<<<<<< HEAD
=======

<style>
    /* Reset and Base Overrides for Mockup */
    .hero-wrapper {
        min-height: 85vh;
        background: #fff;
        position: relative;
        padding: 0;
        overflow: hidden;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.9) 30%, rgba(255, 255, 255, 0) 70%),
            url('https://images.unsplash.com/photo-1542242476-5a3565835a38?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') center/cover no-repeat;
        z-index: 1;
    }

    .hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6rem 0 10rem 0;
        position: relative;
        z-index: 2;
    }

    .hero-left {
        flex: 1;
        max-width: 550px;
    }

    .hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: 4rem;
        font-weight: 800;
        color: #111;
        line-height: 1.1;
        margin-bottom: 2rem;
    }

    .hero-desc {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        color: #444;
        line-height: 1.7;
        margin-bottom: 2.5rem;
    }

    .hero-features {
        list-style: none;
        padding: 0;
        margin-bottom: 3rem;
    }

    .hero-features li {
        font-family: 'Inter', sans-serif;
        font-size: 1.05rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .hero-features i {
        color: #28a745;
        font-size: 1.1rem;
    }

    .hero-right {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        position: relative;
    }

    .hero-carousel {
        width: 100%;
        max-width: 600px;
        height: 380px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
    }

    .hero-carousel img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: #333;
        z-index: 10;
    }

    .carousel-prev {
        left: 20px;
    }

    .carousel-next {
        right: 20px;
    }

    /* Booking Steps Redesign */
    .booking-steps-wrapper {
        position: relative;
        z-index: 20;
        margin-top: -100px;
        padding-bottom: 5rem;
    }

    .booking-steps-container {
        display: flex;
        justify-content: space-between;
        background: #fff;
        padding: 3.5rem 3rem;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        position: relative;
    }

    .booking-steps-container::before {
        content: '';
        position: absolute;
        top: calc(3.5rem + 25px);
        left: 80px;
        right: 80px;
        height: 2px;
        background: #3561ff;
        opacity: 0.2;
        z-index: 1;
    }

    .step-item {
        flex: 1;
        text-align: left;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }

    .step-number {
        width: 50px;
        height: 50px;
        background: #3561ff;
        color: #fff;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(53, 97, 255, 0.3);
    }

    .step-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.3rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 0.8rem;
    }

    .step-desc {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        color: #666;
        line-height: 1.6;
    }
</style>

<div class="hero-wrapper">
    <div class="hero-bg"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-left">
                <h1 class="hero-title">Welcome to<br>Nepal Ride Hub</h1>
                <p class="hero-desc">Nepal Ride Hub provides reliable vehicle rental services across Nepal. Whether you
                    need a bike for adventure, a car for comfort, or a bus for group travel, we have everything for your
                    journey.</p>
                <ul class="hero-features">
                    <li><i class="fa-solid fa-check"></i> Easy Booking System</li>
                    <li><i class="fa-solid fa-check"></i> Affordable Pricing</li>
                    <li><i class="fa-solid fa-check"></i> GPS Tracking Available</li>
                    <li><i class="fa-solid fa-check"></i> Wide Range of Vehicles</li>
                </ul>
                <a href="vehicles.php" class="btn-blue-solid"
                    style="padding: 1.2rem 2.8rem; border-radius: 12px; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(53,97,255,0.3);">Explore
                    Vehicles</a>
            </div>
            <div class="hero-right">
                <div class="hero-carousel">
                    <button class="carousel-btn carousel-prev"><i class="fa-solid fa-chevron-left"></i></button>
                    <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=800&q=80"
                        alt="Featured Car">
                    <button class="carousel-btn carousel-next"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="booking-steps-wrapper">
    <div class="container">
        <div class="booking-steps-container">
            <div class="step-item">
                <div class="step-number">1</div>
                <h3 class="step-title">Choose Your Favorite Vehicle</h3>
                <p class="step-desc">Select your preferred vehicle, tailored to your journey as per your requirement.
                </p>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <h3 class="step-title">Make a Booking</h3>
                <p class="step-desc">You can make easy bookings through our user-friendly app or a simple phone call.
                </p>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <h3 class="step-title">Pick-Up Location & Date</h3>
                <p class="step-desc">Select your nearest location with the date and time for your journey.</p>
            </div>
            <div class="step-item">
                <div class="step-number">4</div>
                <h3 class="step-title">Sit Back & Relax</h3>
                <p class="step-desc">Sit back, relax, and let your safe and convenient journey begin with Spark Car.</p>
            </div>
        </div>
    </div>
</div>

<section style="padding: 4rem 0; background: var(--light-bg);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Featured Vehicles</h2>
            <a href="vehicles.php" class="btn btn-outline">View All Vehicles</a>
        </div>

        <?php if (empty($featuredVehicles)): ?>
            <p style="text-align:center; color: var(--gray-text);">Check back soon for our premium fleet.</p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                <?php foreach ($featuredVehicles as $v): ?>
                    <div
                        style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow); display: flex; flex-direction: column;">
                        <div style="height: 200px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars($v['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($v['name']); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <h3><?php echo htmlspecialchars($v['name']); ?></h3>
                                <div>
                                    <span
                                        style="background: var(--light-bg); padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid var(--border-color); margin-right: 0.2rem;">
                                        <?php echo ucfirst($v['type']); ?>
                                    </span>
                                    <span
                                        style="background: #eef2f3; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid #ced4da; color: #495057;">
                                        <i class="fas fa-route"></i> <?php echo ucfirst($v['condition_type'] ?? 'city'); ?>
                                    </span>
                                </div>
                            </div>
                            <p style="color: var(--gray-text); margin-bottom: 1rem; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($v['brand']); ?> &bull; <?php echo $v['model_year']; ?>
                            </p>
                            <div style="margin-bottom: 1.5rem;">
                                <span style="font-size: 1.4rem; font-weight: 700; color: var(--primary-red);">Rs.
                                    <?php echo $v['price_per_day']; ?></span>
                                <span style="color: var(--gray-text); font-size: 0.9rem;">/ day</span>
                            </div>
                            <div style="margin-top: auto;">
                                <a href="vehicle_details.php?id=<?php echo $v['id']; ?>" class="btn btn-outline btn-block"
                                    style="text-align: center;">View Details & Book</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section style="padding: 4rem 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">How It Works</h2>
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align: center;">
            <div>
                <i class="fas fa-id-card" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3>1. Verify Identity</h3>
                <p>Upload your citizenship and driving license for a secure rental experience.</p>
            </div>
            <div>
                <i class="fas fa-car" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3>2. Choose Vehicle</h3>
                <p>Browse our extensive fleet of well-maintained cars, bikes, and buses.</p>
            </div>
            <div>
                <i class="fas fa-calendar-check"
                    style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3>3. Book & Ride</h3>
                <p>Select your dates, confirm your booking, and start exploring Nepal.</p>
            </div>
        </div>
    </div>
</section>

<script>
    // Hero Carousel Logic
    const images = [
        'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80'
    ];
    let currentIndex = 0;
    const carouselImg = document.querySelector('.hero-carousel img');

    document.querySelector('.carousel-next').addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % images.length;
        carouselImg.style.opacity = '0';
        setTimeout(() => {
            carouselImg.src = images[currentIndex];
            carouselImg.style.opacity = '1';
        }, 200);
    });

    document.querySelector('.carousel-prev').addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        carouselImg.style.opacity = '0';
        setTimeout(() => {
            carouselImg.src = images[currentIndex];
            carouselImg.style.opacity = '1';
        }, 200);
    });

</script>

>>>>>>> origin/seraj
<?php include 'includes/footer.php'; ?>