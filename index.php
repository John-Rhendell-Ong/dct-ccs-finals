<?php
require_once 'functions.php'; // Ensure this path is correct

redirectIfLoggedIn(); // Redirect if the user is already logged in

$errorFeedback = ''; 

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = $_POST['email'];
    $userPass = $_POST['password'];

    // Authenticate the user
    $loginStatus = authenticateUser($userEmail, $userPass); // Updated function name

    if ($loginStatus === true) {
        // Redirect to the dashboard if login is successful
        header("Location: admin/dashboard.php");
        exit();
    } else {
        // Capture and display the error message if login fails
        $errorFeedback = $loginStatus; // The errorFeedback will contain HTML-formatted alert
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>User Login</title>
</head>

<body class="bg-light">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-4">
            <?php if ($errorFeedback): ?>
                <?php echo $errorFeedback; ?> <!-- Show error feedback -->
            <?php endif; ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3 fw-bold">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="<?php echo !empty($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <label for="email">Email Address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" value="<?php echo !empty($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
                            <label for="password">Password</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
