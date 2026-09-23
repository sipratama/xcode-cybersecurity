<?php
include 'db.php';
header('Content-Type: application/json');

// Endpoint login (rentan SQL Injection)
if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $password = isset($_GET['password']) ? $_GET['password'] : '';

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(array(
            "status" => "success",
            "user_id" => $user['id'],
            "username" => $user['username'],
            "role" => $user['role']
        ));
    } else {
        echo json_encode(array("status" => "fail", "message" => "Invalid credentials."));
    }
}

// Endpoint user profile (rentan IDOR)
elseif (isset($_GET['action']) && $_GET['action'] == 'get_profile') {
    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

    $sql = "SELECT id, username, role FROM users WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(array("status" => "success", "profile" => $user));
    } else {
        echo json_encode(array("status" => "fail", "message" => "User not found."));
    }
} else {
    echo json_encode(array("error" => "Invalid action."));
}
?>

