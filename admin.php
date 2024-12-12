<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('navbar.php'); ?>
    
    <div class="login-container">
        <h2>Admin Login</h2>
            <form id="login-form">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
                <p id="login-message" style="color: red; display: none;"></p>
            </form>
    </div>

<script>
    $(document).ready(function () {
        $('#login-form').on('submit', function (e) {
            e.preventDefault();

            const username = $('#username').val();
            const password = $('#password').val();

            $.ajax({
                url: 'controller.php',
                method: 'POST',
                data: {
                    action: 'login',
                    username: username,
                    password: password
                },
                success: function (response) {
                    if (response.success) {
                        window.location.href = 'adminDashboard.php';  // Redirect to admin dashboard
                    } else {
                        $('#login-message').text(response.message).show();
                    }
                },
                error: function () {
                    $('#login-message').text("An error occurred. Please try again.").show();
                }
            });
        });
    });
</script>
</body>
</html>