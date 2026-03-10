
<?php
    include "database/db.php";

    $message = "";
    $searchResults = [];
    
    if (isset($_POST['name'], $_POST['address'], $_POST['phone'], $_POST['email'], $_POST['service'])) {
        $name    = $_POST['name'];
        $address = $_POST['address'];
        $phone   = $_POST['phone'];
        $email   = $_POST['email'];
        $service = $_POST['service'];
        $price   = 25000;
        $status  = "pending";

        $stmt = $conn->prepare(
            "INSERT INTO service_requests
            (name, address, phone_no, email, service_ordered, price, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("sssssds", $name, $address, $phone, $email, $service, $price, $status);

        if ($stmt->execute()) {
            $message = "Service request submitted successfully!";
        } else {
            $message = "Something went wrong. Please try again.";
        }
    }

    if (isset($_POST['search'], $_POST['search_value'])) {
        $search = $_POST['search_value'];

        $stmt = $conn->prepare(
            "SELECT * FROM service_requests
            WHERE phone_no = ? OR email = ?"
        );

        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();

        $result = $stmt->get_result();
        $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    if (isset($_POST['update_request'], $_POST['update_id'])) {
        $id      = $_POST['update_id'];
        $name    = $_POST['update_name'];
        $address = $_POST['update_address'];
        $phone   = $_POST['update_phone'];
        $email   = $_POST['update_email'];
        $service = $_POST['update_service'];

        $stmt = $conn->prepare(
            "UPDATE service_requests 
            SET name = ?, address = ?, phone_no = ?, email = ?, service_ordered = ?
            WHERE service_id = ? AND status = 'pending'"
        );

        $stmt->bind_param("sssssi", $name, $address, $phone, $email, $service, $id);

        $stmt->execute(); 

        if ($stmt->affected_rows > 0) {
            $message = "Service request updated successfully!";
        } else {
            $message = "Update failed — No matching pending request found.";
        }
    }

    if (isset($_POST['delete_request'], $_POST['delete_id'])) {
        $id = $_POST['delete_id'];

        $stmt = $conn->prepare("DELETE FROM service_requests WHERE service_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "Service request deleted permanently!";
        } else {
            $message = "Delete failed — ID not found.";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | Wonder Water Equipment and Supplies Trading</title>
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

        <div class="services-box">
            <h3>We Offer Various Services</h3>
            <p>Membrane Cleaning</p>
            </p>Rehab of Media Filters</p>
            <p>Repair and Upgrades</p>
            <p>Dual Membrane Upgrade</p>
            <p>Supply and Install Water Stations</p>
        </div>

        <h2>Service Request</h2>
        <p>Please fill out the form below to request a service.</p>

        <?php if ($message): ?>
            <p style="color:#38bdf8;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" class="service-form">
            <input type="text" name="name" placeholder="Name / Organization Name" required>
            <textarea name="address" placeholder="Address" required></textarea>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email Address" required>

            <select name="service" required>
                <option value="">Select Service</option>
                <option value="Supply and Install Water Stations">
                    Supply and Install Water Stations
                </option>
                <option value="Membrane Cleaning">
                    Membrane Cleaning
                </option>
                <option value="Rehab of Media Filters">
                    Rehab of Media Filters
                </option>
                <option value="Repair and Upgrades">
                    Repair and Upgrades
                </option>
                <option value="Dual Membrane Upgrade">
                    Dual Membrane Upgrade
                </option>
            </select>

            <button type="submit">Submit Request</button>
        </form>

        <hr style="margin:50px 0;">

        <h2>Check Service Status</h2>
        <p>Enter your phone number or email to view your service request.</p>

        <form method="POST" class="service-form">
            <input
                type="text"
                name="search_value"
                placeholder="Phone Number or Email"
                required
            >
            <button type="submit" name="search">Check Status</button>
        </form>

        <?php if (!empty($searchResults)): ?>
            <h3 style="margin-top:30px;">Your Service Requests</h3>

            <table style="width:100%; margin-top:20px; border-collapse:collapse;">
                <tr style="background:#020617;">
                    <th>Name</th>
                    <th>Service</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($searchResults as $row): ?>
                    <tr style="text-align:center; border-bottom:1px solid #334155;">
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['service_ordered']) ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php if (!empty($searchResults)): ?>
                <h3 style="margin-top:20px;">Edit Your Service Request</h3>

                <form method="POST" class="service-form">
                    <input type="hidden" name="update_id" value="<?= $searchResults[0]['service_id'] ?>">


                    <input type="text" name="update_name" 
                        value="<?= htmlspecialchars($searchResults[0]['name']) ?>" 
                        placeholder="Name / Organization Name" required>

                    <textarea name="update_address" required><?= htmlspecialchars($searchResults[0]['address']) ?></textarea>

                    <input type="text" name="update_phone" 
                        value="<?= htmlspecialchars($searchResults[0]['phone_no']) ?>" 
                        placeholder="Phone Number" required>

                    <input type="email" name="update_email" 
                        value="<?= htmlspecialchars($searchResults[0]['email']) ?>" 
                        placeholder="Email Address" required>

                    <select name="update_service" required>
                        <option value="">Select Service</option>
                        <option value="Membrane Cleaning" 
                            <?= $searchResults[0]['service_ordered'] === 'Membrane Cleaning' ? 'selected' : '' ?>>
                            Membrane Cleaning
                        </option>
                        <option value="Rehab of Media Filters" 
                            <?= $searchResults[0]['service_ordered'] === 'Rehab of Media Filters' ? 'selected' : '' ?>>
                            Rehab of Media Filters
                        </option>
                        <option value="Repair and Upgrades" 
                            <?= $searchResults[0]['service_ordered'] === 'Repair and Upgrades' ? 'selected' : '' ?>>
                            Repair and Upgrades
                        </option>
                        <option value="Dual Membrane Upgrade" 
                            <?= $searchResults[0]['service_ordered'] === 'Dual Membrane Upgrade' ? 'selected' : '' ?>>
                            Dual Membrane Upgrade
                        </option>
                        <option value="Supply and Install Water Stations" 
                            <?= $searchResults[0]['service_ordered'] === 'Supply and Install Water Stations' ? 'selected' : '' ?>>
                            Supply and Install Water Stations
                        </option>
                    </select>

                    <button type="submit" name="update_request" value="1">Update Request</button>

                    <form method="POST" style="margin-top:10px;">
                        <input type="hidden" name="delete_id" value="<?= $searchResults[0]['service_id'] ?>">
                        <button type="submit" name="delete_request" value="1" style="background:#f87171; padding:10px 20px; border:none; cursor:pointer;">
                            Delete Request
                        </button>
                    </form>
                    </select>

                    <button type="submit" name="update_request" value="1">Update Request</button>

                    <form method="POST" style="margin-top:10px;">
                        <input type="hidden" name="delete_id" value="<?= $searchResults[0]['service_id'] ?>">
                        <button type="submit" name="delete_request" value="1" style="background:#f87171; padding:10px 20px; border:none; cursor:pointer;">
                            Delete Request
                        </button>
                    </form>

                </form>
            <?php endif; ?>

        <?php elseif (isset($_POST['search'])): ?>
            <p style="color:#f87171; margin-top:20px;">
                No service request found.
            </p>
        <?php endif; ?>

    </section>

<footer>
    © 2026 Wonder Water Equipment and Supplies Trading. All rights reserved.
</footer>

</body>
</html>
