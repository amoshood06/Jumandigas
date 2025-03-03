<?php 
require_once "../auth_check.php";
require '../db/db.php'; 

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

?>




<h2>Rate Your Vendor</h2>

<?php if (empty($orders)) : ?>
    <p>No orders to rate.</p>
<?php else : ?>
    <form id="ratingForm">
        <label for="order_id">Select Order:</label>
        <select name="order_id" id="order_id" required>
            <?php foreach ($orders as $order) : ?>
                <option value="<?= $order['id'] ?>">
                    Order #<?= $order['tracking_id'] ?> - Vendor: <?= $order['vendor_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="rating">Rating (1-5):</label>
        <input type="number" name="rating" id="rating" min="1" max="5" required>

        <label for="review">Review (optional):</label>
        <textarea name="review" id="review"></textarea>

        <button type="submit">Submit Rating</button>
    </form>

    <p id="message" style="display:none; color: green;"></p>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#ratingForm").submit(function (e) {
                e.preventDefault(); // Prevent page reload

                $.ajax({
                    type: "POST",
                    url: "submit_rating.php",
                    data: $(this).serialize(),
                    success: function (response) {
                        $("#message").text(response).show();
                        $("#ratingForm")[0].reset(); // Reset form fields
                    },
                    error: function () {
                        $("#message").text("Error submitting rating!").css("color", "red").show();
                    }
                });
            });
        });
    </script>
<?php endif; ?>
