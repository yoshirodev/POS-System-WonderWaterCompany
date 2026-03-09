<?php
    include 'database/db.php';

    $sql = "SELECT product_name, cost 
            FROM inventory
            WHERE quantity > 0";

    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Wonder Water Equipment and Supplies Trading</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <nav class="navbar">

        <div class="logo">
            <img src="assets/logo.png" alt="Wonder Water Logo">
            <span>Wonder Water Equipment and Supplies Trading</span>
        </div>

        <ul>
            <li><a href="index.html"><i class="fa-solid fa-house"></i> Home</a></li>
            <li><a href="about.html"><i class="fa-solid fa-circle-info"></i> About</a></li>
            <li><a href="products.php"><i class="fa-solid fa-box"></i> Products</a></li>
            <li><a href="services.php"><i class="fa-solid fa-screwdriver-wrench"></i> Services</a></li>
            <li><a href="portal.php"><i class="fa-solid fa-user-gear"></i> Portal</a></li>
        </ul>

    </nav>

    <section class="section">
        <h2>Our Products</h2>
        <p>Explore our available water equipment and supplies.</p>

        <div class="products-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                        <p class="price">₱<?= number_format($row['cost'], 2) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products currently available.</p>
            <?php endif; ?>
        </div>
    </section>

<footer>
    © 2026 Wonder Water Equipment and Supplies Trading. All rights reserved.
</footer>

</body>
</html>
