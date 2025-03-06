<?php
require '../db/db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $track_id = $_POST['track_id'];

    if (empty($track_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Tracking ID is required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM riders WHERE track_id = ?");
    $stmt->bind_param("s", $track_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tracking_info = $result->fetch_assoc();

        if ($tracking_info['canceled_by_rider']) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Order has been canceled by the rider'
            ]);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Tracking details found',
            'data' => [
                'rider_id' => $tracking_info['rider_id'],
                'latitude' => $tracking_info['latitude'],
                'longitude' => $tracking_info['longitude'],
                'created_at' => $tracking_info['created_at']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Tracking ID']);
    }

    $stmt->close();
    $conn->close();
}
?>
