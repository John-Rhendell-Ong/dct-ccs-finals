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

// Handles user login authentication
function authenticate($email, $password) {
    if (empty($email) || empty($password)) {
        return showAlert('error', "<li>Email and password are required.</li>");
    }

    if (!str_ends_with($email, '@gmail.com')) {
        return showAlert('error', "<li>Email format is invalid.</li>");
    }

    $db = dbConnect();
    $hashedPass = md5($password); // Simplistic hash for demonstration
    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $email, $hashedPass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user_email'] = $email;
        return true;
    }

    return showAlert('error', "<li>Invalid email or password.</li>");
}

// Logs out the current user
function performLogout($redirectUrl) {
    // Destroy the session
    session_unset();
    session_destroy();

    // Redirect to the specified URL after logging out
    header("Location: $redirectUrl");
    exit();
}

// Adds a new subject to the database
function addSubject($code, $name) {
    if (empty($code) || empty($name)) {
        return showAlert('error', "<li>Both subject code and name are required.</li>");
    }

    $db = dbConnect();
    $query = "SELECT COUNT(*) AS count FROM subjects WHERE subject_code = ? OR subject_name = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $code, $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc()['count'] > 0;

    if ($exists) {
        return showAlert('error', "<li>Duplicate subject code or name detected.</li>");
    }

    $insertQuery = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->bind_param("ss", $code, $name);

    if ($insertStmt->execute()) {
        return showAlert('success', "<li>Subject successfully added.</li>");
    }

    return showAlert('error', "<li>Failed to add subject: " . $insertStmt->error . "</li>");
}

// Fetch and display all subjects
function listSubjects() {
    $db = dbConnect();
    $query = "SELECT * FROM subjects";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['subject_code']) . '</td>';
            echo '<td>' . htmlspecialchars($row['subject_name']) . '</td>';
            echo '<td><a href="edit.php?code=' . urlencode($row['subject_code']) . '" class="btn btn-info">Edit</a> ';
            echo '<a href="delete.php?code=' . urlencode($row['subject_code']) . '" class="btn btn-danger">Delete</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3" class="text-center">No subjects found.</td></tr>';
    }
}

function guardDashboard() {
    // Check if user is logged in (you can modify this check based on your needs)
    if (!isset($_SESSION['user_email'])) {
        // Redirect to login page if not logged in
        header("Location: ../index.php");
        exit();
    }
}
?>
