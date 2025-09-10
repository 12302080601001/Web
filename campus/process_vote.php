<?php
include('config.php');

header('Content-Type: application/json');

// Check if essential data is posted
if (!isset($_POST['suggestion_id']) || !isset($_POST['vote_type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data.']);
    exit;
}

$suggestion_id = intval($_POST['suggestion_id']);
$vote_type = $_POST['vote_type'];
$student_id = $_SESSION['student_id'];

if (!in_array($vote_type, ['upvote', 'downvote'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid vote type.']);
    exit;
}

// Check if the user has already voted
$stmt_check = $conn->prepare("SELECT vote_type FROM votes WHERE suggestion_id = ? AND student_id = ?");
$stmt_check->bind_param("is", $suggestion_id, $student_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already voted for this suggestion.']);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

// Record the new vote
$conn->begin_transaction();
try {
    // Insert into votes tracking table
    $stmt_insert = $conn->prepare("INSERT INTO votes (suggestion_id, student_id, vote_type) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("iss", $suggestion_id, $student_id, $vote_type);
    $stmt_insert->execute();
    $stmt_insert->close();

    // Update the suggestions table
    $column_to_update = ($vote_type === 'upvote') ? 'upvotes' : 'downvotes';
    $stmt_update = $conn->prepare("UPDATE suggestions SET $column_to_update = $column_to_update + 1 WHERE id = ?");
    $stmt_update->bind_param("i", $suggestion_id);
    $stmt_update->execute();
    $stmt_update->close();

    $conn->commit();

    // Fetch the new vote counts to send back to the client
    $stmt_fetch = $conn->prepare("SELECT upvotes, downvotes FROM suggestions WHERE id = ?");
    $stmt_fetch->bind_param("i", $suggestion_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result()->fetch_assoc();
    $stmt_fetch->close();

    echo json_encode([
        'success' => true,
        'upvotes' => $result_fetch['upvotes'],
        'downvotes' => $result_fetch['downvotes']
    ]);

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    error_log($exception->getMessage()); // Log error instead of showing to user
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
}
?>