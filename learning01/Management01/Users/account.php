<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

// Session Timeout Check
$session_timeout = 1800; // 30 minutes
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header("Location: ../Frontend/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/CSS/account.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <script src="../Assets/JS/disable-autocomplete.js"></script>
</head>
<body>
    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Account Management</h1>
                <div>
                    <a href="ordercus_history.php" class="btn btn-ocean me-2">Order History</a>
                    <a href="../Frontend/<?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'index.php' : 'dashboard.php'; ?>" class="btn btn-ocean me-2">Home</a>
                    <a href="../Frontend/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="account-container">
            <div id="message" class="mb-4"></div>
            
            <form id="userForm" class="row g-3">
                <!-- Email Field -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                           readonly>
                </div>

                <!-- Username Field -->
                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" maxlength="50">
                </div>

                <!-- Full Name Field -->
                <div class="col-12">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" maxlength="100">
                </div>

                <!-- Address Field -->
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>

                <!-- City, State, Zip -->
                <div class="col-md-4">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" maxlength="50">
                </div>
                <div class="col-md-4">
                    <label for="state" class="form-label">State</label>
                    <input type="text" class="form-control" id="state" name="state" maxlength="50">
                </div>
                <div class="col-md-4">
                    <label for="zip_code" class="form-label">Zip Code</label>
                    <input type="text" class="form-control" id="zip_code" name="zip_code" maxlength="10">
                </div>

                <!-- Country Field -->
                <div class="col-md-4">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country" name="country" maxlength="50">
                </div>

                <!-- Gender Field -->
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Phone Number Field -->
                <div class="col-md-4">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" maxlength="255">
                </div>

                <!-- Submit Button -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-ocean btn-lg w-100">Update Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Wave Decoration -->
    <div class="wave-decoration"></div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Load user data
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
                    $('#message').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function() {
                $('#message').html('<div class="alert alert-danger">Error occurred while loading data</div>');
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
                        $('#message').html(`<div class="alert alert-success">${response.message}</div>`);
                    } else {
                        $('#message').html(`<div class="alert alert-danger">${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#message').html('<div class="alert alert-danger">Error occurred while updating</div>');
                }
            });
        });
    });
    </script>
</body>
</html>