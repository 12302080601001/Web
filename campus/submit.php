<?php
// ALL NECESSARY CODE IS NOW IN THIS ONE FILE TO GUARANTEE IT WORKS

// --- Step 1: Start the Session (from config.php) ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Step 2: Database Connection (from config.php) ---
$host = "localhost";
$user = "root";
$password = "ERROR"; // Change if you have a password
$database = "adit_suggestion_box";
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    // If the database fails, we should stop here.
    die("Connection failed: " . $conn->connect_error);
}

// --- Step 3: CAPTCHA and Form Logic (from submit.php) ---
$message = '';
$error = '';

// Only generate a new question if one doesn't already exist.
if (!isset($_SESSION['captcha_answer'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['num1'] = $num1;
    $_SESSION['num2'] = $num2;
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Force both values to be numbers (integers) before comparing.
    if (isset($_POST['captcha'], $_SESSION['captcha_answer']) && (int)$_POST['captcha'] === (int)$_SESSION['captcha_answer']) {
        $student_id = trim($_POST['student_id']);
        $name = !empty(trim($_POST['name'])) ? trim($_POST['name']) : 'Anonymous';
        $category = $_POST['category'];
        $suggestion_text = trim($_POST['suggestion']);

        $stmt = $conn->prepare("INSERT INTO suggestions (student_id, name, category, suggestion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $student_id, $name, $category, $suggestion_text);

        if ($stmt->execute()) {
            $message = "Suggestion submitted successfully!";
            unset($_SESSION['captcha_answer'], $_SESSION['num1'], $_SESSION['num2']);
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Incorrect CAPTCHA answer. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Suggestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> </head>
<body class="container my-5">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Submit Your Suggestion</h2>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (empty($message)) : ?>
                    <form method="POST" action="submit.php">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student ID (Optional)</label>
                            <input type="text" class="form-control" id="student_id" name="student_id">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name (Leave blank for Anonymous)</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="Academics">Academics</option>
                                <option value="Canteen">Canteen</option>
                                <option value="Hostel">Hostel</option>
                                <option value="Library">Library</option>
                                <option value="Sports">Sports</option>
                                 <option value="Facilities">Other Facilities</option>
                                     
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="suggestion" class="form-label">Suggestion</label>
                            <textarea class="form-control" id="suggestion" name="suggestion" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="captcha" class="form-label">Security Question: What is <?php echo $_SESSION['num1']; ?> + <?php echo $_SESSION['num2']; ?>?</label>
                            <input type="number" class="form-control" id="captcha" name="captcha" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary">Submit Suggestion</button>
                        </div>
                    </form>
                    <?php else: ?>
                        <div class="text-center">
                            <a href="submit.php" class="btn btn-primary">Submit Another Suggestion</a>
                            <br><br>
                            <a href="index.php" class="btn btn-secondary">Go to Homepage</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>