<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->userId)) {
    echo json_encode(["error" => "Missing user ID"]);
    exit;
}

$userId = $data->userId;
$nomCommercial = $data->nomCommercial ?? "";
$deuxiemeNom = $data->deuxiemeNom ?? "";
$bio = $data->bio ?? "";
$bioDetaillee = $data->bioDetaillee ?? "";
$categoriePrincipal = $data->categoriePrincipal ?? "";
$ville = $data->ville ?? "";
$zoneTravail = $data->zoneTravail ?? "";
$telephone = $data->telephone ?? "";
$anneeExperience = $data->anneeExperience ?? 0;
$servicePropose = $data->servicePropose ?? "";
$telephoneContact = $data->telephoneContact ?? "";
$instagram = $data->instagram ?? "";
$facebook = $data->facebook ?? "";
$tiktok = $data->tiktok ?? "";

$sql = "INSERT INTO providerprofile 
(userId, nomCommercial, deuxiemeNom, bio, bioDetaillee, categoriePrincipal, ville, zoneTravail, telephone, anneeExperience, servicePropose, telephoneContact, instagram, facebook, tiktok)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "error" => "SQL prepare failed",
        "details" => $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "issssssssisssss",
    $userId,
    $nomCommercial,
    $deuxiemeNom,
    $bio,
    $bioDetaillee,
    $categoriePrincipal,
    $ville,
    $zoneTravail,
    $telephone,
    $anneeExperience,
    $servicePropose,
    $telephoneContact,
    $instagram,
    $facebook,
    $tiktok
);

if ($stmt->execute()) {
    echo json_encode(["message" => "Profile saved successfully"]);
} else {
    echo json_encode([
        "error" => "Profile save failed",
        "details" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>