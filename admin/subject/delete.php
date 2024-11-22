<?php
// Include necessary files
require_once '../../functions.php'; // Include functions
include '../partials/header.php'; // Include header

// Define redirect paths for logout, dashboard, student registration, and subject management
$logoutUrl = '../logout.php';
$dashboardUrl = '../dashboard.php';
$studentRegistrationUrl = '../student/register.php';
$subjectManagementUrl = './add.php';
include '../partials/side-bar.php'; // Include sidebar

// Fetch subject data based on the subject code passed in the URL
$subjectCode = isset($_GET['subject_code']) ? $_GET['subject_code'] : null;
$subjectData = ($subjectCode) ? getSubjectByCode($subjectCode) : null;

// Handle form submission for subject deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $subjectData) {
    // Attempt to delete the subject and redirect after
    $deletionSuccess = deleteSubject($subjectData['subject_code'], $subjectManagementUrl);
    if ($deletionSuccess) {
        // Redirect after deletion
        header("Location: $subjectManagementUrl");
        exit();
    } else {
        // Show an error message if deletion fails
        $errorMessage = "Failed to 
        +delete subject. Please try again.";
    }
}

?>

<div class="col-md-9 col-lg-10">

    <h3 class="text-left mb-5 mt-5">Delete Subject</h3>

    <!-- Breadcrumb navigation for easy navigation back to previous pages -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $dashboardUrl ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $subjectManagementUrl ?>">Manage Subjects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <div class="border p-5">
        <!-- Confirmation message to verify the deletion of the subject -->
        <?php if ($subjectData): ?>
            <p class="text-left">Are you sure you want to delete the following subject record?</p>
            <ul class="text-left">
                <li><strong>Subject Code:</strong> <?= htmlspecialchars($subjectData['subject_code']) ?></li>
                <li><strong>Subject Name:</strong> <?= htmlspecialchars($subjectData['subject_name']) ?></li>
            </ul>

            <!-- Confirmation Form for deletion -->
            <form method="POST" class="text-left">
                <a href="<?= $subjectManagementUrl ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger">Delete Subject</button>
            </form>
        <?php else: ?>
            <!-- If the subject data is not found, show an error message -->
            <p class="text-danger">Subject not found or invalid subject code.</p>
        <?php endif; ?>
    </div>

</div>

<?php
include '../partials/footer.php'; // Include footer
?>
