<?php
include('auth.php'); // Security check

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=suggestions.csv');

$output = fopen('php://output', 'w');

// Add header row
fputcsv($output, ['ID', 'Student ID', 'Name', 'Category', 'Suggestion', 'Upvotes', 'Downvotes', 'Status', 'Date Submitted']);

// Fetch data
$result = $conn->query("SELECT * FROM suggestions ORDER BY id ASC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
?>