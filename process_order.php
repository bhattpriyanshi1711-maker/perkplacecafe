<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request');
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "perkplacedb";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_name = $_POST['name'];
$customer_email = $_POST['email'];
$customer_phone = $_POST['phone'];
$customer_address = $_POST['address'];

$cart_items = $_POST['cart_items']; 
$total_price = $_POST['total_price'];

$stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, items, total_price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssd", $customer_name, $customer_email, $customer_phone, $customer_address, $cart_items, $total_price);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to place order.']);
}

$stmt->close();
$conn->close();
?>
