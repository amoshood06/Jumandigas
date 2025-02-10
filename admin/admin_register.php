<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/toast/toastr.min.css">
    <title>Admin Registration</title>
</head>
<body>

    <form action="process_admin_register.php" id="registerForm">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Phone:</label>
        <input type="text" name="phone" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <input type="hidden" name="role" value="admin">

        <button type="submit">Register</button>
    </form>

    <script src="../asset/toast/jquery-3.7.1.min.js"></script>
    <script src="../asset/toast/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#registerForm").on("submit", function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "process_admin_register.php",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "3000" };

                        if (response.status === "success") {
                            toastr["success"](response.message, "Success");
                            setTimeout(function () { window.location.href = "admin_login.php"; }, 2000);
                        } else {
                            toastr["error"](response.message, "Error");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
