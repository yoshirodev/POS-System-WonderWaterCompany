<?php
    session_start();

    include "../database/db.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../portal.php");
        exit;
    }

    $_SESSION['last_activity'] = time();

    $userID = $_SESSION['user_id'];

    $today = date("F d, Y"); 

    date_default_timezone_set("Asia/Manila");

    $statement = $conn->prepare("SELECT firstname, middlename, lastname, birthdate, email, phonenumber, accountType FROM logindata WHERE accID = ?");
    $statement->bind_param("i", $userID);
    $statement->execute();
    $result = $statement->get_result();

    if (!$result || $result->num_rows === 0) {
        echo "Profile not found.";
        exit;
    }

    $user = $result->fetch_assoc();

    $dailySales = $conn->query("SELECT daily_id, sales_date, total_items_sold, total_revenue, total_transactions FROM daily_sales ORDER BY sales_date DESC");

    $weeklySales = $conn->query("SELECT weekly_id, week_number, year, total_items_sold, total_revenue, total_transactions FROM weekly_sales ORDER BY year DESC, week_number DESC");

    $monthlySales = $conn->query("SELECT monthly_id, month, year, total_items_sold, total_revenue, total_transactions FROM monthly_sales ORDER BY year DESC, month DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <title>Sales Overview</title>
</head>
<body>
    <div class="sidebar">

        <div class="date-box">
            <p><strong>Today</strong></p>
            <p><?= $today ?></p>
        </div>

        <a href="management.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="inventory.php"><i class="fa-solid fa-boxes-stacked"></i> Inventory</a>
        <a href="sales.php"><i class="fa-solid fa-chart-line"></i> Sales Overview</a>
        <a href="transactions.php"><i class="fa-solid fa-receipt"></i> Transactions</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
    </div>


    <div class="topbar">
        <div class="profile-btn" onclick="toggleProfile()">
            Profile <i class="fa-solid fa-user-circle"></i>
        </div>

        <button onclick="window.location.href='../logout.php'" class="logout-btn">
            Logout <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </div>

    <div id="profileBox" class="profile-box">
        <h3>Profile</h3>

        <p><?= htmlspecialchars($user['firstname'] . " " . $user['lastname']) ?></p>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <p>+63<?= htmlspecialchars($user['phonenumber']) ?></p>
        <p><?= htmlspecialchars($user['accountType']) ?></p>
    </div>

    <div class="main">

        <section class="main-section">
            <h1>Sales Overview <i class="fa-solid fa-chart-line"></i></h1>
        </section>

        <section class="role-section">
            <?php if ($_SESSION['role'] === "HR") { ?>
                <div class="role-box">
                    <h3>HR Access</h3>
                    <p>No Access | Confidential Data</p>
                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] === "Manager") { ?>
                <div class="role-box">
                    <h3>Daily Sales</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Daily Sales ID</th>
                                <th>Total Revenue</th>
                                <th>Total Items Sold</th>
                                <th>Total Transactions</th>
                                <th>Sales Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $dailySales->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['daily_id']) ?></td>
                                    <td><?= htmlspecialchars($row['total_revenue']) ?></td>
                                    <td><?= htmlspecialchars($row['total_items_sold']) ?></td>
                                    <td><?= htmlspecialchars($row['total_transactions']) ?></td>       
                                    <td><?= htmlspecialchars($row['sales_date']) ?></td>
                                    
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="role-box">
                    <h3>Weekly Sales</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Weekly Sales ID</th>
                                <th>Total Revenue</th>
                                <th>Total Items Sold</th>
                                <th>Total Transactions</th>
                                <th>Week Number</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $weeklySales->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['weekly_id']) ?></td>
                                    <td><?= htmlspecialchars($row['total_revenue']) ?></td>
                                    <td><?= htmlspecialchars($row['total_items_sold']) ?></td>
                                    <td><?= htmlspecialchars($row['total_transactions']) ?></td>
                                    <td><?= htmlspecialchars($row['week_number']) ?></td>
                                    <td><?= htmlspecialchars($row['year']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="role-box">
                    <h3>Monthly Sales</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Monthly Sales ID</th>
                                <th>Total Revenue</th>
                                <th>Total Items Sold</th>
                                <th>Total Transactions</th>
                                <th>Month</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $monthlySales->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['monthly_id']) ?></td>
                                    <td><?= htmlspecialchars($row['total_revenue']) ?></td>
                                    <td><?= htmlspecialchars($row['total_items_sold']) ?></td>
                                    <td><?= htmlspecialchars($row['total_transactions']) ?></td>
                                    <td><?= htmlspecialchars($row['month']) ?></td>
                                    <td><?= htmlspecialchars($row['year']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] === "Employee") { ?>
                <div class="role-box">
                    <h3>Employee Access</h3>
                    <p>No Access | Confidential Data</p>
                </div>
            <?php } ?>
        </section>
    </div>
</body>
<script src="design.js"></script>
</html>