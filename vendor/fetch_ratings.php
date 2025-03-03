<?php
require '../db/db.php'; 

$vendor_id = $_GET['vendor_id'] ?? 0;

// Get average rating and total reviews
$stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM ratings WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$rating_data = $stmt->fetch();

// Get individual reviews
$stmt = $pdo->prepare("SELECT r.rating, r.review, u.full_name AS user_name 
                       FROM ratings r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.vendor_id = ?");
$stmt->execute([$vendor_id]);
$reviews = $stmt->fetchAll();

$response = [
    "avg_rating" => round($rating_data['avg_rating'], 1),
    "total_reviews" => $rating_data['total_reviews'],
    "reviews" => $reviews
];

echo json_encode($response);
?>
