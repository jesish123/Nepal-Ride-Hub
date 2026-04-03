<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Bookings</h2>
            <a href="admin_dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </div>

        <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
            <?php if (empty($bookings)): ?>
                <p>No bookings found.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: var(--light-bg); border-bottom: 2px solid var(--border-color);">
                                <th style="padding: 1rem;">ID</th>
                                <th style="padding: 1rem;">Customer</th>
                                <th style="padding: 1rem;">Vehicle</th>
                                <th style="padding: 1rem;">Dates</th>
                                <th style="padding: 1rem;">Amount</th>
                                <th style="padding: 1rem;">Status</th>
                                <th style="padding: 1rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($bookings as $b): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem;">#<?php echo str_pad($b['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td style="padding: 1rem;">
                                        <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($b['email']); ?></small>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <?php echo htmlspecialchars($b['vehicle_name']); ?>
                                        <small>(<?php echo ucfirst($b['vehicle_type']); ?>)</small>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <?php echo $b['start_date']; ?><br>to <?php echo $b['end_date']; ?>
                                    </td>
                                    <td style="padding: 1rem; font-weight: 600; color: var(--primary-blue);">Rs. <?php echo $b['total_amount']; ?></td>
                                    <td style="padding: 1rem;">
                                        <?php 
                                            $bg = '#fff3cd'; $col = '#856404'; // pending
                                            if($b['status'] === 'confirmed') { $bg = '#d4edda'; $col = '#155724'; }
                                            if($b['status'] === 'cancelled') { $bg = '#f8d7da'; $col = '#721c24'; }
                                            if($b['status'] === 'completed') { $bg = '#d1ecf1'; $col = '#0c5460'; }
                                        ?>
                                        <span style="padding: 0.3rem 0.6rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: <?php echo $bg; ?>; color: <?php echo $col; ?>;">
                                            <?php echo ucfirst($b['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; display: flex; gap: 0.5rem;">
                                        <?php if($b['status'] === 'pending'): ?>
                                            <button onclick="updateBooking(<?php echo $b['id']; ?>, 'confirmed')" class="btn" style="background: var(--success); color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Confirm</button>
                                            <button onclick="updateBooking(<?php echo $b['id']; ?>, 'cancelled')" class="btn" style="background: var(--danger); color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Reject</button>
                                        <?php elseif($b['status'] === 'confirmed'): ?>
                                            <button onclick="updateBooking(<?php echo $b['id']; ?>, 'completed')" class="btn" style="background: var(--primary-blue); color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Mark Completed</button>
                                        <?php else: ?>
                                            <span style="color: var(--gray-text); font-size: 0.8rem;">No Actions</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
async function updateBooking(id, status) {
    if(!confirm(`Are you sure you want to change this booking to ${status}?`)) return;
    
    const formData = new FormData();
    formData.append('booking_id', id);
    formData.append('status', status);

    try {
        const response = await fetch('api/manage_bookings.php?action=update_status', {
            method: 'POST', body: formData
        });
        const data = await response.json();
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    } catch {
        alert("Failed to update booking status.");
    }
}
</script>