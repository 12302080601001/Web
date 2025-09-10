<?php
$page_title = "Welcome - ADIT Suggestion Box";
include('templates/header.php');
?>

<div class="p-5 mb-4 bg-light rounded-3 text-center">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Welcome to the Suggestion Portal</h1>
        <p class="fs-4">Your ideas can help shape our campus. Share your suggestions or vote on others.</p>
        <a href="submit.php" class="btn btn-primary btn-lg mx-2">Submit a Suggestion</a>
        <a href="suggestions.php" class="btn btn-secondary btn-lg mx-2">View Suggestions</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h2 class="text-center mb-4">Top 5 Suggestions This Month</h2>
        <?php
        // Fetch top 5 suggestions based on upvotes in the last 30 days
        $top_query = "SELECT * FROM suggestions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY (upvotes - downvotes) DESC LIMIT 5";
        $top_result = $conn->query($top_query);
        if ($top_result->num_rows > 0) {
            echo "<ul class='list-group'>";
            while ($row = $top_result->fetch_assoc()) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo htmlspecialchars($row['suggestion']);
                echo "<span class='badge bg-success rounded-pill'><i class='fas fa-thumbs-up'></i> " . $row['upvotes'] . "</span>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='text-center text-muted'>No suggestions submitted this month yet.</p>";
        }
        ?>
    </div>
</div>


<?php include('templates/footer.php'); ?>