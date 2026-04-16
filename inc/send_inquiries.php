<?php
// Include database connection
include 'conn.php';

// Function to sanitize input using filter_var
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Validate and sanitize input fields
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$tel = isset($_POST['tel']) ? sanitizeInput($_POST['tel']) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
$msg = isset($_POST['msg']) ? sanitizeInput($_POST['msg']) : '';

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Handle invalid email format
    // You might want to redirect back to the form with an error message
    header('Location: ../index.php?error=email');
    exit;
}

// Prepared statement to insert data into database
$stmt = $conn->prepare("INSERT INTO Inquiries (FullName, Mobile, Email, Subject, Msg) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $tel, $email, $subject, $msg);

// Execute the statement
if ($stmt->execute()) {
    // Redirect upon successful insertion
    header('Location: ../index.php');
} else {
    // Handle database error
    // You might want to redirect back to the form with an error message
    header('Location: ../index.php?error=db');
}

// Close statement and database connection
$stmt->close();
$conn->close();
