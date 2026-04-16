<?php
require 'conn.php'; // Database connection

header('Content-Type: application/json');

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $data = json_decode(file_get_contents('php://input'), true);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

    $response = ['success' => false];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email already exists
        $checkSql = "SELECT * FROM NewsletterMails WHERE Email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = 'Email already subscribed.';
        } else {
            // Insert email into the table
            $insertSql = "INSERT INTO NewsletterMails (Email) VALUES (?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("s", $email);

            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Failed to subscribe. Please try again.';
            }
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid email address.';
    }

    echo json_encode($response);
}
?>