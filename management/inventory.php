<?php
    session_start();

    include "../database/db.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../portal.php");
        exit;
    }

    if (time() - $_SESSION['last_activity'] > 10) {
        session_unset();
        session_destroy();
        header("Location: ../portal.php");
        exit();
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
    <title>Dashboard</title>
</head>
<body>
    <div class="sidebar">
        <a href="management.php">Dashboard</a>
        <a href="inventory.php">Inventory</a>
        <a href="sales.php">Sales Overview</a>
        <a href="transactions.php">Transactions</a>
        <a href="settings.php">Settings</a>
    </div>

    <div class="topbar">
        <div class="profile-btn" onclick="toggleProfile()">
            Profile
        </div>

        <button onclick="window.location.href='../logout.php'" class="logout-btn">
            Logout
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
            <h1>Inventory</h1>

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