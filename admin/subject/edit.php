<?php
include '../../functions.php'; // Include the functions
include '../partials/header.php';

// Pages
$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '../student/register.php';
$subjectPage = './add.php';
include '../partials/side-bar.php';

// Get the subject data based on subject code
$subject_data = getSubjectByCode($_GET['subject_code']);

// Handle form submission to update the subject
if (isPost()) {
    $subject_code = $subject_data['subject_code'];
    $subject_name = postData('subject_name');
    $updateStatus = updateSubject($subject_code, $subject_name, $subjectPage);
}
?>

<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Edit Subject</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
        </ol>
    </nav>

    <!-- Display Errors or Success Message -->
    <?php if (isset($updateStatus)) {
        echo showAlert($updateStatus);
    } ?>

    <!-- Edit Subject Form -->
    <div class="card p-4 mb-5">
        <form method="POST">
            <!-- Subject Code (disabled) -->
            <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= htmlspecialchars($subject_data['subject_code']) ?>" disabled>
            </div>

            <!-- Subject Name -->
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= htmlspecialchars($subject_data['subject_name']) ?>">
            </div>

            <!-- Update Subject Button -->
            <button type="submit" class="btn btn-primary btn-sm w-100">Update Subject</button>
        </form>
    </div>

</div>

<?php
include '../partials/footer.php';

// Function to show success or error alerts
function showAlert($message) {
    $alertType = (strpos($message, 'Error') === false) ? 'success' : 'danger';
    return '<div class="alert alert-' . $alertType . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}
?>
