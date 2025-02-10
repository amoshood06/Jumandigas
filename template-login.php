<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./asset/toast/toastr.min.css">
    <title>Login</title>
</head>
<body>

    <form id="loginForm">
        <label>Email:</label>
        <input type="email" name="email" id="email" required>

        <label>Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" id="loginButton">Login</button>
    </form>

    <script src="./asset/toast/jquery-3.7.1.min.js"></script>
    <script src="./asset/toast/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#loginForm").on("submit", function (e) {
                e.preventDefault(); 

                var formData = $(this).serialize(); 

                $.ajax({
                    type: "POST",
                    url: "process_login.php",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "timeOut": "3000"
                        };

                        if (response.status == "success") {
                            toastr["success"](response.message, "Login Successful");
                            setTimeout(function () {
                                window.location.href = response.redirect; 
                            }, 2000);
                        } else {
                            toastr["error"](response.message, "Login Failed");
                        }
                    },
                    error: function () {
                        toastr["error"]("Something went wrong!", "Error");
                    }
                });
            });
        });
    </script>
</body>
</html>
