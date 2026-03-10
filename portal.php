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
                $error = "Wrong username or password";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <title>Sign In</title>
</head>
<body>
    <a style="text-decoration: none;" href="index.html"><header>
        Wonder Water Equipment and Supplies Trading
    </header></a>
    
    <div class = "pageBox">
        <div class = "container"> 
        <form id="container" action="portal.php" method="post">
        <h1>LOG IN</h1>
        <label class = "text" for="username">Username</label>
        <input class = "inp" type="text" name="username" placeholder="Enter Username">
        <label class = "text" for="userpassword">Password</label>
        <input class = "inp" type="password" name="userpassword" placeholder="Enter Password">   

        <div class="error">
            <?php if(!empty($error)) { echo $error; } ?>
        </div>

        <input class = "click" type="submit">
    </form>
    </div>
    <div class = "design" ><img src="assets/logo.png" alt="">
            <h3>Welcome Back</h3>
        </div>
    </div>
</body>
<style>
    body{
        background-color:#1E293B;
        margin:0;
        font-family:Arial, Helvetica, sans-serif;
    }

    header{
        color:#ffffff;
        background-color:#0F172A;
        height:60px;
        font-size:20px;
        text-align:center;
        font-weight:bold;
        display:flex;
        align-items:center;
        justify-content:center;
        letter-spacing:1px;
        box-shadow:0 2px 10px rgba(0,0,0,0.3);
    }

    .pageBox{
        display:flex;
        justify-content:center;
        align-items:center;
        height:calc(100vh - 60px);
        gap:80px;
    }

    .container{
        height:480px;
        width:420px;
        background-color:#334155;
        border-radius:20px;
        box-shadow:0 10px 25px rgba(0,0,0,0.4);
        padding:40px;
    }

    h1{
        text-align:center;
        color:#ffffff;
        margin-bottom:60px;
        letter-spacing:2px;
    }

    .text{
        color:#E2E8F0;
        font-size:14px;
        margin-bottom:5px;
        display:block;
    }

    .inp{
        height:40px;
        width:100%;
        border-radius:8px;
        margin-bottom:20px;
        background-color:#1E293B;
        border:1px solid #000000;
        color:#E2E8F0;
        padding-left:10px;
    }

    .inp:focus{
        outline:none;
        box-shadow:0 0 8px #8ba1a5;
    }

    .click{
        height:40px;
        width:100%;
        border-radius:25px;
        background-color:#8ba1a5;
        border:none;
        color:#0F172A;
        font-weight:bold;
        cursor:pointer;
        transition:0.2s;
    }

    .click:hover{
        transform:scale(1.03);
        background-color:#718386;
    }

    .design{
        text-align:center;
    }

    .design img{
        height:300px;
        width:300px;
        object-fit:contain;
        filter:drop-shadow(0 10px 20px rgba(0,0,0,0.5));
    }

    h3{
        color:#E2E8F0;
        font-size:42px;
        letter-spacing:2px;
        margin-top:20px;
    }

    .error{
        color: red;
    }
</style>
</html>