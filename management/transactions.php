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

    // main transaction system logics

    $inventoryresult = $conn->query("SELECT product_name FROM inventory");

    $transactionresult = $conn->query("SELECT * FROM transaction_log");

    $hasItems = false;

    $total = 0;

    $quantity = "";
    $stock = "";

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $hasItems = !empty($_SESSION['cart']);


    if (isset($_SESSION['alert'])) {
        echo "<script>alert('" . $_SESSION['alert'] . "');</script>";
        unset($_SESSION['alert']);
    }

    if (isset($_POST["addtocart"])) {

        $product = $_POST["products"];
        $quantity = $_POST["quantity"];

        $stmt = $conn->prepare("SELECT cost, quantity FROM inventory WHERE product_name = ?");
        $stmt->bind_param("s", $product);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $price = $data['cost'];
        $stock = $data['quantity'];

        if ($quantity > $stock) {
            $_SESSION['alert'] = "No Stock Available!";
            header("Location: transactions.php");
            exit();
        }

        $subtotal = $price * $quantity;

        $cartItem = [
            "product" => $product,
            "quantity" => $quantity,
            "price" => $price,
            "subtotal" => $subtotal
        ];

        $_SESSION['cart'][] = $cartItem;

        header("Location: transactions.php");
        exit();
    }

    foreach ($_SESSION['cart'] as $cartItem) {
        $total += $cartItem['subtotal'];
    }
        
    if (isset($_POST['delete_item'])) {

        $index = $_POST['item_index'];

        if (isset($_SESSION['cart'][$index])) {

            $item = $_SESSION['cart'][$index];

            if (isset($item['type']) && $item['type'] === "service") {

                $service_id = $item['service_id'];

                $stmt = $conn->prepare("UPDATE service_requests SET status='Pending' WHERE service_id=?");
                $stmt->bind_param("i", $service_id);
                $stmt->execute();
            }

            unset($_SESSION['cart'][$index]);
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']);

        header("Location: transactions.php");
        exit();
    }


    if (isset($_POST["checkout"])) {
        $paymethod = $_POST["paymethod"];
        $amount = (float) $_POST["amount"];
        $refnum = isset($_POST["refnum"]) ? $_POST["refnum"] : null;

        if ($paymethod === "") {
            $_SESSION['alert'] = "Please select a payment method!";
            header("Location: transactions.php");
            exit();
        }

        if (($paymethod === "GCash" || $paymethod === "Maya" || $paymethod === "MariBank") && empty($refnum)) {
            $_SESSION['alert'] = "Reference Number is required for e-wallet payments!";
            header("Location: transactions.php");
            exit();
        }

        if ($amount < $total) {
            $_SESSION['alert'] = "Insufficient Amount!";
            header("Location: transactions.php");
            exit();
        }

        $change = $amount - $total;

        $conn->begin_transaction();

        try {
            foreach ($_SESSION['cart'] as $cartItem) {

                $product = $cartItem['product'];
                $quantity = (int)$cartItem['quantity'];
                $price = (float)$cartItem['price'];
                $subtotal = (float)$cartItem['subtotal'];

                if (isset($cartItem['type']) && $cartItem['type'] === "service") {

                    $service_id = $cartItem['service_id'];

                    $stmt = $conn->prepare("UPDATE service_requests SET status='Done' WHERE service_id=?");
                    $stmt->bind_param("i", $service_id);
                    $stmt->execute();

                } else {
                    $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE product_name = ?");
                    $stmt->bind_param("is", $quantity, $product);
                    $stmt->execute();

                }

                $stmt = $conn->prepare("
                    INSERT INTO transaction_log 
                    (product_name, quantity, price, payment_method, amount_paid, change_amount, subtotal, reference_number, timestamp) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param("sidssdds", $product, $quantity, $price, $paymethod, $amount, $change, $subtotal, $refnum);
                $stmt->execute();
            }


            $conn->commit();

            unset($_SESSION['cart']);

            $_SESSION['alert'] = "Transaction Completed Successfully! Change: ₱" . number_format($change, 2);
            header("Location: transactions.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['alert'] = "Transaction Failed: " . $e->getMessage();
            header("Location: transactions.php");
            exit();
        }
    }

    $service_req = $conn->query("SELECT * FROM service_requests");

    if (isset($_POST['done_service'])) {

        $service_id = $_POST['service_id'];

        $stmt = $conn->prepare("SELECT service_ordered, price FROM service_requests WHERE service_id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();

        if ($service) {

            $cartItem = [
                "product" => $service['service_ordered'],
                "quantity" => 1,
                "price" => $service['price'],
                "subtotal" => $service['price'],
                "type" => "service",
                "service_id" => $service_id
            ];

            $_SESSION['cart'][] = $cartItem;

            $update = $conn->prepare("UPDATE service_requests SET status='Payment' WHERE service_id = ?");
            $update->bind_param("i", $service_id);
            $update->execute();
        }

        header("Location: transactions.php");
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
    <title>Transaction</title>
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
            <h1>Transactions <i class="fa-solid fa-receipt"></i></h1>

        </section>

        <section class="role-section">
            <?php if ($_SESSION['role'] === "HR") { ?>
                <div class="role-box">
                    <h3>HR Access</h3>
                    <p>No Access | Confidential Data</p>
                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] === "Manager") { ?>
                <div class="cart-box">
                    <h3>Cart Section</h3>
                    <p>Add the items to be sold | Payment section will appear after adding items</p>
                    <form method="POST">
                        <div class="cart-row">
                            <div class="cart-group products">
                                <label for="products">Products</label>
                                <select name="products" id="products-options">
                                    <option value="">--Select a product--</option>
                                    <?php while ($row = $inventoryresult->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['product_name']) ?>"><?= htmlspecialchars($row['product_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="cart-group quantity">
                                <label for="quantity">Quantity</label>
                                <input type="number" name="quantity" placeholder="Quantity" required>
                            </div>
                            <button type="submit" name="addtocart">Add to Cart</button>
                        </div>
                    </form>
                </div>

                <?php if ($hasItems): ?>
                    <div id="payment-box" class="payment-box">
                        <h3>Payment</h3>
                        <div class="payment-row">
                            <h4>Total: ₱<?= number_format($total, 2) ?></h4>
                            <form method="POST">
                                <div class="payment-row">
                                    <div class="payment-group payment-option-group" >
                                        <label for="paymethod">Payment Method</label>
                                        <select name="paymethod" id="payment-option" onchange="toggleRefnum()">
                                            <option value="">--Select Payment Method--</option>
                                            <option value="GCash">E-Wallet - GCash</option>
                                            <option value="Maya">E-Wallet - Maya</option>
                                            <option value="MariBank">E-Wallet - MariBank</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>
                                    <div class="payment-group amount" >
                                        <label for="amount">Amount</label>
                                        <input type="number" name="amount" placeholder="Amount" required>
                                    </div>
                                    <div class="payment-group refnum" id="ref_group" style="display:none">
                                        <label for="refnum">Reference Number</label>
                                        <input type="number" name="refnum" id="refnum" placeholder="Reference Number">
                                    </div>
                                    <button type="submit" name="checkout">Complete Transactions</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>  

                <div class="role-box">
                    <h3>Pending for Payment</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Cart Item ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['cart'])): ?>
                                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($item['product']) ?></td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td><?= htmlspecialchars($item['price']) ?></td>
                                        <td><?= htmlspecialchars($item['subtotal']) ?></td>

                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="item_index" value="<?= $index ?>">
                                                <button type="submit" name="delete_item">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="role-box">
                    <h3>Service Request Management</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Service ID</th>
                                <th>Client</th>
                                <th>Address</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Phone No.</th>
                                <th>Email</th>
                                <th>Time Requested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $service_req->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['service_id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td><?= htmlspecialchars($row['service_ordered']) ?></td>
                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['phone_no']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>

                                    <td>
                                        <?php if ($row['status'] === "Pending"): ?>
                                            <form method="POST">
                                                <input type="hidden" name="service_id" value="<?= $row['service_id'] ?>">
                                                <button type="submit" name="done_service">Done</button>
                                            </form>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="role-box">
                    <h3>Transaction Log</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>LogID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Payment Method</th>
                                <th>Amount Paid</th>
                                <th>Change</th>
                                <th>Subtotal</th>
                                <th>Reference Number</th>
                                <th>Time Stamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $transactionresult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['log_id']) ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                    <td><?= htmlspecialchars($row['amount_paid']) ?></td>
                                    <td><?= htmlspecialchars($row['change_amount']) ?></td>
                                    <td><?= htmlspecialchars($row['subtotal']) ?></td>
                                    <td><?= htmlspecialchars($row['reference_number']) ?></td>
                                    <td><?= htmlspecialchars($row['timestamp']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] === "Employee") { ?>
                <div class="cart-box">
                    <h3>Cart Section</h3>
                    <p>Add the items to be sold | Payment section will appear after adding items</p>
                    <form method="POST">
                        <div class="cart-row">
                            <div class="cart-group products">
                                <label for="products">Products</label>
                                <select name="products" id="products-options">
                                    <option value="">--Select a product--</option>
                                    <?php while ($row = $inventoryresult->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['product_name']) ?>"><?= htmlspecialchars($row['product_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="cart-group quantity">
                                <label for="quantity">Quantity</label>
                                <input type="number" name="quantity" placeholder="Quantity" required>
                            </div>
                            <button type="submit" name="addtocart">Add to Cart</button>
                        </div>
                    </form>
                </div>

                <?php if ($hasItems): ?>
                    <div id="payment-box" class="payment-box">
                        <h3>Payment</h3>
                        <div class="payment-row">
                            <h4>Total: ₱<?= number_format($total, 2) ?></h4>
                            <form method="POST">
                                <div class="payment-row">
                                    <div class="payment-group payment-option-group" >
                                        <label for="paymethod">Payment Method</label>
                                        <select name="paymethod" id="payment-option" onchange="toggleRefnum()">
                                            <option value="">--Select Payment Method--</option>
                                            <option value="GCash">E-Wallet - GCash</option>
                                            <option value="Maya">E-Wallet - Maya</option>
                                            <option value="MariBank">E-Wallet - MariBank</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>
                                    <div class="payment-group amount" >
                                        <label for="amount">Amount</label>
                                        <input type="number" name="amount" placeholder="Amount" required>
                                    </div>
                                    <div class="payment-group refnum" id="ref_group" style="display:none">
                                        <label for="refnum">Reference Number</label>
                                        <input type="number" name="refnum" id="refnum" placeholder="Reference Number">
                                    </div>
                                    <button type="submit" name="checkout">Complete Transactions</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>  

                <div class="role-box">
                    <h3>Pending for Payment</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Cart Item ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['cart'])): ?>
                                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($item['product']) ?></td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td><?= htmlspecialchars($item['price']) ?></td>
                                        <td><?= htmlspecialchars($item['subtotal']) ?></td>

                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="item_index" value="<?= $index ?>">
                                                <button type="submit" name="delete_item">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="role-box">
                    <h3>Service Request Management</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Service ID</th>
                                <th>Client</th>
                                <th>Address</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Phone No.</th>
                                <th>Email</th>
                                <th>Time Requested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $service_req->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['service_id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td><?= htmlspecialchars($row['service_ordered']) ?></td>
                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['phone_no']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>

                                    <td>
                                        <?php if ($row['status'] === "Pending"): ?>
                                            <form method="POST">
                                                <input type="hidden" name="service_id" value="<?= $row['service_id'] ?>">
                                                <button type="submit" name="done_service">Done</button>
                                            </form>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </section>
    </div>
</body>
<script src="design.js"></script>
</html>