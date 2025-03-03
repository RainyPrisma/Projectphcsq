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
    <title>Account Management | Custom Seafoods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/CSS/account.css">
    <link rel="stylesheet" href="../Assets/CSS/ref.css">
    <link rel="icon" href="https://customseafoods.com/cdn/shop/files/CS_Logo_2_1000.webp?v=1683664967" type="image/png">
    <script src="../Assets/JS/disable-autocomplete.js"></script>
</head>
<body>
    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Account Management</h1>
                    <p class="mb-0 text-light">Update and manage your personal information</p>
                </div>
                <div>
                    <a href="ordercus_history.php" class="btn btn-light me-2">
                        <i class="fas fa-history me-1"></i> Order History
                    </a>
                    <a href="../Frontend/<?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'index.php' : 'dashboard.php'; ?>" class="btn btn-light me-2">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                    <a href="../Frontend/logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i> Your Profile Details</h5>
                <span class="badge bg-light text-dark" style="padding: 0.5rem 1rem; border-radius: 30px;">
                    <i class="fas fa-shield-alt me-1 text-success"></i> Secure Information
                </span>
            </div>
            <div class="card-body">
                <div id="message" class="mb-4"></div>
                
                <form id="userForm" class="row g-3">
                    <!-- Email Field -->
                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                               readonly>
                    </div>

                    <!-- Username Field -->
                    <div class="col-md-6">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" maxlength="50">
                    </div>

                    <!-- Full Name Field -->
                    <div class="col-12">
                        <label for="full_name" class="form-label">
                            <i class="fas fa-id-card"></i> Full Name
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name" maxlength="100">
                    </div>

                    <!-- Address Field -->
                    <div class="col-12">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>

                    <!-- City, State, Zip -->
                    <div class="col-md-4">
                        <label for="city" class="form-label">
                            <i class="fas fa-city"></i> City
                        </label>
                        <input type="text" class="form-control" id="city" name="city" maxlength="50">
                    </div>
                    <div class="col-md-4">
                        <label for="state" class="form-label">
                            <i class="fas fa-map"></i> State
                        </label>
                        <input type="text" class="form-control" id="state" name="state" maxlength="50">
                    </div>
                    <div class="col-md-4">
                        <label for="zip_code" class="form-label">
                            <i class="fas fa-mail-bulk"></i> Zip Code
                        </label>
                        <input type="text" class="form-control" id="zip_code" name="zip_code" maxlength="10">
                    </div>

                    <!-- Country Field -->
                    <div class="col-md-4">
                        <label for="country" class="form-label">
                            <i class="fas fa-globe"></i> Country
                        </label>
                        <input type="text" class="form-control" id="country" name="country" maxlength="50">
                    </div>

                    <!-- Gender Field -->
                    <div class="col-md-4">
                        <label for="gender" class="form-label">
                            <i class="fas fa-venus-mars"></i> Gender
                        </label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Phone Number Field -->
                    <div class="col-md-4">
                        <label for="phone_number" class="form-label">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" maxlength="255">
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-ocean btn-lg w-100">
                            <i class="fas fa-save me-2"></i> Update Account
                        </button>
                    </div>
                </form>
            </div>
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
                    
                    // Show welcome message
                    $('#message').html(`<div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Welcome back! Here you can update your profile information.
                    </div>`);
                } else {
                    $('#message').html(`<div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> ${response.message}
                    </div>`);
                }
            },
            error: function() {
                $('#message').html(`<div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> Error occurred while loading data
                </div>`);
            }
        });

        // Update user information
        $('#userForm').submit(function(e) {
            e.preventDefault();
            
            // Show loading message
            $('#message').html(`<div class="alert alert-info">
                <i class="fas fa-spinner fa-spin me-2"></i> Updating your information...
            </div>`);
            
            $.ajax({
                url: 'update_user.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#message').html(`<div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> ${response.message}
                        </div>`);
                    } else {
                        $('#message').html(`<div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i> ${response.message}
                        </div>`);
                    }
                },
                error: function() {
                    $('#message').html(`<div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> Error occurred while updating
                    </div>`);
                }
            });
        });
    });
    </script>
</body>
</html>