<?php
session_start();
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$newEmail = $data["email"]??'';
$newPhone = $data["phone"]??'';
$newAddress = $data["address"]??'';


if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email"]);
    exit;
}

if (!preg_match("/^[0-9]{10}$/", $newPhone)) {
    echo json_encode(["success" => false, "message" => "Phone must be 10 digits"]);
    exit;
}

$sql = "UPDATE users SET email = ?, phone = ?, address = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $newEmail, $newPhone, $newAddress , $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}
