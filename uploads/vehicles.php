<section style="background: var(--primary-blue); padding: 3rem 0; color: white; text-align: center;">
    <div class="container">
        <h2>Our Fleet</h2>
        <p>Find the perfect vehicle for your journey across Nepal.</p>
    </div>
</section>

<section style="padding: 4rem 0;">
    <div class="container">

        <!-- Search / Filter -->
        <div style="background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 2rem;">
            <form action="vehicles.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <select name="type" style="padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 4px; flex: 1; min-width: 150px;">
                    <option value="">All Types</option>
                    <option value="car" <?php if($typeFilter==='car') echo 'selected'; ?>>Cars</option>
                    <option value="bike" <?php if($typeFilter==='bike') echo 'selected'; ?>>Bikes</option>
                    <option value="bus" <?php if($typeFilter==='bus') echo 'selected'; ?>>Tourist Buses</option>
                    <option value="taxi" <?php if($typeFilter==='taxi') echo 'selected'; ?>>Taxi / Cabs</option>
                </select>
                <select name="condition" style="padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 4px; flex: 1; min-width: 150px;">
                    <option value="">Any Terrain/Condition</option>
                    <option value="city" <?php if($condFilter==='city') echo 'selected'; ?>>City / Paved</option>
                    <option value="offroad" <?php if($condFilter==='offroad') echo 'selected'; ?>>Off-Road/Mountain</option>
                    <option value="highway" <?php if($condFilter==='highway') echo 'selected'; ?>>Highway</option>
                    <option value="all-terrain" <?php if($condFilter==='all-terrain') echo 'selected'; ?>>All-Terrain</option>
                </select>
                <input type="text" name="location" placeholder="Search by name or brand..." value="<?php echo htmlspecialchars($locFilter); ?>" style="padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 4px; flex: 2; min-width: 200px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 100px;">Filter</button>
            </form>
        </div>

        <!-- Vehicle Grid -->
        <?php if(empty($vehicles)): ?>
            <div style="text-align: center; padding: 4rem;">
                <h3 style="color: var(--gray-text);">No vehicles found matching your criteria.</h3>
                <a href="vehicles.php" class="btn btn-outline" style="margin-top: 1rem;">Clear Filters</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                <?php foreach($vehicles as $v): ?>
                    <div style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow); display: flex; flex-direction: column;">
                        <div style="height: 200px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars($v['image_path']); ?>" alt="<?php echo htmlspecialchars($v['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <h3><?php echo htmlspecialchars($v['name']); ?></h3>
                                <div>
                                    <span style="background: var(--light-bg); padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid var(--border-color); margin-right: 0.2rem;">
                                        <?php echo ucfirst($v['type']); ?>
                                    </span>
                                    <span style="background: #eef2f3; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid #ced4da; color: #495057;">
                                        <i class="fas fa-route"></i> <?php echo ucfirst($v['condition_type'] ?? 'city'); ?>
                                    </span>
                                </div>
                            </div>
                            <p style="color: var(--gray-text); margin-bottom: 1rem; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($v['brand']); ?> &bull; <?php echo $v['model_year']; ?>
                            </p>
                            <div style="margin-bottom: 1.5rem;">
                                <span style="font-size: 1.4rem; font-weight: 700; color: var(--primary-red);">Rs. <?php echo $v['price_per_day']; ?></span>
                                <span style="color: var(--gray-text); font-size: 0.9rem;">/ day</span>
                            </div>
                            <!-- Push button to bottom -->
                            <div style="margin-top: auto;">
                                <a href="vehicle_details.php?id=<?php echo $v['id']; ?>" class="btn btn-outline btn-block" style="text-align: center;">View Details & Book</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>