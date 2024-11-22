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

// Wrapper for createDatabaseConnection
function getConnection() {
    return createDatabaseConnection(); // Calls your existing function
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

// Fetch Subject by Subject Code
function getSubjectByCode($subjectCode) {
    try {
        // Get the database connection
        $conn = getConnection();

        // SQL query to fetch the subject by subject code
        $sql = "SELECT * FROM subjects WHERE subject_code = :subject_code";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':subject_code', $subjectCode, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch and return the subject data
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);

        return $subject ? $subject : null; // If no subject is found, return null
    } catch (PDOException $exception) {
        // Return null in case of error
        return null;
    }
}

// Delete Subject
function deleteSubject($subjectCode, $redirectUrl) {
    try {
        // Get the database connection
        $conn = getConnection();

        // SQL query to delete the subject by subject code
        $sql = "DELETE FROM subjects WHERE subject_code = :subject_code";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':subject_code', $subjectCode, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the subject management page after successful deletion
            header("Location: $redirectUrl");
            exit(); // Ensure no further code is executed
        } else {
            // Return an error message if deletion fails
            return false;
        }
    } catch (PDOException $exception) {
        // Return false in case of any error
        return false;
    }
}

// Fetch Subjects
function fetchSubjects() {
    $conn = getConnection();

    try {
        $sql = "SELECT * FROM subjects";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $subjects;
    } catch (PDOException $e) {
        return [];
    }
}

// Add Subject function
function addSubject($subject_code, $subject_name) {
    $validateSubjectData = validateSubjectData($subject_code, $subject_name);
    $checkDuplicate = checkDuplicateSubjectData($subject_code, $subject_name);

    if (count($validateSubjectData) > 0) {
        echo displayErrors($validateSubjectData);
        return;
    }

    if (count($checkDuplicate) == 1) {
        echo displayErrors($checkDuplicate);
        return;
    }

    $conn = getConnection();

    try {
        $sql = "INSERT INTO subjects (subject_code, subject_name) VALUES (:subject_code, :subject_name)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':subject_code', $subject_code);
        $stmt->bindParam(':subject_name', $subject_name);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Failed to add subject.";
        }
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

// Validate Subject Data function
function validateSubjectData($subject_code, $subject_name ) {
    $errors = [];

    if (empty($subject_code)) {
        $errors[] = "Subject code is required.";
    }

    if (empty($subject_name)) {
        $errors[] = "Subject name is required.";
    }

    return $errors;
}

// Check if subject data already exists in the database
function checkDuplicateSubjectData($subject_code, $subject_name) {
    $conn = getConnection();
    try {
        $sql = "SELECT * FROM subjects WHERE subject_code = :subject_code OR subject_name = :subject_name";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':subject_code', $subject_code);
        $stmt->bindParam(':subject_name', $subject_name);

        $stmt->execute();

        $existingSubject = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingSubject) {
            return ["Subject with the given code or name already exists."];
        }
    } catch (PDOException $e) {
        return ["Database error: " . $e->getMessage()];
    }

    return [];
}

// Display errors
function displayErrors($errors) {
    if (empty($errors)) return "";

    $errorMessages = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Alert</strong><ul>';

    foreach ($errors as $error) {
        $errorMessages .= '<li>' . htmlspecialchars($error) . '</li>';
    }

    $errorMessages .= '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

    return $errorMessages;
}

function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Function to safely retrieve POST data by key
function postData($key) {
    return isset($_POST[$key]) ? $_POST[$key] : null;
}

// Function to update the subject
function updateSubject($subject_code, $subject_name, $redirectUrl) {
    $errors = validateSubjectData($subject_code, $subject_name);
    
    if (count($errors) > 0) {
        return displayErrors($errors);
    }
    
    $conn = getConnection();
    
    try {
        $sql = "UPDATE subjects SET subject_name = :subject_name WHERE subject_code = :subject_code";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':subject_name', $subject_name);
        $stmt->bindParam(':subject_code', $subject_code);

        if ($stmt->execute()) {
            header("Location: $redirectUrl");
            exit();
        } else {
            return "Failed to update the subject.";
        }
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

// Fetch all subjects count
function countAllSubjects() {
    $conn = getConnection();

    try {
        $sql = "SELECT COUNT(*) AS subject_count FROM subjects";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['subject_count'];
    } catch (PDOException $e) {
        return 0;
    }
}

// Fetch all students count
function countAllStudents() {
    $conn = getConnection();

    try {
        $sql = "SELECT COUNT(*) AS student_count FROM students";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['student_count'];
    } catch (PDOException $e) {
        return 0;
    }
}

// Calculate passed and failed students
function calculateTotalPassedAndFailedStudents() {
    $conn = getConnection();

    try {
        // Get the number of students who passed (assuming a passing grade of 50% or higher)
        $sqlPassed = "SELECT COUNT(*) AS passed FROM students WHERE average_grade >= 50";
        $stmt = $conn->prepare($sqlPassed);
        $stmt->execute();
        $passed = $stmt->fetch(PDO::FETCH_ASSOC)['passed'];

        // Get the number of students who failed
        $sqlFailed = "SELECT COUNT(*) AS failed FROM students WHERE average_grade < 50";
        $stmt = $conn->prepare($sqlFailed);
        $stmt->execute();
        $failed = $stmt->fetch(PDO::FETCH_ASSOC)['failed'];

        return [
            'passed' => $passed,
            'failed' => $failed
        ];
    } catch (PDOException $e) {
        return [
            'passed' => 0,
            'failed' => 0
        ];
    }
}
?>
