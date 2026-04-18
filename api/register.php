<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->fullName, $data->contact, $data->password)) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$fullName = trim($data->fullName);
$contact = trim($data->contact);
$password = $data->password;

$checkSql = "SELECT userId FROM users WHERE email = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("s", $contact);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["error" => "This email or phone already exists"]);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insertSql = "INSERT INTO users (fullName, email, passwordHash, status) VALUES (?, ?, ?, 'active')";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("sss", $fullName, $contact, $passwordHash);

if ($stmt->execute()) {
    echo json_encode(["message" => "User registered successfully"]);
} else {
    echo json_encode(["error" => "Registration failed"]);
}

$stmt->close();
$conn->close();
?>