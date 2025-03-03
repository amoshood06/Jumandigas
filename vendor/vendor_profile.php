<?php
require '../db/db.php'; 

$vendor_id = $_GET['vendor_id'] ?? 0;

// Get vendor details
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch();

?>

<h2><?= $vendor['full_name'] ?> - Ratings</h2>
<p id="averageRating"></p>

<h3>Reviews:</h3>
<div id="reviewList"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadRatings() {
        $.ajax({
            url: "fetch_ratings.php?vendor_id=<?= $vendor_id ?>",
            type: "GET",
            dataType: "json",
            success: function (data) {
                $("#averageRating").html("Average Rating: " + data.avg_rating + "⭐ (" + data.total_reviews + " reviews)");
                
                let reviewHtml = "";
                data.reviews.forEach(function (review) {
                    reviewHtml += "<p><strong>" + review.user_name + ":</strong> " + review.rating + "⭐</p>";
                    reviewHtml += "<p>" + review.review + "</p><hr>";
                });
                $("#reviewList").html(reviewHtml);
            }
        });
    }

    $(document).ready(function () {
        loadRatings(); // Load ratings when page loads
    });
</script>
