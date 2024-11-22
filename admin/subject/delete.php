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

// Handle subject deletion when form is submitted
if (isPost() && $subjectData) {
    deleteSubject($subjectData['subject_code'], './add.php');
}
?>

<!-- Content Area -->
<div class="col-md-9 col-lg-10">
    <h3 class="text-left mb-5 mt-5">Delete Subject</h3>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <!-- Confirmation Section -->
    <div class="border p-5">
        <?php if ($subjectData): ?>
            <p class="text-left">Are you sure you want to delete the following subject record?</p>
            <ul class="text-left">
                <li><strong>Subject Code:</strong> <?= htmlspecialchars($subjectData['subject_code']) ?></li>
                <li><strong>Subject Name:</strong> <?= htmlspecialchars($subjectData['subject_name']) ?></li>
            </ul>

            <!-- Confirmation Form -->
            <form method="POST" class="text-left">
                <a href="add.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger">Delete Subject Record</button>
            </form>
        <?php else: ?>
            <p class="text-danger">Subject not found or invalid subject code.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer partial
include '../partials/footer.php';
?>
