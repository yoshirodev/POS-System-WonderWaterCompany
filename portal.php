<?php
    include "database/db.php";
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['userpassword'];

        if ($username != "" && $password != "") {
            $statement = $conn->prepare("SELECT * FROM logindata WHERE username = ?");
            $statement->bind_param("s", $username);
            $statement->execute();
            $result = $statement->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['userpassword'])) {
                $_SESSION['user_id'] = $user['accID'];
                $_SESSION['role'] = $user['accountType'];
                $_SESSION['name'] = $user['firstname'];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();

                header("Location: management/management.php");
                exit();
            } else {
                echo "Wrong email or password";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="portal.css">
    <title>Sign In</title>
</head>
<body>
    <header>
        Wonder Water Equipment and Supplies Trading
    </header>
    
    <div class = "pageBox">
        <div class = "container"> 
        <form id="container" action="portal.php" method="post">
        <h1>LOG IN</h1>
        <label class = "text" for="username">Username</label>
        <input class = "inp" type="text" name="username" placeholder="Enter Username">
        <label class = "text" for="userpassword">Password</label>
        <input class = "inp" type="password" name="userpassword" placeholder="Enter Password">   
        <input class = "click" type="submit">
    </form>
    </div>
    <div class = "design" ><img src="assets/logo.png" alt="">
            <h3>Welcome Back</h3>
        </div>
    </div>
</body>
</html>