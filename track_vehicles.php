<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
$isAdmin = ($_SESSION['role'] === 'admin');
?>

<section style="background: #f8f9fa; color: #111; min-height: 90vh; display: flex; flex-direction: column;">
    <div
        style="padding: 1.5rem 2rem; background: #fff; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        <div>
            <h2 style="margin: 0; color: #111; font-size: 1.5rem; font-weight: 800;"><i class="fas fa-satellite-dish"
                    style="color: #3561ff;"></i> Live Fleet Tracking</h2>
            <p style="margin: 0; font-size: 0.85rem; color: #666;">Monitoring all active customer trips across Nepal</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div id="connectionStatus"
                style="font-size: 0.85rem; background: #eef2ff; color: #3561ff; padding: 0.5rem 1.2rem; border-radius: 50px; display: flex; align-items: center; gap: 0.6rem; font-weight: 700;">
                <span
                    style="width: 10px; height: 10px; background: #28a745; border-radius: 50%; box-shadow: 0 0 10px rgba(40,167,69,0.4);"></span>
                Live System
            </div>
            <a href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'customer_dashboard.php'; ?>" class="btn-blue-solid"
                style="padding: 0.6rem 1.5rem; font-size: 0.9rem; text-decoration: none;">Back to Dashboard</a>
        </div>
    </div>

    <div style="display: flex; flex: 1; overflow: hidden;">
        <!-- Trip Sidebar -->
        <div
            style="width: 360px; background: #fff; border-right: 1px solid #eee; overflow-y: auto; padding: 2rem; box-shadow: 10px 0 30px rgba(0,0,0,0.02);">
            <h4
                style="margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px; color: #999; font-weight: 700;">
                Active Trips</h4>
            <div id="tripList" style="display: flex; flex-direction: column; gap: 1.2rem;">
                <!-- Loaded dynamically -->
                <div style="color: #999; text-align: center; padding: 3rem; font-size: 0.9rem;">
                    <i class="fas fa-circle-notch fa-spin"
                        style="font-size: 1.5rem; margin-bottom: 1rem; display: block; color: #3561ff;"></i>
                    Connecting to fleet...
                </div>
            </div>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #f0f0f0;">
                <p
                    style="font-size: 0.75rem; color: #aaa; margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                    Admin Controls</p>
                <a href="seed_coordinates.php" target="_blank" class="btn btn-outline btn-block"
                    style="font-size: 0.85rem; text-align: left; padding: 0.8rem 1rem; border-radius: 8px; border-color: #eee; color: #444; margin-bottom: 0.8rem;">
                    <i class="fas fa-sync" style="color: #3561ff;"></i> Sync Sample GPS Data
                </a>
                <button onclick="loadActiveTrips()" class="btn btn-outline btn-block"
                    style="font-size: 0.85rem; text-align: left; padding: 0.8rem 1rem; border-radius: 8px; border-color: #eee; color: #444;">
                    <i class="fas fa-rotate-right" style="color: #3561ff;"></i> Force Refresh
                </button>
            </div>
        </div>

        <!-- Map Container -->
        <div style="flex: 1; position: relative; background: #f0f2f5;">
            <div id="map" style="width: 100%; height: 100%;"></div>

            <!-- Map Overlay -->
            <div
                style="position: absolute; bottom: 30px; right: 30px; background: rgba(255,255,255,0.95); padding: 1.2rem 1.8rem; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.8); backdrop-filter: blur(10px); z-index: 1000;">
                <div
                    style="font-size: 0.75rem; color: #999; margin-bottom: 0.8rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    Fleet Status</div>
                <div style="display: flex; gap: 2.5rem;">
                    <div>
                        <div id="activeCount" style="font-size: 1.8rem; font-weight: 800; color: #111;">0</div>
                        <div style="font-size: 0.75rem; color: #666; font-weight: 600;">ACTIVE</div>
                    </div>
                    <div>
                        <div id="movingCount" style="font-size: 1.8rem; font-weight: 800; color: #3561ff;">0</div>
                        <div style="font-size: 0.75rem; color: #666; font-weight: 600;">MOVING</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trip Detail Modal (Optional hidden by default) -->
