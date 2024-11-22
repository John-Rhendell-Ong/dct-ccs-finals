<?php
    // All project functions should be placed here

session_start();

function postData($key) {
    return $_POST[$key] ?? null;
}

function guardLogin() {
    if (isset($_SESSION['email'])) {
        header("Location: admin/dashboard.php");
        exit;
    }
}

function guardDashboard() {
    if (!isset($_SESSION['email'])) {
        header("Location: ../index.php");
        exit;
    }
}

function getConnection() {
    $host = 'localhost';
    $dbName = 'dct-ccs-finals';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    try {
        $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function login($email, $password) {
    $validateLogin = validateLoginCredentials($email, $password);

    if ($validateLogin) {
        echo displayErrors($validateLogin);
        return;
    }

    $conn = getConnection();
    $hashedPassword = md5($password);
    $query = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['email'] = $user['email'];
        header("Location: admin/dashboard.php");
        exit;
    } else {
        echo displayErrors(["Invalid email or password"]);
    }
}

function validateLoginCredentials($email, $password) {
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    return $errors ?: null;
}

function displayErrors($errors) {
    if (empty($errors)) return "";

    $errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>System Alerts</strong><ul>';
    
    foreach ($errors as $error) {
        $errorHtml .= '<li>' . htmlspecialchars($error) . '</li>';
    }

    $errorHtml .= '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    
    return $errorHtml;
}

function countAllSubjects() {
    try {
        $conn = getConnection();
        $sql = "SELECT COUNT(*) AS total_subjects FROM subjects";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_subjects'];
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function countAllStudents() {
    try {
        $conn = getConnection();
        $sql = "SELECT COUNT(*) AS total_students FROM students";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_students'];
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function calculateTotalPassedAndFailedStudents() {
    try {
        $conn = getConnection();
        $sql = "SELECT student_id, SUM(grade) AS total_grades, COUNT(subject_id) AS total_subjects FROM students_subjects GROUP BY student_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $passed = 0;
        $failed = 0;

        foreach ($students as $student) {
            $average_grade = $student['total_grades'] / $student['total_subjects'];
            $average_grade >= 75 ? $passed++ : $failed++;
        }

        return ['passed' => $passed, 'failed' => $failed];
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function addSubject($subject_code, $subject_name) {
    $validateSubjectData = validateSubjectData($subject_code, $subject_name);
    $checkDuplicate = checkDuplicateSubjectData($subject_code, $subject_name);

    if ($validateSubjectData) {
        echo displayErrors($validateSubjectData);
        return;
    }

    if ($checkDuplicate) {
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

function validateSubjectData($subject_code, $subject_name) {
    $errors = [];

    if (empty($subject_code)) {
        $errors[] = "Subject code is required.";
    }

    if (empty($subject_name)) {
        $errors[] = "Subject name is required.";
    }

    return $errors ?: null;
}

function checkDuplicateSubjectData($subject_code, $subject_name) {
    $conn = getConnection();
    $sql = "SELECT * FROM subjects WHERE subject_code = :subject_code OR subject_name = :subject_name";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':subject_code', $subject_code);
    $stmt->bindParam(':subject_name', $subject_name);
    $stmt->execute();

    $existing_subject = $stmt->fetch(PDO::FETCH_ASSOC);
    return $existing_subject ? ["Duplicate subject found."] : null;
}

function fetchSubjects() {
    $conn = getConnection();
    try {
        $sql = "SELECT * FROM subjects";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getSubjectByCode($subject_code) {
    $pdo = getConnection();
    $query = "SELECT * FROM subjects WHERE subject_code = :subject_code";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':subject_code' => $subject_code]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateSubject($subject_code, $subject_name, $redirectPage) {
    $validateSubjectData = validateSubjectData($subject_code, $subject_name);
    $checkDuplicate = checkDuplicateSubjectForEdit($subject_name);

    if ($validateSubjectData) {
        echo displayErrors($validateSubjectData);
        return;
    }

    if ($checkDuplicate) {
        echo displayErrors($checkDuplicate);
        return;
    }

    try {
        $pdo = getConnection();
        $sql = "UPDATE subjects SET subject_name = :subject_name WHERE subject_code = :subject_code";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':subject_name', $subject_name, PDO::PARAM_STR);
        $stmt->bindParam(':subject_code', $subject_code, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>window.location.href = '$redirectPage';</script>";
        } else {
            return 'Failed to update subject';
        }
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function deleteSubject($subject_code, $redirectPage) {
    try {
        $pdo = getConnection();
        $sql = "DELETE FROM subjects WHERE subject_code = :subject_code";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':subject_code', $subject_code, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>window.location.href = '$redirectPage';</script>";
        } else {
            return "Failed to delete subject.";
        }
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

function checkDuplicateSubjectForEdit($subject_name) {
    $pdo = getConnection();
    $query = "SELECT * FROM subjects WHERE subject_name = :subject_name";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':subject_name' => $subject_name]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ? ["Duplicate subject name found."] : null;
}
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Function to log out the user
function logout() {
    session_start();  // Start the session if not already started
    session_unset();  // Unset all session variables
    session_destroy();  // Destroy the session
    header("Location: login.php");  // Redirect to login page
    exit;  // Ensure no further code is executed
}
?>
