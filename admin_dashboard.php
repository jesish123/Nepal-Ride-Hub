<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

// Stats
$totalUsers      = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$totalVehicles   = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$pendingDocs     = $pdo->query("SELECT COUNT(*) FROM user_documents WHERE status='pending'")->fetchColumn();
$pendingReviews  = 0;
try {
    $pendingReviews = $pdo->query("SELECT COUNT(*) FROM site_reviews WHERE status='pending'")->fetchColumn();
} catch (Exception $e) {}

$openEmergencies = 0;
try {
    $openEmergencies = $pdo->query("SELECT COUNT(*) FROM emergency_incidents WHERE status='open'")->fetchColumn();
} catch (Exception $e) {}
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
            <h2>Admin Dashboard</h2>
        </div>

        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div onclick="location.href='manage_users_ui.php'"
                style="background: var(--primary-blue); color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: var(--shadow); cursor: pointer; transition: 0.3s;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <h3 style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $totalUsers; ?></h3>
                <p style="font-weight: 600; opacity: 0.9;">Total Customers</p>
            </div>
            <div onclick="location.href='manage_vehicles_ui.php'"
                style="background: #d32f2f; color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: var(--shadow); cursor: pointer; transition: 0.3s;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <h3 style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $totalVehicles; ?></h3>
                <p style="font-weight: 600; opacity: 0.9;">Total Vehicles</p>
            </div>
            <div onclick="location.href='manage_bookings_ui.php'"
                style="background: #2e7d32; color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: var(--shadow); cursor: pointer; transition: 0.3s;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <h3 style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $pendingBookings; ?></h3>
                <p style="font-weight: 600; opacity: 0.9;">Pending Bookings</p>
            </div>
            <div onclick="location.href='manage_users_ui.php'"
                style="background: #fbc02d; color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: var(--shadow); cursor: pointer; transition: 0.3s;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <h3 style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $pendingDocs; ?></h3>
                <p style="font-weight: 600; opacity: 0.9;">Docs to Verify</p>
            </div>

            <div onclick="location.href='manage_reviews.php'"
                style="background: #00838f; color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: var(--shadow); cursor: pointer; transition: 0.3s;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <h3 style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $pendingReviews; ?></h3>
                <p style="font-weight: 600; opacity: 0.9;">Reviews to Moderate</p>
            </div>
            <div
                style="background: #c62828; color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: var(--shadow); position: relative; cursor: pointer; transition: 0.3s;"
                onclick="document.getElementById('emergency-reports').scrollIntoView({behavior:'smooth'})"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <h3 style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $openEmergencies; ?></h3>
                <p style="font-weight: 600; opacity: 0.9;"><i class="fas fa-triangle-exclamation"></i> Open SOS Reports</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Pending Documents -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>Documents for Verification</h3>
                </div>
                <hr style="margin: 1.5rem 0;">
                <div id="docsTarget">
                    <p>Loading documents...</p>
                </div>
            </div>

            <!-- Quick Manage Links -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
                <h3>Management Modules</h3>
                <hr style="margin: 1.5rem 0;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="manage_vehicles_ui.php" class="btn btn-outline btn-block" style="text-align: left;"><i
                            class="fas fa-car"></i> Manage Vehicles</a>
                    <a href="manage_bookings_ui.php" class="btn btn-outline btn-block" style="text-align: left;"><i
                            class="fas fa-calendar-alt"></i> Manage Bookings</a>
                    <a href="manage_users_ui.php" class="btn btn-outline btn-block" style="text-align: left;"><i
                            class="fas fa-users"></i> Manage Users</a>
                    <a href="track_vehicles.php" class="btn btn-primary btn-block" 
                       style="text-align: left; background: #1a1a1a; border: 1px solid #333; margin-top: 0.5rem;">
                        <i class="fas fa-satellite-dish" style="color: var(--primary-red);"></i> Live GPS Tracking
                    </a>
                </div>
            </div>
        </div>

        <!-- ===== EMERGENCY REPORTS SECTION ===== -->
        <div id="emergency-reports" style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow); margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: #dc3545;"><i class="fas fa-triangle-exclamation"></i> Customer Emergency Reports</h3>
                <button onclick="loadEmergencyReports()" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                    <i class="fas fa-rotate-right"></i> Refresh
                </button>
            </div>
            <hr style="margin: 1.5rem 0;">
            <div id="emrgTarget"><p style="color:#888;"><i class="fas fa-spinner fa-spin"></i> Loading emergency reports...</p></div>
        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Load pending docs
        try {
            const response = await fetch('api/manage_users.php?action=list_pending_docs');
            const data = await response.json();
            const docsTarget = document.getElementById('docsTarget');

            if (data.success && data.documents.length > 0) {
                let html = '<ul style="list-style: none;">';
                data.documents.forEach(doc => {
                    html += `
                    <li style="padding: 1rem; border: 1px solid var(--border-color); margin-bottom: 1rem; border-radius: 4px;">
                        <strong>User:</strong> ${doc.user_name} (${doc.email})<br>
                        <strong>Type:</strong> ${doc.document_type.toUpperCase()}<br>
                        <a href="${doc.file_path}" target="_blank" style="display: inline-block; margin: 0.5rem 0;">View Document</a>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            <button onclick="verifyDoc(${doc.id}, 'verified')" class="btn" style="background: #28a745; color: white; padding: 0.3rem 0.8rem;">Verify</button>
                            <button onclick="verifyDoc(${doc.id}, 'rejected')" class="btn" style="background: #dc3545; color: white; padding: 0.3rem 0.8rem;">Reject</button>
                        </div>
                    </li>
                `;
                });
                html += '</ul>';
                docsTarget.innerHTML = html;
            } else {
                docsTarget.innerHTML = '<p style="color: var(--success);"><i class="fas fa-check-circle"></i> No pending documents to verify!</p>';
            }
        } catch (e) {
            document.getElementById('docsTarget').innerHTML = 'Failed to load documents.';
        }

        // Auto-load emergency reports on page load
        loadEmergencyReports();
    });

    async function verifyDoc(docId, status) {
        if (!confirm(`Are you sure you want to mark this document as ${status}?`)) return;

        const formData = new FormData();
        formData.append('document_id', docId);
        formData.append('status', status);

        try {
            const response = await fetch('api/manage_users.php?action=verify_document', {
                method: 'POST', body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch {
            alert('Request failed');
        }
    }

    // ── Emergency Reports ──────────────────────────────────────
    const statusColors = {
        open:        { bg: '#dc3545', label: '🔴 Open' },
        in_progress: { bg: '#fd7e14', label: '🟠 In Progress' },
        resolved:    { bg: '#28a745', label: '✅ Resolved' }
    };

    async function loadEmergencyReports() {
        const target = document.getElementById('emrgTarget');
        target.innerHTML = '<p style="color:#888;"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';

        try {
            const res  = await fetch('api/emergency.php?action=list');
            const data = await res.json();

            if (!data.success) {
                target.innerHTML = `<p style="color:red;">${data.message}</p>`;
                return;
            }

            if (!data.reports.length) {
                target.innerHTML = '<p style="color: #28a745;"><i class="fas fa-check-circle"></i> No emergency reports submitted yet.</p>';
                return;
            }

            let html = '<div style="display: grid; gap: 1rem;">';
            data.reports.forEach(r => {
                const sc   = statusColors[r.status] || { bg: '#6c757d', label: r.status };
                const date = new Date(r.created_at).toLocaleString('en-NP', { dateStyle:'medium', timeStyle:'short' });
                const gps  = (r.gps_lat && r.gps_lng)
                    ? `<a href="https://maps.google.com/?q=${r.gps_lat},${r.gps_lng}" target="_blank"
                          style="font-size:0.8rem; color: var(--primary-blue);"><i class="fas fa-map-pin"></i> View on Map</a>`
                    : '';

                html += `
                <div style="border: 1px solid #eee; border-left: 5px solid ${sc.bg}; border-radius: 8px;
                            padding: 1.2rem 1.5rem; background: #fafafa;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem;">
                        <div>
                            <span style="background:${sc.bg}; color:#fff; padding:0.2rem 0.7rem; border-radius:50px; font-size:0.78rem; font-weight:700;">
                                ${sc.label}
                            </span>
                            <span style="font-weight:700; font-size:1rem; margin-left:0.75rem;">${r.incident_type}</span>
                        </div>
                        <small style="color:#999;">${date}</small>
                    </div>

                    <div style="margin-top:0.8rem; display:grid; grid-template-columns:1fr 1fr; gap:0.4rem 1.5rem; font-size:0.9rem;">
                        <div><strong>Customer:</strong> ${r.customer_name}</div>
                        <div><strong>Email:</strong> ${r.customer_email}</div>
                        <div><strong>Phone:</strong> ${r.customer_phone ?? '—'}</div>
                        <div><strong>Booking&nbsp;#:</strong> ${r.booking_id ?? 'None'}</div>
                    </div>

                    <div style="margin-top:0.6rem; font-size:0.9rem;">
                        <strong>Location:</strong> ${r.location_text} ${gps}
                    </div>
                    <div style="margin-top:0.4rem; font-size:0.9rem; color:#444;">
                        <strong>Details:</strong> ${r.description}
                    </div>

                    <div style="margin-top:1rem; display:flex; align-items:center; gap:0.6rem; flex-wrap:wrap;">
                        <label style="font-size:0.85rem; font-weight:600;">Update Status:</label>
                        <select id="status_${r.id}" style="padding:0.35rem 0.6rem; border:1px solid #ccc; border-radius:6px; font-size:0.85rem;">
                            <option value="open"        ${r.status==='open'        ?'selected':''}>Open</option>
                            <option value="in_progress" ${r.status==='in_progress' ?'selected':''}>In Progress</option>
                            <option value="resolved"    ${r.status==='resolved'    ?'selected':''}>Resolved</option>
                        </select>
                        <button onclick="updateEmergencyStatus(${r.id})" class="btn"
                            style="background:#3561ff; color:#fff; padding:0.35rem 1rem; border-radius:6px; font-size:0.85rem;">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </div>`;
            });
            html += '</div>';
            target.innerHTML = html;

        } catch(e) {
            target.innerHTML = '<p style="color:red;">Failed to load emergency reports.</p>';
        }
    }

    async function updateEmergencyStatus(id) {
        const status = document.getElementById('status_' + id).value;
        const fd = new FormData();
        fd.append('id', id);
        fd.append('status', status);

        try {
            const res  = await fetch('api/emergency.php?action=update_status', { method:'POST', body:fd });
            const data = await res.json();
            if (data.success) {
                loadEmergencyReports();   // refresh the list
            } else {
                alert('Error: ' + data.message);
            }
        } catch {
            alert('Request failed.');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>