<div id="tripModal"
    style="display: none; position: fixed; bottom: 20px; left: 370px; width: 300px; background: #fff; color: #111; padding: 1.5rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
        <h5 id="modalTitle" style="margin: 0;">Vehicle Name</h5>
        <button onclick="closeModal()" style="border: none; background: none; cursor: pointer;"><i
                class="fas fa-times"></i></button>
    </div>
    <img id="modalImg" src=""
        style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
    <div style="font-size: 0.9rem;">
        <p><strong>Customer:</strong> <span id="modalCustomer">Name</span></p>
        <p><strong>Phone:</strong> <span id="modalPhone">Phone</span></p>
        <p><strong>Status:</strong> <span id="modalStatus" style="color: var(--success);">In Transit</span></p>
    </div>
    <a id="callBtn" href="#" class="btn btn-primary btn-block"
        style="margin-top: 1rem; padding: 0.6rem; text-decoration: none; text-align: center; display: block;">
        <i class="fas fa-phone"></i> Call Driver
    </a>
</div>

<!-- Leaflet CSS & JS (Free, No API Key Required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    let map;
    let markers = {};
    let activeTrips = [];

    // Initialize Map (Leaflet version)
    function initLeafletMap() {
        // Center on Nepal (Kathmandu area)
        map = L.map('map', {
            zoomControl: false // Move zoom control if needed, but we'll hide for premium look
        }).setView([28.3949, 84.1240], 8);

        // CartoDB Voyager (Premium Light Theme)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        loadActiveTrips();
        setInterval(loadActiveTrips, 15000); // Poll API every 15s

        // --- Live Movement Simulation ---
        setInterval(simulateLiveMovement, 2000); // Smooth movement every 2s
    }

    // Call init on load
    document.addEventListener('DOMContentLoaded', initLeafletMap);

    // Simulation Engine: Moves markers slightly to simulate real-time driving
    function simulateLiveMovement() {
        activeTrips.forEach(trip => {
            // Only simulate movement for "Confirmed" (Active) trips
            if (trip.booking_status !== 'confirmed') return;

            // Randomly jitter position slightly (0.0001 degrees is ~10-15 meters)
            const latDiff = (Math.random() - 0.5) * 0.0003;
            const lngDiff = (Math.random() - 0.5) * 0.0003;

            trip.gps_lat = parseFloat(trip.gps_lat) + latDiff;
            trip.gps_lng = parseFloat(trip.gps_lng) + lngDiff;

            // Update marker on map if it exists
            if (markers[trip.vehicle_id]) {
                markers[trip.vehicle_id].setLatLng([trip.gps_lat, trip.gps_lng]);
            }
        });
        // We don't re-render the whole list to keep it smooth, 
        // but we update the text in current trip modal if open
        const modal = document.getElementById('tripModal');
        if (modal.style.display === 'block') {
            // Optionally update coordinates text here if wanted
        }
    }

    async function loadActiveTrips() {
        try {
            const response = await fetch('api/manage_vehicles.php?action=list_active_tracking');
            const data = await response.json();

            if (data.success) {
                activeTrips = data.trips;
                renderTripList();
                updateMarkers();
                document.getElementById('activeCount').innerText = activeTrips.length;

                // Count moving (for simulation/demo we just count those with status confirmed)
                const moving = activeTrips.filter(t => t.booking_status === 'confirmed').length;
                document.getElementById('movingCount').innerText = moving;
            }
        } catch (e) {
            console.error("Tracking Error:", e);
        }
    }

    function renderTripList() {
        const list = document.getElementById('tripList');
        if (activeTrips.length === 0) {
            list.innerHTML = `
                <div style="color: #bbb; text-align: center; padding: 4rem 1rem;">
                    <div style="width: 60px; height: 60px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="fas fa-search" style="font-size: 1.5rem; color: #ddd;"></i>
                    </div>
                    <p style="font-weight: 600; color: #888; margin-bottom: 0.5rem;">No active vehicles</p>
                    <p style="font-size: 0.8rem; color: #aaa; line-height: 1.4;">Make sure you have <strong>'pending'</strong> or <strong>'confirmed'</strong> bookings to track.</p>
                </div>`;
            return;
        }

        list.innerHTML = activeTrips.map(trip => {
            const hasGps = trip.gps_lat && trip.gps_lng;
            const statusColor = trip.booking_status === 'confirmed' ? '#28a745' : '#ffc107';

            return `
            <div onclick="${hasGps ? `focusTrip(${trip.booking_id})` : ''}" 
                 style="background: #fff; padding: 1.2rem; border-radius: 12px; cursor: pointer; transition: all 0.3s; border: 1px solid #f0f0f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); opacity: ${hasGps ? 1 : 0.6};" 
                 onmouseover="this.style.borderColor='#3561ff'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#f0f0f0'; this.style.transform='translateY(0)';">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.8rem;">
                    <div style="width: 44px; height: 44px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas ${getVehicleIcon(trip.vehicle_type)}" style="color: #3561ff; font-size: 1.2rem;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 700; font-size: 0.95rem; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${trip.vehicle_name}</div>
                        <div style="font-size: 0.75rem; color: #888; font-weight: 500;">${trip.customer_name}</div>
                    </div>
                    <div style="background: ${hasGps ? statusColor : '#ddd'}; width: 10px; height: 10px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 5px rgba(0,0,0,0.1);"></div>
                </div>
                <div style="font-size: 0.75rem; color: #666; display: flex; justify-content: space-between; align-items: center; padding-top: 0.8rem; border-top: 1px dashed #f0f0f0;">
                    <span style="font-weight: 500;">
                        ${hasGps
                    ? `<i class="fas fa-location-dot" style="color: #3561ff; margin-right: 4px;"></i> ${parseFloat(trip.gps_lat).toFixed(4)}, ${parseFloat(trip.gps_lng).toFixed(4)}`
                    : `<i class="fas fa-circle-exclamation" style="color: #dc3545; margin-right: 4px;"></i> SIGNAL LOST`
                }
                    </span>
                    <span style="color: ${statusColor}; font-weight: 700; font-size: 0.7rem;">
                        ${trip.booking_status.toUpperCase()}
                    </span>
                </div>
            </div>
        `;
        }).join('');
    }

    function updateMarkers() {
        const tripIdsWithGps = activeTrips.filter(t => t.gps_lat && t.gps_lng).map(t => t.vehicle_id.toString());

        // Remove dead markers
        Object.keys(markers).forEach(id => {
            if (!tripIdsWithGps.includes(id)) {
                map.removeLayer(markers[id]);
                delete markers[id];
            }
        });

        activeTrips.forEach(trip => {
            if (!trip.gps_lat || !trip.gps_lng) return; // Skip those without GPS

            const pos = [parseFloat(trip.gps_lat), parseFloat(trip.gps_lng)];

            if (markers[trip.vehicle_id]) {
                // Leaflet update position
                markers[trip.vehicle_id].setLatLng(pos);
            } else {
                // Create custom icon
                const myIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: #3561ff; color: white; border: 3px solid white; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(53,97,255,0.4);">
                             <i class="fas ${getVehicleIcon(trip.vehicle_type)}" style="font-size: 16px;"></i>
                           </div>`,
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                });

                markers[trip.vehicle_id] = L.marker(pos, { icon: myIcon }).addTo(map);
                markers[trip.vehicle_id].on('click', () => {
                    openTripModal(trip);
                });
            }
        });
    }

    function focusTrip(bookingId) {
        const trip = activeTrips.find(t => t.booking_id == bookingId);
        if (trip) {
            map.flyTo([parseFloat(trip.gps_lat), parseFloat(trip.gps_lng)], 14, {
                animate: true,
                duration: 1.5
            });
            openTripModal(trip);
        }
    }

    function openTripModal(trip) {
        const modal = document.getElementById('tripModal');
        document.getElementById('modalTitle').innerText = trip.vehicle_name;
        document.getElementById('modalImg').src = trip.image_path;
        document.getElementById('modalCustomer').innerText = trip.customer_name;
        document.getElementById('modalPhone').innerText = trip.customer_phone;

        // Link the call button
        const callBtn = document.getElementById('callBtn');
        callBtn.href = `tel:${trip.customer_phone}`;

        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('tripModal').style.display = 'none';
    }

    function getVehicleIcon(type) {
        switch (type) {
            case 'bike': return 'fa-motorcycle';
            case 'bus': return 'fa-bus';
            case 'jeep': return 'fa-truck-pickup';
            default: return 'fa-car';
        }
    }
</script>


<?php include 'includes/footer.php'; ?>