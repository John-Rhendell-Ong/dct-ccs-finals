<?php
// Include necessary files
include '../../functions.php'; // Functions for database and form handling
include '../partials/header.php'; // Header for the page

// Define navigation URLs
$logoutUrl = '../logout.php';
$dashboardUrl = '../dashboard.php';
$studentRegistrationUrl = '../student/register.php';
$subjectListUrl = './add.php'; // Subject List page

// Include sidebar for navigation
include '../partials/side-bar.php';

// Fetch the subject data based on the provided subject code from the URL
if (isset($_GET['subject_code'])) {
    $subjectCode = $_GET['subject_code'];
    $subject_data = getSubjectByCode($subjectCode);
} else {
    echo "Error: Subject code not provided.";
    exit; // If the subject code is not passed, stop execution
}

?>

<!-- Main Content Area -->
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

    <?php
    // Handle form submission to update the subject data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the posted subject name and use the subject code to update the database
        $subjectName = postData('subject_name');
        if (updateSubject($subjectCode, $subjectName)) {
            echo showAlert('success', 'Subject updated successfully!');
        } else {
            echo showAlert('error', 'Failed to update subject. Please try again.');
        }
    }
    ?>

    <!-- Edit Subject Form -->
    <div class="card p-4 mb-5">
        <form method="POST">
            <!-- Subject Code (disabled field, cannot be edited) -->
            <div class="mb-3">
                <label for="subject_code" class="form-label">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= htmlspecialchars($subject_data['subject_code']) ?>" disabled>
            </div>

            <!-- Subject Name -->
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= htmlspecialchars($subject_data['subject_name']) ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-sm w-100">Update Subject</button>
        </form>
    </div>

</div>

<?php
// Include footer for the page
include '../partials/footer.php';
?>
