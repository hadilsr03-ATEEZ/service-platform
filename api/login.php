<?php
header("Content-Type: application/json");
include "connect.php";

// نجيب data من fetch
$data = json_decode(file_get_contents("php://input"));

// تحقق من البيانات
if (!$data || !isset($data->contact, $data->password)) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$contact = trim($data->contact);
$password = $data->password;

// نجيب user من database
$sql = "SELECT userId, fullName, email, passwordHash, status FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $contact);
$stmt->execute();
$result = $stmt->get_result();

// إذا ماكانش user
if ($result->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// تحقق من password
if (!password_verify($password, $user["passwordHash"])) {
    echo json_encode(["error" => "Incorrect password"]);
    exit;
}

// نجاح login
echo json_encode([
    "message" => "Login successful",
    "user" => [
        "userId" => $user["userId"],
        "fullName" => $user["fullName"],
        "email" => $user["email"],
        "status" => $user["status"]
    ]
]);

$stmt->close();
$conn->close();
?>