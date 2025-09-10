<?php
include('auth.php'); // Security check

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    if (in_array($status, ['Pending', 'Approved', 'Implemented'])) {
        $stmt = $conn->prepare("UPDATE suggestions SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        
        // --- EMAIL NOTIFICATION (PLACEHOLDER) ---
        // In a real application, you would add your email sending code here.
        // For example: include('send_email.php'); send_notification($id, $status);
    }
}

header("Location: index.php");
exit;
?>