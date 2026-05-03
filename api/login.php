<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->contact, $data->password)) {
    echo json_encode([
        "success" => false,
        "error" => "Missing data"
    ]);
    exit;
}

$contact = trim($data->contact);
$password = $data->password;

// Search by email OR phone
$sql = "SELECT userId, fullName, email, phone, passwordHash, status 
        FROM users 
        WHERE email = ? OR phone = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $contact, $contact);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "error" => "User not found"
    ]);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["passwordHash"])) {
    echo json_encode([
        "success" => false,
        "error" => "Incorrect password"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "user" => [
        "userId" => $user["userId"],
        "fullName" => $user["fullName"],
        "email" => $user["email"],
        "phone" => $user["phone"],
        "contact" => $user["email"] ? $user["email"] : $user["phone"],
        "status" => $user["status"]
    ]
]);

$stmt->close();
$conn->close();
?>