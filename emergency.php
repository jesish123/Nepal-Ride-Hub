<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch active emergency contacts
$stmt = $pdo->query("SELECT * FROM emergency_contacts WHERE is_active = 1 ORDER BY display_order ASC");
$contacts = $stmt->fetchAll();

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;
$userRole = $isLoggedIn ? ($_SESSION['role'] ?? 'customer') : null;
$isAdmin = ($userRole === 'admin');
$isCustomer = ($isLoggedIn && !$isAdmin);

// Only fetch bookings for customers
$bookings = [];
if ($isCustomer) {
    $bStmt = $pdo->prepare("SELECT b.id, v.name as vehicle_name, b.start_date, b.end_date FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id WHERE b.user_id = ? AND b.status IN ('confirmed', 'pending') ORDER BY b.start_date DESC LIMIT 5");
    $bStmt->execute([$userId]);
    $bookings = $bStmt->fetchAll();
}
?>

<section style="padding: 4rem 0; background-color: #f0f7ff; min-height: 80vh;">
    <div class="container">

        <div style="text-align: center; margin-bottom: 3rem;">
            <h1
                style="color: #3561ff; font-size: 3rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 1rem;">
                <i class="fas fa-phone-volume"></i> Emergency Center
            </h1>
            <p style="font-size: 1.2rem; color: #555; max-width: 600px; margin: 0 auto;">Immediate assistance for
                accidents, breakdowns, or critical support while using our services.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">

            <!-- Left: Quick Call Directory -->
            <div>
                <h3 style="margin-bottom: 1.5rem; color: #111;">National & Core Helplines</h3>
                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                    <?php foreach ($contacts as $contact): ?>
                        <div
                            style="background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 10px 20px rgba(53,97,255,0.05); display: flex; align-items: center; gap: 1.5rem; border-left: 5px solid #3561ff;">
                            <div
                                style="width: 50px; height: 50px; border-radius: 50%; background: #eef2ff; color: #3561ff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                                <i class="fas <?php echo htmlspecialchars($contact['icon']); ?>"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin-bottom: 0.2rem; color: #111;">
                                    <?php echo htmlspecialchars($contact['service_name']); ?>
                                </h4>
                                <p style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem; line-height: 1.4;">
                                    <?php echo htmlspecialchars($contact['description']); ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <a href="tel:<?php echo htmlspecialchars($contact['phone_number']); ?>"
                                    style="display: inline-block; background: #3561ff; color: white; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; white-space: nowrap;">
                                    <i class="fas fa-phone-volume"></i>
                                    <?php echo htmlspecialchars($contact['phone_number']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Submit Incident Form -->
            <div
                style="background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.08);">
                <h3 style="margin-bottom: 1.5rem; color: #111;"><i class="fas fa-location-crosshairs"
                        style="color: #3561ff;"></i> Report an Incident</h3>

                <?php if (!$isLoggedIn): ?>
                    <div class="alert alert-warning">
                        <strong>Not Logged In:</strong> You must be logged into your account to securely electronically
                        transmit an SOS or incident report.
                        <br><br>
                        <a href="login.php" class="btn btn-outline">Log In to Report Incident</a>
                    </div>
                <?php elseif ($isAdmin): ?>
                    <!-- Admin cannot submit emergency reports -->
                    <div style="text-align: center; padding: 2rem 1rem;">
                        <div
                            style="width: 80px; height: 80px; border-radius: 50%; background: #fff3cd; color: #856404; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.5rem;">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h4 style="color: #111; margin-bottom: 0.75rem;">Admin Account Detected</h4>
                        <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                            Emergency incident reporting is a <strong>customer-only</strong> feature.<br>
                            As an administrator, you can <strong>view and manage</strong> all submitted emergency reports
                            from your dashboard.
                        </p>
                        <a href="admin_dashboard.php#emergency-reports" class="btn btn-primary"
                            style="background: var(--primary-blue); display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.8rem 1.8rem; border-radius: 8px;">
                            <i class="fas fa-bell"></i> View Emergency Reports
                        </a>
                    </div>
                <?php else: ?>
                    <form id="incidentForm">
                        <div id="incAlert" class="alert" style="display:none;"></div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label style="font-weight: 600;">Nature of Emergency</label>
                            <select name="incident_type" required
                                style="padding: 0.8rem; border: 2px solid #eee; border-radius: 8px;">
                                <option value="" disabled selected>-- Select Type --</option>
                                <option value="Medical / Injury">Medical Emergency / Injury</option>
                                <option value="Accident / Collision">Accident / Collision</option>
                                <option value="Vehicle Breakdown">Vehicle Breakdown / Stuck</option>
                                <option value="Theft / Security">Theft / Law Enforcement Issue</option>
                                <option value="Other">Other Urgent Issue</option>
                            </select>
                        </div>

                        <?php if (count($bookings) > 0): ?>
                            <div class="form-group" style="margin-bottom: 1.5rem;">
                                <label style="font-weight: 600;">Linked Booking (Optional)</label>
                                <select name="booking_id" style="padding: 0.8rem; border: 2px solid #eee; border-radius: 8px;">
                                    <option value="">-- No specific booking --</option>
                                    <?php foreach ($bookings as $b): ?>
                                        <option value="<?php echo $b['id']; ?>">
                                            <?php echo htmlspecialchars($b['vehicle_name']); ?> (#
                                            <?php echo $b['id']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label style="font-weight: 600;">Your Exact Location</label>
                            <input type="text" name="location_text" placeholder="E.g., Highway 4 near Pokhara Checkpost"
                                required style="padding: 0.8rem; border: 2px solid #eee; border-radius: 8px;">
                            <small style="color: #888; display: block; margin-top: 0.5rem;"><a href="#" id="gpsBtn"
                                    style="color: var(--primary-blue);"><i class="fas fa-location-arrow"></i> Target my GPS
                                    Location automatically</a></small>
                            <input type="hidden" name="gps_lat" id="gps_lat">
                            <input type="hidden" name="gps_lng" id="gps_lng">
                            <span id="gpsStatus"
                                style="font-size: 0.8rem; color: var(--success); margin-left: 10px;"></span>
                        </div>

                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label style="font-weight: 600;">Details of the Situation</label>
                            <textarea name="description" rows="4"
                                placeholder="Briefly describe what happened and what kind of support you need..." required
                                style="padding: 0.8rem; border: 2px solid #eee; border-radius: 8px;"></textarea>
                        </div>

                        <button type="submit" id="submitIncBtn" class="btn btn-primary btn-block"
                            style="background: #3561ff; padding: 1.2rem; font-size: 1.1rem; border-radius: 8px; border: none; font-weight: 700;">
                            <i class="fas fa-paper-plane"></i> Transmit Urgent SOS
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // GPS Geolocation Handler
        const gpsBtn = document.getElementById('gpsBtn');
        if (gpsBtn) {
            gpsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const status = document.getElementById('gpsStatus');
                status.innerText = "Locating...";
                status.style.color = "#888";

                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        document.getElementById('gps_lat').value = position.coords.latitude;
                        document.getElementById('gps_lng').value = position.coords.longitude;
                        status.innerText = "GPS Coordinates Attached! ✓";
                        status.style.color = "var(--success)";
                    }, function (error) {
                        status.innerText = "Failed to access location.";
                        status.style.color = "var(--danger)";
                    });
                } else {
                    status.innerText = "Geolocation not supported.";
                }
            });
        }

        // Incident Form Submission
        const incForm = document.getElementById('incidentForm');
        if (incForm) {
            incForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('submitIncBtn');
                const alertEl = document.getElementById('incAlert');
                const formData = new FormData(incForm);

                btn.disabled = true;
                btn.innerHTML = 'Transmitting...';

                try {
                    const response = await fetch('api/emergency.php?action=report', {
                        method: 'POST', body: formData
                    });
                    const data = await response.json();

                    alertEl.style.display = 'block';
                    if (data.success) {
                        alertEl.className = 'alert alert-success';
                        alertEl.innerHTML = data.message;
                        incForm.reset();
                        setTimeout(() => window.location.href = 'customer_dashboard.php', 3000);
                    } else {
                        alertEl.className = 'alert alert-danger';
                        alertEl.innerHTML = data.message;
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Transmit Urgent SOS';
                    }
                } catch (err) {
                    alertEl.className = 'alert alert-danger';
                    alertEl.style.display = 'block';
                    alertEl.innerHTML = 'Transmission failed check your connection.';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Transmit Urgent SOS';
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>