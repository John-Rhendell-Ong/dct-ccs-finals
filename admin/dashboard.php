<?php
require '../functions.php';
guardDashboard();

// Page redirects
$logoutPage = 'logout.php';
$subjectPage = './subject/add.php';
$studentPage = './student/register.php';

// Include header and sidebar partials
require './partials/header.php';
require './partials/side-bar.php';

// Get data for total subjects, students, and grade statistics
$totalSubjects = countAllSubjects();
$totalStudents = countAllStudents();
$gradeStatistics = calculateTotalPassedAndFailedStudents();
?>

<!-- Main Content Start -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Dashboard</h1>

    <div class="row mt-5">
        <!-- Total Subjects Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?= htmlspecialchars($totalSubjects) ?></h5>
                </div>
            </div>
        </div>

        <!-- Total Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?= htmlspecialchars($totalStudents) ?></h5>
                </div>
            </div>
        </div>

        <!-- Failed Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?= htmlspecialchars($gradeStatistics['failed']) ?></h5>
                </div>
            </div>
        </div>

        <!-- Passed Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?= htmlspecialchars($gradeStatistics['passed']) ?></h5>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Main Content End -->

<?php
// Include footer partial
require './partials/footer.php';
?>
