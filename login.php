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

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(['status'=>'error','message'=>'Both fields are required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE username=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['status'=>'error','message'=>'Username not found']);
    exit;
}

$stmt->bind_result($id, $fullname, $hashed_password);
$stmt->fetch();

if (password_verify($password, $hashed_password)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['fullname'] = $fullname;

    echo json_encode(['status'=>'success','message'=>'Login successful!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Incorrect password']);
}

$stmt->close();
$conn->close();
?>
