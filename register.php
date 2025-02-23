<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <script>
    const states = {
        nigeria: [
            { name: "Lagos", cities: ["Ikeja", "Surulere", "Lekki"] },
            { name: "Abuja", cities: ["Gwarimpa", "Maitama", "Asokoro"] },
            { name: "Rivers", cities: ["Port Harcourt", "Obio Akpor", "Eleme"] }
        ],
        ghana: [
            { name: "Greater Accra", cities: ["Accra", "Tema", "Madina"] },
            { name: "Ashanti", cities: ["Kumasi", "Obuasi", "Ejisu"] },
            { name: "Northern", cities: ["Tamale", "Yendi", "Savelugu"] }
        ]
    };

    const currencies = {
        nigeria: "₦",
        ghana: "₵"
    };

    function updateStates() {
        let country = document.getElementById("country").value;
        let stateDropdown = document.getElementById("state");
        let cityDropdown = document.getElementById("city");
        let currencyDropdown = document.getElementById("currency");

        stateDropdown.innerHTML = '<option value="">Select State</option>';
        cityDropdown.innerHTML = '<option value="">Select City</option>';
        currencyDropdown.innerHTML = '<option value="">Select Currency</option>';

        if (states[country]) {
            states[country].forEach(state => {
                let option = document.createElement("option");
                option.value = state.name.toLowerCase();
                option.textContent = state.name;
                stateDropdown.appendChild(option);
            });

            let currencyOption = document.createElement("option");
            currencyOption.value = currencies[country];
            currencyOption.textContent = currencies[country];
            currencyDropdown.appendChild(currencyOption);
        }
    }

    function updateCities() {
        let country = document.getElementById("country").value;
        let state = document.getElementById("state").value;
        let cityDropdown = document.getElementById("city");

        cityDropdown.innerHTML = '<option value="">Select City</option>';

        if (states[country]) {
            let selectedState = states[country].find(s => s.name.toLowerCase() === state);
            if (selectedState) {
                selectedState.cities.forEach(city => {
                    let option = document.createElement("option");
                    option.value = city.toLowerCase();
                    option.textContent = city;
                    cityDropdown.appendChild(option);
                });
            }
        }
    }
    </script>
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
                <form id="registerForm" class="space-y-4">
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

                    <div class="space-y-2">
                        <select name="country" id="country" onchange="updateStates()" required>
                            <option value="">Select Country</option>
                            <option value="nigeria">Nigeria</option>
                            <option value="ghana">Ghana</option>
                        </select>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="state" class="text-sm font-medium">State</label>
                            <select name="state" id="state" 
                                class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" 
                                onchange="updateCities()">
                                <option value="">Select State</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="city" class="text-sm font-medium">City</label>
                            <select name="city" id="city" 
                                class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                <option value="">Select City</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="currency" class="text-sm font-medium">Currency</label>
                        <select name="currency" id="currency" 
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                            <option value="">Select Currency</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="role" class="text-sm font-medium">role</label>
                        <select name="role" id="role" 
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
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
                                id="password" 
                                type="password"
                                class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                                required
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <span id="eyeIcon" class="hidden">👁️</span>
                                <span id="eyeOffIcon">🙈</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <button 
                            type="submit"
                            class="w-full py-3 text-sm font-medium text-white bg-[#FAAF15] rounded-md hover:bg-[#FA9A16]"
                        >
                            Register
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="./asset/toast/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#registerForm").on("submit", function (e) {
                e.preventDefault();  // Prevent the form from submitting the traditional way

                var formData = $(this).serialize();  // Serialize form data

                $.ajax({
                    type: "POST",
                    url: "process_register.php",  // Your PHP file to handle registration
                    data: formData,
                    dataType: "json",  // Expect JSON response
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
                                window.location.href = "login.php";  // Redirect after success
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
