<?php
// Include the necessary files for functions and header
require_once '../../functions.php'; 
include '../partials/header.php'; 
include '../partials/side-bar.php'; 

// Define redirect paths for logout and dashboard
$logoutUrl = '../logout.php';
$dashboardUrl = '../dashboard.php';
$studentRegistrationUrl = '../student/register.php';
?>

<!-- Main Content Area -->
<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Add a New Subject</h3>

    <!-- Breadcrumb for Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" aria-current="page"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
        </ol>
    </nav>

    <?php
    // Handle form submission and subject insertion
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get posted form data
        $subjectCode = postData("subject_code");
        $subjectName = postData("subject_name");

        // Insert the subject into the database
        $result = addSubject($subjectCode, $subjectName);
        
        // Show an alert depending on the result
        if ($result) {
            echo showAlert('success', 'Subject added successfully!');
        } else {
            echo showAlert('error', 'Failed to add subject. Please try again.');
        }
    }
    ?>

    <!-- Add Subject Form Section -->
    <div class="card p-4 mb-5">
        <form method="POST">
            <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" required>
            </div>
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">Add Subject</button>
        </form>
    </div>

    <!-- List of Subjects -->
    <div class="card p-4">
        <h3 class="card-title text-center">Subject List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Fetch the subjects from the database
                $subjects = fetchSubjects(); 

                if (!empty($subjects)): 
                    foreach ($subjects as $subject): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                    <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                    <td>
                        <!-- Edit and Delete Buttons -->
                        <a href="edit.php?subject_code=<?= urlencode($subject['subject_code']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?subject_code=<?= urlencode($subject['subject_code']) ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No subjects found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include '../partials/footer.php'; // Include the footer
?>

<?php
// Function to display alert messages for success or error
function showAlert($type, $message) {
    $alertClass = $type === 'success' ? 'alert-success' : 'alert-danger';
    
    return "
    <div class='alert $alertClass alert-dismissible fade show' role='alert'>
        <strong>Notice:</strong> $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
    ";
}
?>
