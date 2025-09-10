<?php
$page_title = "Admin Dashboard";
include('auth.php'); // Check for login first
include(__DIR__ . '/../templates/header.php');

// --- Fetch Stats ---
$total_suggestions = $conn->query("SELECT COUNT(*) as count FROM suggestions")->fetch_assoc()['count'];
$pending_suggestions = $conn->query("SELECT COUNT(*) as count FROM suggestions WHERE status='Pending'")->fetch_assoc()['count'];
$approved_suggestions = $conn->query("SELECT COUNT(*) as count FROM suggestions WHERE status='Approved'")->fetch_assoc()['count'];
$implemented_suggestions = $conn->query("SELECT COUNT(*) as count FROM suggestions WHERE status='Implemented'")->fetch_assoc()['count'];

// Data for charts
$category_data = $conn->query("SELECT category, COUNT(*) as count FROM suggestions GROUP BY category");
$status_data = $conn->query("SELECT status, COUNT(*) as count FROM suggestions GROUP BY status");

$cat_labels = [];
$cat_counts = [];
while($row = $category_data->fetch_assoc()) {
    $cat_labels[] = $row['category'];
    $cat_counts[] = $row['count'];
}

$stat_labels = [];
$stat_counts = [];
while($row = $status_data->fetch_assoc()) {
    $stat_labels[] = $row['status'];
    $stat_counts[] = $row['count'];
}

// Fetch all suggestions for the table
$all_suggestions = $conn->query("SELECT * FROM suggestions ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>


<div class="row">
    <div class="col-md-3 mb-4"><div class="card text-white bg-primary h-100"><div class="card-body"><h3><?php echo $total_suggestions; ?></h3><p>Total Suggestions</p></div></div></div>
    <div class="col-md-3 mb-4"><div class="card text-white bg-warning h-100"><div class="card-body"><h3><?php echo $pending_suggestions; ?></h3><p>Pending</p></div></div></div>
    <div class="col-md-3 mb-4"><div class="card text-white bg-success h-100"><div class="card-body"><h3><?php echo $approved_suggestions; ?></h3><p>Approved</p></div></div></div>
    <div class="col-md-3 mb-4"><div class="card text-white bg-info h-100"><div class="card-body"><h3><?php echo $implemented_suggestions; ?></h3><p>Implemented</p></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Suggestions by Category</h5>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
         <div class="card">
            <div class="card-body">
                <h5 class="card-title">Suggestions by Status</h5>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Manage Suggestions</h4>
        <a href="export.php" class="btn btn-success"><i class="fas fa-file-csv"></i> Export to CSV</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Suggestion</th>
                        <th>Category</th>
                        <th>Submitted By</th>
                        <th>Votes</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $all_suggestions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['suggestion']); ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><i class="fas fa-thumbs-up text-success"></i> <?php echo $row['upvotes']; ?> | <i class="fas fa-thumbs-down text-danger"></i> <?php echo $row['downvotes']; ?></td>
                        <td><span class="badge status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                        <td>
                            <div class="btn-group">
                                <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Approved" class="btn btn-sm btn-success">Approve</a>
                                <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Implemented" class="btn btn-sm btn-info">Implement</a>
                                <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Pending" class="btn btn-sm btn-warning">Pend</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category Chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                label: 'Suggestions',
                data: <?php echo json_encode($cat_counts); ?>,
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1']
            }]
        }
    });

    // Status Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($stat_labels); ?>,
            datasets: [{
                label: 'Count',
                data: <?php echo json_encode($stat_counts); ?>,
                backgroundColor: ['#ffc107', '#198754', '#0dcaf0']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>

<?php include(__DIR__ . '/../templates/footer.php'); ?>