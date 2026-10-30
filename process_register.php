
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

// Redirect helper
function redirectWithError($message) {
    header("Location: register.php?error=" . urlencode($message));
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

// Get submitted form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate inputs
if (
    $name === '' ||
    strlen($name) > 100 ||
    strlen($email) > 255 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($password) < 8 ||
    strlen($password) > 4096
) {
    redirectWithError("Please enter valid details. Password must be at least 8 characters.");
}

// Check database credentials
if (
    !$connectionOptions['Uid'] ||
    !$connectionOptions['PWD']
) {
    error_log("Database credentials are missing.");
    redirectWithError("Database configuration error.");
}

// Check SQL Server extension
if (!function_exists('sqlsrv_connect')) {
    error_log("SQLSRV extension is not installed.");
    redirectWithError("Server configuration error.");
}

// Connect to Azure SQL
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    error_log("Database connection error: " . print_r(sqlsrv_errors(), true));
    redirectWithError("Unable to connect to database.");
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($hashedPassword === false) {
    sqlsrv_close($conn);
    redirectWithError("Unable to process registration.");
}

// SQL INSERT query
$sql = "INSERT INTO shopusers (name, email, password)
        VALUES (?, ?, ?)";

$params = [
    $name,
    $email,
    $hashedPassword
];

// Execute query
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {

    // Log detailed errors privately
    error_log("Registration error: " . print_r(sqlsrv_errors(), true));

    sqlsrv_close($conn);

    redirectWithError("Registration failed. Please check your details or try again.");
}

// Close database resources
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Redirect after successful registration
header("Location: success.php");
exit();

?>
