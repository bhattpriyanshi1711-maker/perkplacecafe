<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request']);
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "perkplacedb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status'=>'error','message'=>'DB connection failed']));
}

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$fullname || !$email || !$phone || !$username || !$password) {
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (fullname,email,phone,address,username,password) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $fullname, $email, $phone, $address, $username, $hashed_password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['username'] = $username;
    $_SESSION['fullname'] = $fullname;

    echo json_encode(['status'=>'success','message'=>'Registration successful!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to register. Username or email may already exist.']);
}

$stmt->close();
$conn->close();
?>
