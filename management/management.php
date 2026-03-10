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

    $query = "SELECT SUM(quantity) AS total_stock FROM inventory";
    $result = mysqli_query($conn, $query);

    $row = mysqli_fetch_assoc($result);
    $total_stock = $row['total_stock'];

    $accShow = $conn->query("SELECT accID, firstname, middlename, lastname, birthdate, gender, email, phonenumber, accountType, username FROM logindata");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <title>Dashboard</title>
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

        <!-- DASHBOARD SECTION (VISIBLE TO ALL) -->
        <section class="main-section">
            <h1>Dashboard <i class="fa-solid fa-chart-pie"></i></h1>
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
                    <h2>Account Management Table</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>accID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Birthdate</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Account Type</th>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $accShow->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['accID']) ?></td>
                                    <td><?= htmlspecialchars($row['lastname']) ?></td>
                                    <td><?= htmlspecialchars($row['firstname']) ?></td>
                                    <td><?= htmlspecialchars($row['middlename']) ?></td>
                                    <td><?= htmlspecialchars($row['birthdate']) ?></td>
                                    <td><?= htmlspecialchars($row['gender']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>     
                                    <td><?= htmlspecialchars($row['phonenumber']) ?></td>
                                    <td><?= htmlspecialchars($row['accountType']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="role-box">
                    <form id="container" action="registration.php" method="post">
                        <h2>Employee Account Registration Tool</h2>
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