<?php

session_start();
include "../database/db.php";

if (isset($_POST['accID'])) {
    $accID = mysqli_real_escape_string($conn, $_POST['accID']);
    
    
    $check_query = "SELECT * FROM logindata WHERE accID = '$accID'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        
        $delete_query = "DELETE FROM logindata WHERE accID = '$accID'";
        
        if (mysqli_query($conn, $delete_query)) {
            echo "success";
        } else {
            echo "error: " . mysqli_error($conn);
        }
    } else {
        echo "error: Employee not found";
    }
} else {
    echo "error: No ID provided";
}
?>