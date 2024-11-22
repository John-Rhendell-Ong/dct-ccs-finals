<?php
    // User logout procedure
    require_once '../functions.php';
    
    // Define the page to redirect after logout
    $redirectToHome = '../index.php';
    
    // Call the logout function and pass the redirect page
    performLogout($redirectToHome);
?>
