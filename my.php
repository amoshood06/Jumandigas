<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./asset/toast/toastr.min.css">
    <title>Register</title>
</head>
<body>
    


<button type="button" id="button">Login</button>    
</body>
<script src="./asset/toast/jquery-3.7.1.min.js"></script>
<script src="./asset/toast/toastr.min.js"></script>
<script>
    $('#button').on('click', function(){
        toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
        }

        toastr["success"]("Registration is successful ", "Successful register")
    })
</script>
</html>