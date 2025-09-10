<?php
// --- THIS IS THE MOST IMPORTANT LINE ---
// It includes your configuration and starts the PHP session for the CAPTCHA.
include_once(__DIR__ . '/../config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'ADIT Suggestion Box'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/campus/style.css">
</head>
<body>
    <header class="app-header text-center mb-4">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center">
                 <img src="/campus/assets/adit.png" alt="ADIT Logo" height="80">
                 <img src="/campus/assets/cvm.png" alt="CVM Logo" height="80" class="mx-4">
            </div>
            <h1 class="mt-2">ADIT Suggestion Box Portal</h1>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/campus/index.php">Home</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/campus/submit.php">Submit Suggestion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/campus/suggestions.php">View Suggestions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/campus/admin/">Admin Panel</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">