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
    <link rel="stylesheet" href="tools/style.css">
    <title>Profile Page</title>
</head>
<body>
    <div id="container">
        <h1>Profile</h1>
        <h2><?= htmlspecialchars($user['firstname'] . " " . $user['middlename'] . " " . $user['lastname']) ?></h2>
        <h2><?= htmlspecialchars($user['birthdate']) ?></h2>
        <h2><?= htmlspecialchars($user['email']) ?></h2>
        <h2>+63<?= htmlspecialchars($user['phonenumber']) ?></h2>
        <h2><?= htmlspecialchars($user['accountType']) ?></h2>

        <form action="../logout.php">
            <button>Logout</button>
        </form>
    </div>

    <div id="container">
        <!-- Put here the features that can be seen by all role-->

        <?php if ($_SESSION['role'] === "HR") { ?>
            <div style=" padding:10px; margin-top:10px;">
                <!-- Put here the features that can only be seen by HR -->
                <h1>HR</h1>


            </div>
        <?php } ?>

        <?php if ($_SESSION['role'] === "Manager") { ?>
            <div style=" padding:10px; margin-top:10px;">
                <!-- Put here the features that can only be seen by Manager -->
                <h1>Manager</h1>

            </div>
        <?php } ?>

        <?php if ($_SESSION['role'] === "Employee") { ?>
            <div style=" padding:10px; margin-top:10px;">
                <!-- Put here the features that can only be seen by Employee -->
                <h1>Employee</h1>

            </div>
        <?php } ?>
    </div>
</body>
</html>