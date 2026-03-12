<?php
    session_start();

    include "../database/db.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../portal.php");
        exit;
    }

    $_SESSION['last_activity'] = time();

    $userID = $_SESSION['user_id'];

    $statement = $conn->prepare("SELECT firstname, middlename, lastname, birthdate, email, phonenumber, accountType FROM logindata WHERE accID = ?");
    $statement->bind_param("i", $userID);
    $statement->execute();
    $result = $statement->get_result();

    if (!$result || $result->num_rows === 0) {
        echo "Profile not found.";
        exit;
    }

    $user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
  
    <title>Settings</title>
</head>
<body>
    <div class="sidebar">
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
            <h1>Settings <i class="fa-solid fa-gear"></i></h1>

        </section>

        <section class="role-section">
            <?php if ($_SESSION['role'] === "HR") { ?>
                <div class="role-box">
                    <h3>HR Controls</h3>
                   
                    <!-- HR ONLY FEATURES HERE -->

                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] === "Manager") { ?>
                <div class="role-box">
                    <h3>Manager Controls</h3>
                    <!-- MANAGER ONLY FEATURES HERE -->

                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] === "Employee") { ?>
                <div class="role-box">
                    <h3>Employee Tools</h3>
                    <!-- EMPLOYEE FEATURES HERE -->

                </div>
            <?php } ?>
        </section>
    </div>
</body>
<script src="design.js"></script>
</html>