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
</head>
<body>

<nav class="navbar">
    <div class="logo">Wonder Water Equipment and Supplies Trading</div>
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="portal.php">Management Portal</a></li>
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
