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

    $query = "SELECT SUM(quantity) AS total_stock FROM inventory";
    $result = mysqli_query($conn, $query);

    $row = mysqli_fetch_assoc($result);
    $total_stock = $row['total_stock'];
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

        <!-- DASHBOARD SECTION (VISIBLE TO ALL) -->
        <section class="main-section">
            <h1>Dashboard</h1>
            <div class="dashboard-cards">
                <div class="card">
                    <h2>Daily Sales</h2>
                    <p>₱0.00</p>
                </div>
                <div class="card">
                    <h2>Overall Stock</h2>
                    <p><?php echo $total_stock; ?> Items</p>
                </div>
            </div>
        </section>

        <section class="role-section">
            <?php if ($_SESSION['role'] === "HR") { ?>
                <div class="role-box">
                    <h3>HR Management</h3>
                    <!-- HR ONLY FEATURES HERE -->
                    <form id="container" action="registration.php" method="post">
                        <h1>Employee Account Registration Tool</h1>
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" placeholder="lastname">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" placeholder="firstname">
                        <label for="middlename">Middle Name</label>
                        <input type="text" name="middlename" placeholder="middlename">
                        <label for="birthdate">BirthDate</label>
                        <input type="date" name="birthdate">
                        <label for="gender">Gender</label>
                        <select style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%;" name="gender" id="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <label for="phonenumber">Phone Number</label>
                        <input type="tel" id="phonenumber" name="phonenumber" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="123-456-7890">
                        <label for="email">Email</label>
                        <input type="email" name="email" placeholder="email">
                        <label for="username">Username</label>
                        <input type="username" name="username" placeholder="username">
                        <label for="userpassword">Password</label>
                        <input type="password" name="userpassword" placeholder="password">
                        <label for="role">Account Type</label>
                        <select style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%;" name="accountType" id="accountType">
                            <option value="Manager">Manager</option>
                            <option value="Employee">Employee</option>
                        </select>
                        <input type="submit">
                        <button><a href="../portal.php">Login</a></button>
                    </form>
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