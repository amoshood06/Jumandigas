<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="./asset/toast/toastr.min.css">
    <link rel="shortcut icon" href="./asset/image/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</head>
<body>
    <div class="grid min-h-screen md:grid-cols-2">
        <!-- Left Column -->
        <div class="relative hidden bg-[#FAAF15] p-8 text-white md:block">
            <div class="space-y-15">
                <p class="text-sm text-zinc-400"><img src="./asset/image/logo.png" class="w-24" alt=""></p>
                
                <div class="relative h-80">
                    <img 
                        src="./asset/image/ads.png"
                        alt="Credit card preview"
                        class="object-contain w-full h-full"
                    />
                </div>

                <div class="space-y-5">
                    <h1 class="text-5xl font-semibold tracking-tight">
                    Join JumandiGas –<br />Fast & Reliable Gas Delivery!
                    </h1>
                    <p class="text-black font-semibold leading-[60px]">
                    Sign up now to enjoy seamless gas ordering and doorstep delivery. With JumandiGas, you get fast service, secure payments, and 24/7 support.
                        <br/>
                    🔹 Easy registration <br/>
                    🔹 Quick gas refills  <br/>
                    🔹 Safe & reliable delivery <br/>

                    ✅ Create your account now and order with ease! 🚀
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="flex items-center justify-center p-8">
            <div class="w-full max-w-lg space-y-8">
                <!-- Logo and Welcome -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-full bg-[#FAAF15] p-2">
                            <span class="block text-center text-sm font-bold text-white"><img src="./asset/image/logo.png" alt=""></span>
                        </div>
                        <h2 class="text-xl font-semibold">Jumandigas</h2>
                    </div>
                    <h1 class="text-3xl font-semibold tracking-tight">
                        Hi! Welcome to<br />Jumandigas 👋
                    </h1>
                </div>

                <!-- Registration Form -->
                <form action="process_register.php" id="registerForm" class="space-y-4">
                    <div class="space-y-2">
                        <label for="full_Name" class="text-sm font-medium">
                            Full Name
                        </label>
                        <input name="full_name" id="full_name" required
                            type="text" 
                            placeholder="John Doe"
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium">
                            Email
                        </label>
                        <input name="email"
                            type="email" 
                            id="email" 
                            placeholder="johndoe@gmail.com"
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            required
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="text-sm font-medium">
                            Phone Number
                        </label>
                        <input name="phone"
                            type="phone" 
                            id="phone" 
                            placeholder="+234 123 456 7890"
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            required
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="address" class="text-sm font-medium">
                            Address
                        </label>
                        <input name="address"
                            type="text" 
                            id="address" 
                            placeholder="123 Main St"
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            required
                        >
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="location" class="text-sm font-medium">
                                City
                            </label>
                            <input name="location"
                                type="text" 
                                id="location" 
                                placeholder="Lagos"
                                class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                                required
                            >
                        </div>
                        <div class="space-y-2">
                            <label for="state" class="text-sm font-medium">
                                State
                            </label>
                            <select name="state"
                                id="state" 
                                class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                                required
                            >
                                <option value="">Select State</option>
                                <option value="lagos">Lagos</option>
                                <option value="abuja">Abuja</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="role" class="text-sm font-medium">
                            Role
                        </label>
                        <select name="role"
                            id="role" 
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            required
                        >
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="vendor">Vendor</option>
                            <option value="rider">Rider</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium">
                            Password
                        </label>
                        <div class="relative">
                            <input name="password" 
                                type="password" 
                                id="password" 
                                placeholder="********"
                                class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                                required
                            >
                            <button 
                                type="submit" id="registerButton"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
                            >
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" y1="2" x2="22" y2="22"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full rounded-md bg-[#FAAF15] px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50"
                    >
                        Sign up
                    </button>
                </form>

                <!-- Additional Options -->
                <div class="space-y-4">
                    <div class="text-center text-sm">
                        Already have an account?
                        <a href="login.php" class="font-semibold text-[#FAAF15] hover:underline">
                            Sign In
                        </a>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <span class="w-full border-t"></span>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-white px-2 text-gray-500">Or with email</span>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <button 
                            type="button"
                            class="flex w-full items-center justify-center gap-2 rounded-md border border-gray-200 px-4 py-2 text-sm font-medium hover:bg-gray-50"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4"
                                />
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853"
                                />
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                    fill="#FBBC05"
                                />
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335"
                                />
                            </svg>
                            Sign up with Google
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="./asset/toast/jquery-3.7.1.min.js"></script>
    <script src="./asset/toast/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#registerForm").on("submit", function (e) {
                e.preventDefault(); 

                var formData = $(this).serialize(); 

                $.ajax({
                    type: "POST",
                    url: "process_register.php",
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
                            toastr["success"](response.message, "Registration Successful");
                            setTimeout(function () {
                                window.location.href = "login.php"; 
                            }, 2000);
                        } else {
                            toastr["error"](response.message, "Registration Failed");
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