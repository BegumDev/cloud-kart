<?php

// Simple database connection
$serverName = "tcp:mycardiffmet.database.windows.net,1433";

$connectionOptions = array(
    "Database" => "myDatabase",
    "Uid" => "myadmin",
    "PWD" => "Abcdefgh0!",
    "Encrypt" => 1,
    "TrustServerCertificate" => 1
);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (!empty($name) && !empty($email) && !empty($password)) {

        // Connect to database
        $conn = sqlsrv_connect($serverName, $connectionOptions);

        if ($conn) {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $sql = "INSERT INTO shopusers (name, email, password) VALUES (?, ?, ?)";
            $params = array($name, $email, $hashed_password);

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt) {

                // Redirect to success page
                header("Location: success.php");
                exit();

            } else {

                // Get detailed database error
                $errors = sqlsrv_errors();
                $error_message = "Database error: ";

                if ($errors != null) {
                    foreach ($errors as $error) {
                        $error_message .= $error['message'] . " ";
                    }
                }

                header("Location: register.php?error=" . urlencode($error_message));
                exit();
            }

            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);

        } else {

            $connection_errors = sqlsrv_errors();
            $conn_error_message = "Database connection failed: ";

            if ($connection_errors != null) {
                foreach ($connection_errors as $error) {
                    $conn_error_message .= $error['message'] . " ";
                }
            }

            header("Location: register.php?error=" . urlencode($conn_error_message));
            exit();
        }

    } else {

        header("Location: register.php?error=Please+fill+all+fields");
        exit();
    }

} else {

    // Prevent direct access
    header("Location: register.php");
    exit();
}

?>
