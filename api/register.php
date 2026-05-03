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

$email = null;
$phone = null;

// Email or Algerian phone
if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
    $email = $contact;
} elseif (preg_match('/^(05|06|07)[0-9]{8}$/', $contact)) {
    $phone = $contact;
} else {
    echo json_encode(["error" => "Please enter a valid email or Algerian phone number"]);
    exit;
}

// Check duplicate only in the right column
if ($email !== "") {
    $checkSql = "SELECT userId FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $email);
} else {
    $checkSql = "SELECT userId FROM users WHERE phone = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $phone);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["error" => "This email or phone already exists"]);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insertSql = "INSERT INTO users (fullName, email, phone, passwordHash, status)
              VALUES (?, ?, ?, ?, 'active')";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("ssss", $fullName, $email, $phone, $passwordHash);

if ($stmt->execute()) {
    echo json_encode(["message" => "User registered successfully"]);
} else {
    echo json_encode([
        "error" => "Registration failed",
        "details" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>