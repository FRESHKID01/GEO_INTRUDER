<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-intruder | Reset Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0e14; color: #00ffcc; font-family: 'Courier New', Courier, monospace; }
        .reset-card { background-color: #1a1d24; border: 1px solid #ff4d4d; border-radius: 15px; padding: 30px; margin-top: 100px; }
        .form-control { background-color: #0b0e14; border: 1px solid #333; color: #ff4d4d; }
        .btn-danger { background-color: #ff4d4d; border: none; font-weight: bold; color: #000; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="reset-card">
                <h3 class="text-center mb-4">RECOVERY MODE</h3>
                <p class="small text-secondary text-center">Enter your admin email to verify identity.</p>

                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'not_found'): ?>
                    <div class="alert alert-warning small py-1 text-center">Email not recognized in database.</div>
                <?php endif; ?>

                <form action="process_forgot.php" method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Admin Email" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">VERIFY EMAIL</button>
                </form>
                <div class="text-center mt-3"><a href="login.php" class="text-info small text-decoration-none">Back to Login</a></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>