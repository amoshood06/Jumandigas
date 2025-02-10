<?php
require_once "auth_admin.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Upload Gas</title>
    <link rel="stylesheet" href="../asset/toast/toastr.min.css">
</head>
<body>
    <h2>Upload Gas Product</h2>

    <form id="uploadForm" enctype="multipart/form-data">
        <label>Gas Name:</label>
        <input type="text" name="gas_name" required><br>

        <label>Gas KG:</label>
        <input type="number" name="gas_kg" required><br>

        <label>Price Per KG:</label>
        <input type="number" step="0.01" name="price_per_kg" required><br>

        <label>Upload Image:</label>
        <input type="file" name="image" required><br>

        <button type="submit">Upload</button>
    </form>

    <script src="../asset/toast/jquery-3.7.1.min.js"></script>
    <script src="../asset/toast/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#uploadForm").on("submit", function (e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "upload_gas.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "timeOut": "3000"
                        };

                        if (response.status === "success") {
                            toastr["success"](response.message, "Upload Successful");
                            $("#uploadForm")[0].reset(); // Reset form
                        } else {
                            toastr["error"](response.message, "Upload Failed");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
