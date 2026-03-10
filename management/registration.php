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

            header("Location: management.php");
            exit();
        }
    }
?>