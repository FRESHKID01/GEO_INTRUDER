<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEO-intruder | Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0e14; color: #00ffcc; font-family: 'Courier New', Courier, monospace; }
        .signup-card { 
            background-color: #1a1d24; 
            border: 1px solid #00ffcc; 
            border-radius: 15px; 
            padding: 30px; 
            margin-top: 50px; 
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.2); 
        }
        .form-control { 
            background-color: #0b0e14 !important; 
            border: 1px solid #333; 
            color: #00ffcc !important; 
        }
        .form-control:focus { 
            border-color: #00ffcc; 
            box-shadow: 0 0 10px rgba(0, 255, 204, 0.5); 
        }
        /* Fix for Chrome Autofill background */
        input:-webkit-autofill {
            -webkit-text-fill-color: #00ffcc !important;
            -webkit-box-shadow: 0 0 0px 1000px #1a1d24 inset !important;
        }
        .btn-cyber { 
            background-color: #00ffcc; 
            color: #0b0e14; 
            font-weight: bold; 
            border: none; 
            transition: 0.3s; 
        }
        .btn-cyber:hover { background-color: #00ccaa; transform: scale(1.02); }
        .alert-cyber { background-color: #ff4d4d; color: white; border: none; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="signup-card">
                <h2 class="text-center mb-4">GEO-INTRUDER</h2>
                <p class="text-center small text-secondary mb-4">INITIALIZE NEW ADMIN PROFILE</p>

                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-cyber text-center mb-4">
                        <?php 
                            if($_GET['error'] == "email_exists") echo "⚠ ACCESS DENIED: Email already registered!";
                            if($_GET['error'] == "invalid_password") echo "⚠ ERROR: Password must be exactly 8 digits.";
                        ?>
                    </div>
                <?php endif; ?>
                
                <form action="process_signup.php" method="POST">
                    <div class="mb-3">
                        <label class="small text-secondary">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary">Admin Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary">Organization</label>
                        <input type="text" name="organization" class="form-control" placeholder="e.g. SQI" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary">Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Cyber Security" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary">Secure Pin (8 Digits Only)</label>
                        <input type="password" name="password" class="form-control" 
                               pattern="[0-9]{8}" 
                               maxlength="8"
                               placeholder="12345678" required>
                    </div>
                    
                    <button type="submit" class="btn btn-cyber w-100 py-2 mt-2">INITIALIZE ENCRYPTION</button>
                </form>
                
                <div class="text-center mt-4">
                    <a href="forgot_password.php" class="text-secondary small text-decoration-none d-block mb-1">Forgot Password?</a>
                    <a href="login.php" class="text-info small text-decoration-none">Existing Admin? Secure Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>