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

function fetchStudents() {
    // Use the existing getConnection function to establish a connection to the database
    $dbConnection = getConnection();

    try {
        // SQL query to retrieve all student records
        $query = "SELECT * FROM students";
        $stmt = $dbConnection->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch all student records as an associative array
        $studentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the student records
        return $studentRecords;
    } catch (PDOException $exception) {
        // In case of an error, return an empty array
        return [];
    }
}

function getStudentPassFailCounts() {
    try {
        // Get the database connection using the existing getConnection function
        $dbConnection = getConnection();

        // SQL query to calculate total grades and subject counts for each student
        $query = "SELECT student_id, 
                         SUM(grade) AS total_grades, 
                         COUNT(subject_id) AS subject_count 
                  FROM students_subjects 
                  GROUP BY student_id";

        // Prepare and execute the query
        $stmt = $dbConnection->prepare($query);
        $stmt->execute();

        // Fetch all the results as an associative array
        $studentData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize counters for passed and failed students
        $studentsPassed = 0;
        $studentsFailed = 0;

        // Iterate through each student's data
        foreach ($studentData as $data) {
            $averageGrade = $data['total_grades'] / $data['subject_count'];

            // Count the student as passed or failed based on average grade
            if ($averageGrade >= 75) {
                $studentsPassed++;
            } else {
                $studentsFailed++; 
            }
        }

        // Return an array with the pass and fail counts
        return [
            'passed' => $studentsPassed,
            'failed' => $studentsFailed
        ];
    } catch (PDOException $exception) {
        // Return an error message if something goes wrong
        return "Error: " . $exception->getMessage();
    }
}

function removeSubject($subjectCode, $redirectUrl) {
    try {
        // Establish a database connection
        $pdoConnection = getConnection();

        // Prepare the SQL query to delete the subject
        $query = "DELETE FROM subjects WHERE subject_code = :subject_code";
        $stmt = $pdoConnection->prepare($query);

        // Bind the subject code parameter
        $stmt->bindParam(':subject_code', $subjectCode, PDO::PARAM_STR);

        // Execute the deletion query
        if ($stmt->execute()) {
            // Redirect to the given page after successful deletion
            header("Location: $redirectUrl");
            exit;
        } else {
            // Return an error message if deletion fails
            return "Failed to delete the subject with code $subjectCode.";
        }
    } catch (PDOException $exception) {
        // Return the error message in case of a database exception
        return "Database error: " . $exception->getMessage();
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

?>
