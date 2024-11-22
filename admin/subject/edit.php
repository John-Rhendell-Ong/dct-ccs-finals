<?php
// Include necessary files and functions
include '../../functions.php';
include '../partials/header.php';

// Redirect pages for logout and navigation
$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '../student/register.php';
$subjectPage = './add.php';

// Include sidebar
include '../partials/side-bar.php';

// Fetch subject data based on the subject code from the query string
$subjectCode = $_GET['subject_code'] ?? null;
$subjectData = null;

if ($subjectCode) {
    $subjectData = getSubjectByCode($subjectCode);
}

// Handle subject update when form is submitted
if (isPost() && $subjectData) {
    $subjectName = postData('subject_name');
    updateSubject($subjectData['subject_code'], $subjectName, './add.php');
}
?>

<!-- Content Area -->
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

    <!-- Edit Subject Form -->
    <?php if ($subjectData): ?>
        <div class="card p-4 mb-5">
            <form method="POST">
                <!-- Subject Code (disabled) -->
                <div class="mb-3">
                    <label for="subject_code" class="form-label">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= htmlspecialchars($subjectData['subject_code']) ?>" disabled>
                </div>

                <!-- Subject Name -->
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= htmlspecialchars($subjectData['subject_name']) ?>">
                </div>

                <!-- Update Subject Button -->
                <button type="submit" class="btn btn-primary btn-sm w-100">Update Subject</button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-danger">Subject not found or invalid subject code.</p>
    <?php endif; ?>

</div>

<?php
// Include footer partial
include '../partials/footer.php';
?>
