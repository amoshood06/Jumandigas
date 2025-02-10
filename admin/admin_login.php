<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/toast/toastr.min.css">
    <title>Admin Login</title>
</head>
<body>


    <form action="process_admin_register.php" id="loginForm">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <script src="../asset/toast/jquery-3.7.1.min.js"></script>
    <script src="../asset/toast/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#loginForm").on("submit", function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "process_admin_login.php",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "3000" };

                        if (response.status === "success") {
                            toastr["success"](response.message, "Login Successful");
                            setTimeout(function () { window.location.href = response.redirect; }, 2000);
                        } else {
                            toastr["error"](response.message, "Login Failed");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
