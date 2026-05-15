<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

// Fetch all vehicles
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll();
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Vehicles</h2>
            <a href="admin_dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            <!-- Add Vehicle Form -->
            <div
                style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow); align-self: start;">
                <h3>Add New Vehicle</h3>
                <hr style="margin: 1.5rem 0;">

                <form id="addVehicleForm" enctype="multipart/form-data">
                    <div id="vehAlert" class="alert" style="display:none;"></div>
                    <div class="form-group">
                        <label>Vehicle Name (e.g., Hyundai i20)</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" required>
                            <option value="car">Car</option>
                            <option value="bike">Bike</option>
                            <option value="bus">Tourist Bus</option>
                            <option value="taxi">Taxi / Cab</option>
                            <option value="jeep">Jeep</option>
                            <option value="van">Van</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Terrain / Condition</label>
                        <select name="condition_type" required>
                            <option value="city">City / Paved Roads</option>
                            <option value="offroad">Off-Road / Mountains</option>
                            <option value="highway">Highway / Long Distance</option>
                            <option value="all-terrain">All-Terrain / 4x4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" required>
                    </div>
                    <div class="form-group">
                        <label>Model Year</label>
                        <input type="number" name="model_year" min="2000" max="2030" required>
                    </div>
                    <div class="form-group">
                        <label>Price Per Day (Rs.)</label>
                        <input type="number" name="price_per_day" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Image</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="addBtn">Add Vehicle</button>
                </form>
            </div>

            <!-- Vehicle List -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
                <h3>All Vehicles</h3>
                <hr style="margin: 1.5rem 0;">

                <?php if (empty($vehicles)): ?>
                    <p>No vehicles found.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="background: var(--light-bg); border-bottom: 2px solid var(--border-color);">
                                    <th style="padding: 1rem;">Image</th>
                                    <th style="padding: 1rem;">Details</th>
                                    <th style="padding: 1rem;">Price</th>
                                    <th style="padding: 1rem;">Status</th>
                                    <th style="padding: 1rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehicles as $v): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 1rem;">
                                            <img src="<?php echo htmlspecialchars($v['image_path']); ?>" alt="Vehicle"
                                                style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        </td>
                                        <td style="padding: 1rem;">
                                            <strong><?php echo htmlspecialchars($v['name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($v['brand']); ?>
                                                (<?php echo $v['model_year']; ?>)</small>
                                        </td>
                                        <td style="padding: 1rem;">Rs. <?php echo $v['price_per_day']; ?></td>
                                        <td style="padding: 1rem;">
                                            <?php
                                            $sColor = $v['status'] === 'available' ? 'green' : ($v['status'] === 'booked' ? 'orange' : 'red');
                                            echo "<span style='color: $sColor; font-weight: 500;'>" . ucfirst($v['status']) . "</span>";
                                            ?>
                                        </td>
                                        <td style="padding: 1rem; display: flex; gap: 0.5rem;">
                                            <a href="edit_vehicle.php?id=<?php echo $v['id']; ?>" class="btn"
                                                style="background: var(--primary-blue); color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem; text-decoration: none;">Edit</a>
                                            <button onclick="deleteVehicle(<?php echo $v['id']; ?>)" class="btn"
                                                style="background: var(--danger); color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addForm = document.getElementById('addVehicleForm');
        if (addForm) {
            addForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('addBtn');
                const alertEl = document.getElementById('vehAlert');
                const formData = new FormData(addForm);

                btn.disabled = true;
                btn.innerHTML = 'Adding...';

                try {
                    const response = await fetch('api/manage_vehicles.php?action=add_vehicle', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    alertEl.style.display = 'block';
                    if (data.success) {
                        alertEl.className = 'alert alert-success';
                        alertEl.innerHTML = data.message;
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        alertEl.className = 'alert alert-danger';
                        alertEl.innerHTML = data.message;
                        btn.disabled = false;
                        btn.innerHTML = 'Add Vehicle';
                    }
                } catch (err) {
                    alertEl.className = 'alert alert-danger';
                    alertEl.style.display = 'block';
                    alertEl.innerHTML = 'Failed completely.';
                    btn.disabled = false;
                    btn.innerHTML = 'Add Vehicle';
                }
            });
        }
    });

    async function deleteVehicle(id) {
        if (!confirm("Are you sure you want to delete this vehicle?")) return;

        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch('api/manage_vehicles.php?action=delete_vehicle', {
                method: 'POST', body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert("Vehicle deleted.");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        } catch {
            alert("Delete failed.");
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
