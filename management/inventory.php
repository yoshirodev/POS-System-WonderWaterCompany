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

    $inventoryresult = $conn->query("SELECT id, product_name, quantity, type, cost, image_path FROM inventory");

    // Main CRUD Operations
    if (isset($_POST["create"])) {
        $product_name = $_POST["product_name"];
        $cost = $_POST["cost"];
        $quantity = $_POST["quantity"];
        $type = $_POST["type"];

        $sql_insert = $conn->prepare("INSERT INTO `inventory` (`product_name`, `cost`, `quantity`, `type`) VALUES (?, ?, ?, ?)");
        $sql_insert->bind_param("ssss", $product_name, $cost, $quantity, $type);
        $sql_insert->execute();

        header("Location: inventory.php");
        exit();
    }

    if (isset($_POST["update"])) {
        $productID = $_POST["productID"];
        $product_name = $_POST["product_name"];
        $cost = $_POST["cost"];
        $quantity = $_POST["quantity"];
        $type = $_POST["type"];

        $sql_insert = $conn->prepare("UPDATE `inventory` SET `product_name` = ?, `cost` = ?, `quantity` = ?, `type` = ? WHERE `id` = ?");
        $sql_insert->bind_param("ssssi", $product_name, $cost, $quantity, $type, $productID);
        $sql_insert->execute();

        header("Location: inventory.php");
        exit();
    }

    if (isset($_POST["delete"])) { 
        $productID = $_POST["productID"]; 
        
        $sql_delete = $conn->prepare("DELETE FROM inventory WHERE id = ?"); 
        $sql_delete->bind_param("i", $productID); 
        $sql_delete->execute(); 
        header("Location: inventory.php"); 
        exit(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/logo.png">
    <title>Inventory</title>
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
        <?php if ($_SESSION['role'] === "Manager") { ?>
            <!-- MANAGER ONLY FEATURES HERE -->

            <h1>Manager Controls</h1>

            <div class="inv-role-box">
                <form method="POST" required>
                    <h3>Create</h3>

                    <div class="form-row">

                        <div class="form-group product">
                            <label>Product Name</label>
                            <input type="text" name="product_name" placeholder="required field" required>
                        </div>

                        <div class="form-group type">
                            <label>Type</label>
                            <input type="text" name="type" placeholder="required field" required>
                        </div>

                        <div class="form-group cost">
                            <label>Cost</label>
                            <input type="text" name="cost" placeholder="required field" required>
                        </div>

                        <div class="form-group quantity">
                            <label>Quantity</label>
                            <input type="text" name="quantity" placeholder="required field" required>
                        </div>

                    </div>

                    <button type="submit" name="create">Create</button>
                </form>
            </div>

            <div class="inv-control-box">
                <form method="POST">

                    <h3>Update Product Information</h3>

                    <div class="update-row">

                        <div class="update-group pid">
                            <label>Product ID</label>
                            <input type="text" name="productID" placeholder="enter product ID" required>
                        </div>

                        <div class="update-group product">
                            <label>Product Name</label>
                            <input type="text" name="product_name">
                        </div>

                        <div class="update-group type">
                            <label>Type</label>
                            <input type="text" name="type">
                        </div>

                        <div class="update-group cost">
                            <label>Cost</label>
                            <input type="text" name="cost">
                        </div>

                        <div class="update-group quantity">
                            <label>Quantity</label>
                            <input type="text" name="quantity">
                        </div>

                    </div>

                    <button type="submit" name="update">Update</button>

                </form>
            </div>          
        <?php } ?>

        <?php if ($_SESSION['role'] === "Employee") { ?>
            <!-- EMPLOYEE FEATURES HERE -->

            <h1>Employee Controls</h1>

            <div class="inv-role-box">
                <form method="POST" required>
                    <h3>Create</h3>

                    <div class="form-row">

                        <div class="form-group product">
                            <label>Product Name</label>
                            <input type="text" name="product_name" placeholder="required field" required>
                        </div>

                        <div class="form-group type">
                            <label>Type</label>
                            <input type="text" name="type" placeholder="required field" required>
                        </div>

                        <div class="form-group cost">
                            <label>Cost</label>
                            <input type="text" name="cost" placeholder="required field" required>
                        </div>

                        <div class="form-group quantity">
                            <label>Quantity</label>
                            <input type="text" name="quantity" placeholder="required field" required>
                        </div>

                    </div>

                    <button type="submit" name="create">Create</button>
                </form>
            </div>

            <div class="inv-control-box">
                <form method="POST">

                    <h3>Update Product Information</h3>

                    <div class="update-row">

                        <div class="update-group pid">
                            <label>Product ID</label>
                            <input type="text" name="productID" placeholder="enter product ID" required>
                        </div>

                        <div class="update-group product">
                            <label>Product Name</label>
                            <input type="text" name="product_name">
                        </div>

                        <div class="update-group type">
                            <label>Type</label>
                            <input type="text" name="type">
                        </div>

                        <div class="update-group cost">
                            <label>Cost</label>
                            <input type="text" name="cost">
                        </div>

                        <div class="update-group quantity">
                            <label>Quantity</label>
                            <input type="text" name="quantity">
                        </div>

                    </div>

                    <button type="submit" name="update">Update</button>

                </form>
            </div>
        <?php } ?>

        <section class="main-section">
            <h1>Inventory <i class="fa-solid fa-boxes-stacked"></i></h1>
            <div class="inventory-cards">
                <?php while ($row = $inventoryresult->fetch_assoc()): ?>
                    <div class="inv-card">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($row['image_path'] ?? 'https://image.shutterstock.com/image-photo/coriander-isolated-on-wood-background-260nw-1416953786.jpg') ?>" alt="product">
                        </div>
                        <h3>ID: <?= htmlspecialchars($row['id']) ?></h3>
                        <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                        <p>Stock: <?= htmlspecialchars($row['quantity']) ?></p>
                        <p>Cost: <?= htmlspecialchars($row['cost']) ?></p>
                        <p>Type: <?= htmlspecialchars($row['type']) ?></p>

                        <form method="POST">
                            <input type="hidden" name="productID" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="role-section">
            <?php if ($_SESSION['role'] === "HR") { ?>
                <div class="role-box">
                    <h3>HR Access</h3>
                    <!-- HR ONLY FEATURES HERE -->
                    <p>Read Only Access</p>
                </div>
            <?php } ?>
        </section>
    </div>
</body>
<script src="design.js"></script>
</html>