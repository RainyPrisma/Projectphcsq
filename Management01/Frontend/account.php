<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Management</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <style>
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; }
        .error { color: red; }
        .success { color: green; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logout-btn {
            padding: 8px 15px;
            background-color: #ff4444;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div id="account-form">
        <div class="header">
            <h2>Account Management</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
            <a href="index.php" class="home-btn">Home</a>
        </div>
        <div id="message"></div>
        
        <form id="userForm">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                    value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                    readonly>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" maxlength="50">
            </div>

            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" maxlength="100">
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" maxlength="50">
            </div>

            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" id="state" name="state" maxlength="50">
            </div>

            <div class="form-group">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" maxlength="10">
            </div>

            <div class="form-group">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" maxlength="50">
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" maxlength="255">
            </div>

            <button type="submit">Update Account</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        // Load user data automatically
        $.ajax({
            url: 'get_user.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    for (const [key, value] of Object.entries(response.data)) {
                        $(`#${key}`).val(value);
                    }
                } else {
                    $('#message').html('<div class="error">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#message').html('<div class="error">Error occurred while loading data</div>');
            }
        });

        // Update user information
        $('#userForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'update_user.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#message').html('<div class="success">' + response.message + '</div>');
                    } else {
                        $('#message').html('<div class="error">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#message').html('<div class="error">Error occurred while updating</div>');
                }
            });
        });
    });
    </script>
</body>
</html>