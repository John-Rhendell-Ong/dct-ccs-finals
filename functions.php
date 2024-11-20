<?php

session_start(); // Initialize the session

// Establishes database connection
function dbConnect() {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $database = "dct-ccs-finals";
    $connection = new mysqli($host, $user, $pass, $database);
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    return $connection;
}

// Generates a dismissable alert for errors and success
function showAlert($type, $content) {
    $alertType = $type === 'error' ? 'alert-danger' : 'alert-success';
    $prefix = $type === 'error' ? '<strong>Error!</strong> ' : '<strong>Success!</strong> ';
    return '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">' .
           $prefix . $content .
           '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

// Redirects authenticated users to the appropriate page
function redirectIfLoggedIn() {
    if (!empty($_SESSION["user_email"])) {
        $redirectUrl = $_SESSION['redirect_to'] ?? '/admin/dashboard.php';
        header("Location: $redirectUrl");
        exit();
    }
}
?>
