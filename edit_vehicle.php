<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: manage_vehicles_ui.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v) {
    echo "<div class='container'><div class='alert alert-danger'>Vehicle not found.</div></div>";
    include 'includes/footer.php';
    exit;
}
?>

<section style="padding: 4rem 0;">
    <div class="container" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Edit Vehicle:
                <?php echo htmlspecialchars($v['name']); ?>
            </h2>
            <a href="manage_vehicles_ui.php" class="btn btn-outline">Back to Manage Vehicles</a>
        </div>

        <div style="background: #fff; padding: 2.5rem; border-radius: 8px; box-shadow: var(--shadow);">
            <form id="editVehicleForm" enctype="multipart/form-data">
                <div id="vehAlert" class="alert" style="display:none;"></div>
                <input type="hidden" name="id" value="<?php echo $v['id']; ?>">

                <div class="form-group">
                    <label>Vehicle Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($v['name']); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" required>
                            <option value="car" <?php echo $v['type'] === 'car' ? 'selected' : ''; ?>>Car</option>
                            <option value="bike" <?php echo $v['type'] === 'bike' ? 'selected' : ''; ?>>Bike</option>
                            <option value="bus" <?php echo $v['type'] === 'bus' ? 'selected' : ''; ?>>Tourist Bus</option>
                            <option value="taxi" <?php echo $v['type'] === 'taxi' ? 'selected' : ''; ?>>Taxi / Cab</option>
                            <option value="jeep" <?php echo $v['type'] === 'jeep' ? 'selected' : ''; ?>>Jeep</option>
                            <option value="van" <?php echo $v['type'] === 'van' ? 'selected' : ''; ?>>Van</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Terrain / Condition</label>
                        <select name="condition_type" required>
                            <option value="city" <?php echo $v['condition_type'] === 'city' ? 'selected' : ''; ?>>City /
                                Paved Roads</option>
                            <option value="offroad" <?php echo $v['condition_type'] === 'offroad' ? 'selected' : ''; ?>
                                >Off-Road / Mountains</option>
                            <option value="highway" <?php echo $v['condition_type'] === 'highway' ? 'selected' : ''; ?>
                                >Highway / Long Distance</option>
                            <option value="all-terrain" <?php echo $v['condition_type'] === 'all-terrain' ? 'selected' : ''; ?>>All-Terrain / 4x4</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" value="<?php echo htmlspecialchars($v['brand']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Model Year</label>
                        <input type="number" name="model_year" min="2000" max="2030"
                            value="<?php echo $v['model_year']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price Per Day (Rs.)</label>
                        <input type="number" name="price_per_day" step="0.01" value="<?php echo $v['price_per_day']; ?>"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Current Status</label>
                    <select name="status" required style="border: 2px solid var(--primary-blue); font-weight: 600;">
                        <option value="available" <?php echo $v['status'] === 'available' ? 'selected' : ''; ?>>Available
                        </option>
                        <option value="booked" <?php echo $v['status'] === 'booked' ? 'selected' : ''; ?>>Booked / In-Use
                        </option>
                        <option value="maintenance" <?php echo $v['status'] === 'maintenance' ? 'selected' : ''; ?>>Under
                            Maintenance</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Replace Vehicle Image (Leave blank to keep existing)</label>
                    <div style="margin-bottom: 0.5rem;">
                        <img src="<?php echo htmlspecialchars($v['image_path']); ?>" alt="Current Image"
                            style="height: 100px; border-radius: 4px; border: 1px solid #ccc;">
                    </div>
                    <input type="file" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($v['description']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="editBtn"
                    style="padding: 1rem; font-size: 1.1rem; border-radius: 8px;">Save Changes</button>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editForm = document.getElementById('editVehicleForm');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('editBtn');
                const alertEl = document.getElementById('vehAlert');
                const formData = new FormData(editForm);

                btn.disabled = true;
                btn.innerHTML = 'Saving...';

                try {
                    const response = await fetch('api/manage_vehicles.php?action=edit_vehicle', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    alertEl.style.display = 'block';
                    if (data.success) {
                        alertEl.className = 'alert alert-success';
                        alertEl.innerHTML = data.message;
                        setTimeout(() => window.location.href = 'manage_vehicles_ui.php', 1500);
                    } else {
                        alertEl.className = 'alert alert-danger';
                        alertEl.innerHTML = data.message;
                        btn.disabled = false;
                        btn.innerHTML = 'Save Changes';
                    }
                } catch (err) {
                    alertEl.className = 'alert alert-danger';
                    alertEl.style.display = 'block';
                    alertEl.innerHTML = 'Failed completely.';
                    btn.disabled = false;
                    btn.innerHTML = 'Save Changes';
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>