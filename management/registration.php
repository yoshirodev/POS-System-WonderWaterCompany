<?php
    include "../database/db.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lastname = $_POST['lastname'] ?? '';
        $firstname = $_POST['firstname'] ?? '';
        $middlename = $_POST['middlename'] ?? '';
        $date = $_POST['birthdate'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $number = $_POST['phonenumber'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $userpassword = $_POST['userpassword'] ?? '';
        $hashedPassword = password_hash($userpassword, PASSWORD_BCRYPT);
        $accountType = $_POST['accountType'] ??'';

        if (!empty($lastname) && !empty($firstname) && !empty($email)) {
            $statement = $conn->prepare("INSERT INTO logindata (lastname, firstname, middlename, birthdate, gender, phonenumber, email, username, userpassword, accountType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->bind_param("ssssssssss", $lastname, $firstname, $middlename, $date, $gender, $number, $email, $username, $hashedPassword, $accountType);
            $statement->execute();
            header("Location: ../portal.php"); 
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Human Resources Manager</title>
</head>
<body>
    <form id="container" action="registration.php" method="post">
        <h1>HR - Employee Registration</h1>
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" placeholder="lastname">
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" placeholder="firstname">
        <label for="middlename">Middle Name</label>
        <input type="text" name="middlename" placeholder="middlename">
        <label for="birthdate">BirthDate</label>
        <input type="date" name="birthdate">
        <label for="gender">Gender</label>
        <select style="    padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 100%;" name="gender" id="gender">
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
        <select style="    padding: 10px border-radius: 5px; border: 1px solid #ccc; width: 100%;" name="accountType" id="accountType">
            <option value="Manager">Manager</option>
            <option value="Employee">Employee</option>
        </select>
        <input type="submit">
        <button><a href="../portal.php">Login</a></button>
    </form>
</body>
</html>