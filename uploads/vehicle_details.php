<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    echo "<div class='container' style='padding: 4rem 0; text-align: center;'><h2>Vehicle not found.</h2><a href='vehicles.php' class='btn btn-outline'>Back to Fleet</a></div>";
    include 'includes/footer.php';
    exit;
}

$canBook = false;
$bookError = "Please log in as a customer to book this vehicle.";

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
    $uid = $_SESSION['user_id'];
    $docStmt = $pdo->prepare("SELECT SUM(CASE WHEN document_type='citizenship' AND status='verified' THEN 1 ELSE 0 END) as c_ok, SUM(CASE WHEN document_type='license' AND status='verified' THEN 1 ELSE 0 END) as l_ok FROM user_documents WHERE user_id = ?");
    $docStmt->execute([$uid]);
    $docs = $docStmt->fetch();
    
    if ($docs['c_ok'] > 0 && $docs['l_ok'] > 0) {
        $canBook = true;
    } else {
        $bookError = "Your Citizenship and Driving License must be VERIFIED by an admin before you can book. Please check your Dashboard.";
    }
}
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            
            <!-- Vehicle Visuals -->
            <div>
                <img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>" style="width: 100%; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 1.5rem;">
                <div style="background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow);">
                    <h3>Specifications</h3>
                    <ul style="list-style: none; margin-top: 1rem;">
                        <li style="margin-bottom: 0.5rem;"><strong>Brand:</strong> <?php echo htmlspecialchars($vehicle['brand']); ?></li>
                        <li style="margin-bottom: 0.5rem;"><strong>Model Year:</strong> <?php echo $vehicle['model_year']; ?></li>
                        <li style="margin-bottom: 0.5rem;"><strong>Type:</strong> <?php echo ucfirst($vehicle['type']); ?></li>
                        <li style="margin-bottom: 0.5rem;"><strong>Terrain / Condition:</strong> <?php echo ucfirst($vehicle['condition_type'] ?? 'city'); ?></li>
                        <li style="margin-bottom: 0.5rem;"><strong>Status:</strong> <?php echo ucfirst($vehicle['status']); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Vehicle Details & Booking -->
            <div>
                <h1 style="color: var(--primary-blue); font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($vehicle['name']); ?></h1>
                <p style="font-size: 1.5rem; color: var(--primary-red); font-weight: 700; margin-bottom: 1.5rem;">Rs. <?php echo $vehicle['price_per_day']; ?> <span style="font-size: 1rem; color: var(--gray-text); font-weight: normal;">/ day</span></p>
                
                <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 2rem; color: #555;">
                    <?php echo nl2br(htmlspecialchars($vehicle['description'])); ?>
                </p>

                <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow); border-top: 4px solid var(--primary-red);">
                    <h3 style="margin-bottom: 1.5rem;">Book This Vehicle</h3>

                    <?php if (!$canBook): ?>
                        <div class="alert alert-danger" style="margin-bottom: 1rem;">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $bookError; ?>
                        </div>
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="btn btn-primary btn-block" style="text-align: center;">Login Now to Book</a>
                        <?php else: ?>
                            <!-- Inline Document Upload for Streamlined UX -->
                            <div style="background: var(--light-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                                <h4 style="margin-bottom: 0.5rem;">Upload Document Here</h4>
                                <p style="font-size: 0.85rem; margin-bottom: 1rem;">Admin will verify it before your booking is accepted.</p>
                                <form id="documentUploadForm" enctype="multipart/form-data">
                                    <div id="docAlert" class="alert" style="display:none; padding: 0.5rem; font-size: 0.85rem;"></div>
                                    <div class="form-group" style="margin-bottom: 0.8rem;">
                                        <select name="document_type" required style="padding: 0.5rem;">
                                            <option value="citizenship">Citizenship/Passport</option>
                                            <option value="license">Driving License</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0.8rem;">
                                        <label style="font-size: 0.85rem;">Expiry Date (if applicable)</label>
                                        <input type="date" name="expiry_date" style="padding: 0.5rem;">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 1rem;">
                                        <input type="file" name="document_file" accept=".jpg,.jpeg,.png,.pdf" required style="padding: 0.5rem; font-size: 0.85rem;">
                                    </div>
                                    <button type="submit" class="btn btn-outline btn-block" id="uploadBtn">Upload for Verification</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php elseif($vehicle['status'] !== 'available'): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-ban"></i> This vehicle is currently <?php echo $vehicle['status']; ?> and cannot be booked.
                        </div>
                    <?php else: ?>
                        <form id="bookingForm">
                            <div id="bookAlert" class="alert" style="display:none;"></div>
                            <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                            <input type="hidden" id="price_per_day" value="<?php echo $vehicle['price_per_day']; ?>">
                            
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label>Purpose of Booking</label>
                                <select name="purpose" id="booking_purpose" required onchange="toggleRouteFields()">
                                    <option value="travel">General Travel / Tour</option>
                                    <option value="function">Wedding / Special Function</option>
                                    <option value="pick_and_drop">Kathmandu City Pick & Drop (Taxi/Cab)</option>
                                </select>
                            </div>

                            <div id="route_fields" style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div class="form-group">
                                    <label>Pickup Location</label>
                                    <input type="text" name="pickup_location" placeholder="E.g. Thamel, KTM">
                                </div>
                                <div class="form-group">
                                    <label>Drop-off Location</label>
                                    <input type="text" name="dropoff_location" placeholder="E.g. Airport, KTM">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" id="start_date" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" id="end_date" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>

                            <div style="background: var(--light-bg); padding: 1rem; border-radius: 4px; border: 1px solid var(--border-color); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 500;">Estimated Total:</span>
                                <span id="total_price_display" style="font-size: 1.2rem; font-weight: 700; color: var(--primary-blue);">Rs. 0.00</span>
                            </div>

                            <div class="alert alert-warning" style="margin-bottom: 1.5rem; font-size: 0.9rem;">
                                <strong><i class="fas fa-id-badge"></i> STRICT POLICY:</strong> You <strong>MUST</strong> bring your original Citizenship and Driving License to the office physically before the keys are handed over.
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" id="bookBtn">Confirm Booking Request</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const toggleRouteFields = () => {
    const purpose = document.getElementById('booking_purpose').value;
    const routeDiv = document.getElementById('route_fields');
    if(purpose === 'pick_and_drop') {
        routeDiv.style.display = 'grid';
    } else {
        routeDiv.style.display = 'none';
        routeDiv.querySelectorAll('input').forEach(i => i.value = '');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const sDate = document.getElementById('start_date');
    const eDate = document.getElementById('end_date');
    const display = document.getElementById('total_price_display');
    const price = parseFloat(document.getElementById('price_per_day')?.value || 0);

    const calcTotal = () => {
        if(!sDate.value || !eDate.value) return;
        const start = new Date(sDate.value);
        const end = new Date(eDate.value);
        if(end < start) {
            display.innerText = "Invalid Dates";
            return;
        }
        const diffTime = Math.abs(end - start);
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (diffDays === 0) diffDays = 1; // Minimum 1 day syntax
        
        display.innerText = "Rs. " + (diffDays * price).toFixed(2);
    };

    if(sDate) sDate.addEventListener('change', calcTotal);
    if(eDate) eDate.addEventListener('change', calcTotal);

    const bookForm = document.getElementById('bookingForm');
    if(bookForm) {
        bookForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('bookBtn');
            const alertEl = document.getElementById('bookAlert');
            const formData = new FormData(bookForm);

            btn.disabled = true;
            btn.innerHTML = 'Processing...';

            try {
                const response = await fetch('api/manage_bookings.php?action=create', {
                    method: 'POST', body: formData
                });
                const data = await response.json();
                
                alertEl.style.display = 'block';
                if(data.success) {
                    alertEl.className = 'alert alert-success';
                    alertEl.innerHTML = data.message;
                    setTimeout(() => window.location.href = 'customer_dashboard.php', 2000);
                } else {
                    alertEl.className = 'alert alert-danger';
                    alertEl.innerHTML = data.message;
                    btn.disabled = false;
                    btn.innerHTML = 'Confirm Booking Request';
                }
            } catch(err) {
                alertEl.className = 'alert alert-danger';
                alertEl.style.display = 'block';
                alertEl.innerHTML = 'Booking request failed.';
                btn.disabled = false;
                btn.innerHTML = 'Confirm Booking Request';
            }
        });
    }

    // Inline Doc Upload Form Handling
    const docForm = document.getElementById('documentUploadForm');
    if(docForm) {
        docForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('uploadBtn');
            const alertEl = document.getElementById('docAlert');
            const formData = new FormData(docForm);

            btn.disabled = true;
            btn.innerHTML = 'Uploading...';

            try {
                const response = await fetch('api/manage_users.php?action=upload_document', {
                    method: 'POST', body: formData
                });
                const data = await response.json();
                
                alertEl.style.display = 'block';
                if(data.success) {
                    alertEl.className = 'alert alert-success';
                    alertEl.innerHTML = data.message;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alertEl.className = 'alert alert-danger';
                    alertEl.innerHTML = data.message;
                    btn.disabled = false;
                    btn.innerHTML = 'Upload for Verification';
                }
            } catch(err) {
                alertEl.className = 'alert alert-danger';
                alertEl.style.display = 'block';
                alertEl.innerHTML = 'Upload failed.';
                btn.disabled = false;
                btn.innerHTML = 'Upload for Verification';
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>