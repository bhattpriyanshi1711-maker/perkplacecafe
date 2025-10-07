<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["loggedIn" => false]);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT fullname, email, phone, address, username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "loggedIn" => true,
        "name" => $user['fullname'],
        "email" => $user['email'],
        "phone" => $user['phone'],
        "address" => $user['address'],
        "username" => $user['username']
    ]);
} else {
    echo json_encode(["loggedIn" => false]);
}
