
<?php

// Database connection settings
$serverName = "tcp:mycardiffmet.database.windows.net,1433";

$connectionOptions = [
    "Database" => "rb-db",
    "Uid" => getenv("DB_USERNAME"),
    "PWD" => getenv("DB_PASSWORD"),
    "Encrypt" => true,
    "TrustServerCertificate" => false,
    "LoginTimeout" => 30
];

// Check that the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.html");
    exit();
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate form data
if (
    empty($name) ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($password) < 8
) {
    header("Location: register.html?error=Please+enter+valid+details");
    exit();
}

// Check database credentials
if (!$connectionOptions['Uid'] || !$connectionOptions['PWD']) {
    error_log("Database credentials are missing.");
    header("Location: register.html?error=Database+configuration+error");
    exit();
}

// Check SQL Server extension
if (!function_exists('sqlsrv_connect')) {
    error_log("SQLSRV PHP extension is not installed.");
    header("Location: register.html?error=Server+configuration+error");
    exit();
}

// Connect to Azure SQL Database
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    error_log("Database connection failed: " . print_r(sqlsrv_errors(), true));
    header("Location: register.html?error=Database+connection+failed");
    exit();
}

// Hash the user's password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($hashedPassword === false) {
    sqlsrv_close($conn);
    header("Location: register.html?error=Registration+failed");
    exit();
}

// Insert user into database
$sql = "INSERT INTO shopusers (name, email, password)
        VALUES (?, ?, ?)";

$params = [
    $name,
    $email,
    $hashedPassword
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {

    // Log detailed database error privately
    error_log("Registration failed: " . print_r(sqlsrv_errors(), true));

    sqlsrv_close($conn);

    header("Location: register.html?error=Registration+failed");
    exit();
}

// Close database resources
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Redirect after successful registration
header("Location: success.php");
exit();

?>
