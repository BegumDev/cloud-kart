
<?php
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MyShop</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 400px;
        }

        .header {
            text-align: center;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f8d7da;
            border-radius: 4px;
            text-align: center;
        }

        a {
            color: #007bff;
        }
    </style>
</head>

<body>

    <div class="register-form">

        <div class="header">
            <h1>MyShop</h1>
            <p>Create Your Account</p>
        </div>

        <h2>Register</h2>

        <?php if (!empty($error)): ?>
            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="process_register.php">

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="255"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    required
                >
            </div>

            <button type="submit" class="btn">
                Register
            </button>

        </form>

        <p style="text-align:center;">
            <a href="index.php">Back to Home</a>
        </p>

    </div>

</body>
</html>
