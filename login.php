<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-intruder | Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0e14; color: #00ffcc; font-family: 'Courier New', Courier, monospace; }
        .login-card { background-color: #1a1d24; border: 1px solid #00ffcc; border-radius: 15px; padding: 30px; margin-top: 100px; box-shadow: 0 0 20px rgba(0, 255, 204, 0.2); }
        .form-control { background-color: #0b0e14; border: 1px solid #333; color: #00ffcc; /* Changed from #fff to your theme green */
}
        .form-control:focus { border-color: #00ffcc; color: #070000; box-shadow: none; }
        .btn-cyber { background-color: #00ffcc; color: #0b0e14; font-weight: bold; border: none; }
        /* This targets the browser's autofill style */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus {
    -webkit-text-fill-color: #00ffcc !important; /* Your Green Text */
    -webkit-box-shadow: 0 0 0px 1000px #0b0e14 inset !important; /* Your Dark Background */
    transition: background-color 5000s ease-in-out 0s;
}
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="login-card">
                <h2 class="text-center mb-4">GEO-INTRUDER</h2>
                <p class="text-center small text-secondary mb-4">SECURE ACCESS GATEWAY</p>
                
                <form action="process_login.php" method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Admin Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-cyber w-100">AUTHORIZE ACCESS</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="signup.php" class="text-info small text-decoration-none">New Admin? Register</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>