<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-intruder | New Credentials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0e14; color: #00ffcc; font-family: monospace; }
        .reset-card { background-color: #1a1d24; border: 1px solid #00ffcc; padding: 30px; margin-top: 100px; }
        .form-control { background-color: #0b0e14; border: 1px solid #333; color: #00ffcc; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="reset-card text-center">
                <h4>RESET SECURE PIN</h4>
                <form action="update_password.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $_GET['uid']; ?>">
                    <input type="password" name="new_pass" class="form-control mb-3" pattern="[0-9]{8}" maxlength="8" placeholder="Enter New 8-Digit Pin" required>
                    <button type="submit" class="btn btn-success w-100">UPDATE DATABASE</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>