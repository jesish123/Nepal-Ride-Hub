<?php include 'includes/header.php'; ?>

<style>
    .about-hero {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&q=80') center/cover;
        padding: 8rem 0;
        text-align: center;
        color: #fff;
    }
    .about-hero h1 {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: #fff;
    }
    .about-section {
        padding: 5rem 0;
    }
    .section-title {
        text-align: center;
        margin-bottom: 4rem;
    }
    .section-title h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #111;
        margin-bottom: 1rem;
    }
    .section-title .underline {
        width: 80px;
        height: 4px;
        background: var(--new-blue);
        margin: 0 auto;
        border-radius: 2px;
    }
    .value-card {
        background: #fff;
        padding: 3rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        text-align: center;
        transition: transform 0.3s ease;
    }
    .value-card:hover {
        transform: translateY(-10px);
    }
    .value-icon {
        width: 70px;
        height: 70px;
        background: #f0f4ff;
        color: var(--new-blue);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 1.5rem;
    }
    .stats-container {
        background: var(--new-blue);
        padding: 4rem 0;
        color: #fff;
        border-radius: 30px;
        margin: 4rem 0;
    }
    .stat-item {
        text-align: center;
    }
    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        font-family: 'Outfit', sans-serif;
        margin-bottom: 0.5rem;
    }
</style>

<div class="about-hero">
    <div class="container">
        <h1>Our Story</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto; opacity: 0.9;">Redefining mobility across the Himalayas. Nepal Ride Hub is more than just a rental service; we are your companions in every journey.</p>
    </div>
</div>

<section class="about-section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 1.5rem;">The Premier Choice for Vehicle Rentals in Nepal</h2>
                <p style="color: #555; margin-bottom: 1.5rem; line-height: 1.8;">Founded with a vision to provide seamless transportation solutions, Nepal Ride Hub has grown into the country's most trusted vehicle rental platform. We understand that every trip in Nepal—whether a city commute, a family pilgrimage, or a mountain adventure—requires a reliable partner.</p>
                <p style="color: #555; margin-bottom: 1.5rem; line-height: 1.8;">Our fleet features everything from fuel-efficient city cars and rugged 4x4 SUVs to premium motorcycles and heavy-duty buses. Every vehicle in our hub undergoes rigorous multi-point inspections to ensure your safety and comfort.</p>
                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                    <a href="vehicles.php" class="btn-blue-solid">Explore Our Fleet</a>
                <?php endif; ?>
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80" alt="Nepal Trip" style="width: 100%; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
            </div>
        </div>

        <div class="stats-container">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <p style="opacity: 0.8;">Vehicles in Fleet</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10k+</div>
                    <p style="opacity: 0.8;">Happy Customers</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <p style="opacity: 0.8;">Cities Covered</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <p style="opacity: 0.8;">Support Available</p>
                </div>
            </div>
        </div>

        <div class="section-title">
            <h2>Our Core Values</h2>
            <div class="underline"></div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3 style="margin-bottom: 1rem;">Safety First</h3>
                <p style="color: #666;">We prioritize your safety above all. Every vehicle is GPS-tracked and fully insured for your peace of mind.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <h3 style="margin-bottom: 1rem;">Fair Pricing</h3>
                <p style="color: #666;">No hidden fees. What you see is what you pay. We offer the most competitive rates in the market.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid fa-leaf"></i></div>
                <h3 style="margin-bottom: 1rem;">Sustainability</h3>
                <p style="color: #666;">We are progressively including electric vehicles in our fleet to support a greener and cleaner Nepal.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
