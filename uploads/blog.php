<?php include 'includes/header.php'; ?>

<style>
    .blog-header {
        padding: 5rem 0;
        text-align: center;
        background: #f8fbff;
    }
    .blog-header h1 {
        font-size: 3rem;
        font-weight: 800;
        color: #111;
        margin-bottom: 1rem;
    }
    .blog-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
        padding-bottom: 5rem;
    }
    .blog-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .blog-card:hover {
        transform: translateY(-8px);
    }
    .blog-img {
        height: 220px;
        overflow: hidden;
        position: relative;
    }
    .blog-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .blog-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: var(--new-blue);
        color: #fff;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .blog-body {
        padding: 2rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .blog-body h3 {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.4;
        margin-bottom: 1rem;
        color: #111;
    }
    .blog-body p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    .blog-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 1.5rem;
        border-top: 1px solid #f0f0f0;
    }
    .author {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .author img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }
</style>

<div class="blog-header">
    <div class="container">
        <h1>Latest From Our Blog</h1>
        <p style="color: #666; max-width: 600px; margin: 0 auto;">Expert travel tips, vehicle maintenance guides, and the best driving routes across Nepal.</p>
    </div>
</div>

<section style="padding: 4rem 0;">
    <div class="container">
        <div class="blog-grid">
            <!-- Blog 1 -->
            <div class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=600&q=80" alt="Mustang Trip">
                    <div class="blog-badge">Road Trip</div>
                </div>
                <div class="blog-body">
                    <h3>The Ultimate Guide to Mustang Road Trips</h3>
                    <p>Driving through the rugged terrains of Mustang is every adventurer's dream. Learn how to prepare your 4x4 for the challenge.</p>
                    <div class="blog-footer">
                        <div class="author">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=3561ff&color=fff" alt="Author">
                            <span style="font-weight: 600; font-size: 0.8rem;">Admin</span>
                        </div>
                        <span style="color: #999; font-size: 0.8rem;">May 15, 2024</span>
                    </div>
                </div>
            </div>

            <!-- Blog 2 -->
            <div class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=600&q=80" alt="Safety Tips">
                    <div class="blog-badge">Safety</div>
                </div>
                <div class="blog-body">
                    <h3>Essential Safety Tips for Driving in Kathmandu</h3>
                    <p>Navigating the busy streets of Kathmandu can be tricky. We've compiled the top 5 tips for a stress-free city drive.</p>
                    <div class="blog-footer">
                        <div class="author">
                            <img src="https://ui-avatars.com/api/?name=Expert&background=111&color=fff" alt="Author">
                            <span style="font-weight: 600; font-size: 0.8rem;">Rent Specialist</span>
                        </div>
                        <span style="color: #999; font-size: 0.8rem;">May 10, 2024</span>
                    </div>
                </div>
            </div>

            <!-- Blog 3 -->
            <div class="blog-card">
                <div class="blog-img">
                    <img src="https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=600&q=80" alt="Fleet Guide">
                    <div class="blog-badge">Tips & Tricks</div>
                </div>
                <div class="blog-body">
                    <h3>How to Choose the Right Vehicle for Group Travel</h3>
                    <p>Planning a trip with friends or family? Here's how to select a vehicle that fits everyone comfortably without breaking the bank.</p>
                    <div class="blog-footer">
                        <div class="author">
                            <img src="https://ui-avatars.com/api/?name=Nepal+Ride&background=da291c&color=fff" alt="Author">
                            <span style="font-weight: 600; font-size: 0.8rem;">Fleet Manager</span>
                        </div>
                        <span style="color: #999; font-size: 0.8rem;">May 05, 2024</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <button class="btn-blue-solid" style="padding: 1rem 3rem;">View More Posts</button>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>