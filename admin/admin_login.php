<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../asset/toast/toastr.min.css">
    <title>Admin Login</title>
</head>
<body class="bg-black w-full h-screen flex items-center justify-center">

<div class="bg-white rounded-[20px] shadow-lg p-6 w-full max-w-sm mx-auto">
    <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Admin Login</h2>
    
    <form action="process_admin_register.php" id="loginForm" class="flex flex-col space-y-4">
        <!-- Email Input -->
        <div>
            <label class="block text-gray-600 font-medium mb-1">Email:</label>
            <input type="email" name="email" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none">
        </div>

        <!-- Password Input -->
        <div>
            <label class="block text-gray-600 font-medium mb-1">Password:</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none">
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-md font-semibold hover:bg-blue-600 transition duration-300">
            Login
        </button>
    </form>
</div>

    

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
