<?php
// Session initialization for the user
session_start();

// Helper function to retrieve POST data safely
function retrievePostData($key) {
    return isset($_POST[$key]) ? $_POST[$key] : null;
}

// Function to redirect to the dashboard if the user is already logged in
function redirectIfLoggedIn() {
    $dashboardURL = 'admin/dashboard.php';

    if (isset($_SESSION['email'])) {
        header("Location: $dashboardURL");
        exit(); // Ensure no further code is executed
    }
}

// Function to restrict access to the dashboard for non-logged-in users
function restrictDashboardAccess() {
    $loginPageURL = '../index.php';
    if (!isset($_SESSION['email'])) {
        header("Location: $loginPageURL");
        exit();
    }
}

// Establishes a connection to the database using PDO
function createDatabaseConnection() {
    $hostname = 'localhost';
    $databaseName = 'dct-ccs-finals';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    try {
        $dsn = "mysql:host=$hostname;dbname=$databaseName;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $exception) {
        die("Database connection failed: " . $exception->getMessage());
    }
}

// Function to authenticate the user login
function authenticateUser($email, $password) {
    // Validate email and password
    $validationErrors = validateUserLogin($email, $password);
    if (count($validationErrors) > 0) {
        echo showErrorMessages($validationErrors);
        return;
    }

    // Establish database connection
    $dbConnection = createDatabaseConnection();

    // Encrypt the password
    $encryptedPassword = md5($password);

    // Check if the email and password match a user in the database
    $query = "SELECT * FROM users WHERE email = :email AND password = :password";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':password', $encryptedPassword);
    
    $statement->execute();

    // Fetch user details if a match is found
    $user = $statement->fetch();
    if ($user) {
        $_SESSION['email'] = $user['email'];
        header("Location: admin/dashboard.php");
        exit(); // Stop further script execution after redirection
    } else {
        echo showErrorMessages(["Invalid email or password"]);
    }
}

// Function to validate login credentials (email and password)
function validateUserLogin($email, $password) {
    $errors = [];

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    return $errors;
}

// Function to display errors in a formatted alert box
function showErrorMessages($errors) {
    if (empty($errors)) return "";

    $errorMessages = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Alert</strong><ul>';

    foreach ($errors as $error) {
        $errorMessages .= '<li>' . htmlspecialchars($error) . '</li>';
    }

    $errorMessages .= '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

    return $errorMessages;
}

function performLogout($redirectTo) {
    // Remove the 'email' session data
    if (isset($_SESSION['email'])) {
        unset($_SESSION['email']);
    }

    // End the session completely
    session_destroy();

    // Redirect to the specified page
    header("Location: $redirectTo");
    exit();
}

function guardDashboard(){
    $loginPage = '../index.php';
    if(!isset($_SESSION['email'])){
        header("Location: $loginPage");
    }
}

function fetchSubjects() {
    // Establish the database connection
    $conn = getConnection();

    try {
        // SQL query to select all records from the subjects table
        $sql = "SELECT * FROM subjects";
        $stmt = $conn->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all results as an associative array
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the subjects array
        return $subjects;
    } catch (PDOException $e) {
        // Log the error message if needed (optional)
        // error_log($e->getMessage());

        // Return an empty array in case of error
        return [];
    }
}

?>