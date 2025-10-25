<?php
session_start();
header('Content-Type: application/json');

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number
function validatePhone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);
    // Check if phone number is between 10-15 digits
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

// Response function
function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get POST data
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $productType = sanitizeInput($_POST['productType'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');
        $formSource = sanitizeInput($_POST['formSource'] ?? 'unknown');
        
        // Validation
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = "Name must be at least 2 characters long.";
        }
        
        if (empty($email) || !validateEmail($email)) {
            $errors[] = "Please provide a valid email address.";
        }
        
        if (empty($phone) || !validatePhone($phone)) {
            $errors[] = "Please provide a valid phone number (10-15 digits).";
        }
        
        if (empty($message) || strlen($message) < 10) {
            $errors[] = "Message must be at least 10 characters long.";
        }
        
        if (!empty($errors)) {
            sendResponse(false, "Validation errors: " . implode(" ", $errors));
        }
        
        // Get database connection
        $pdo = getDBConnection();
        
        // Insert into database
        $sql = "INSERT INTO contact_submissions (name, email, phone, product_type, message, form_source) 
                VALUES (:name, :email, :phone, :product_type, :message, :form_source)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':product_type' => $productType,
            ':message' => $message,
            ':form_source' => $formSource
        ]);
        
        if ($result) {
            // Send success response
            sendResponse(true, "Thank you for your inquiry! We will get back to you soon.", [
                'submission_id' => $pdo->lastInsertId()
            ]);
        } else {
            sendResponse(false, "Failed to submit your inquiry. Please try again.");
        }
        
    } catch (PDOException $e) {
        error_log("Database error in contact_handler: " . $e->getMessage());
        sendResponse(false, "A database error occurred. Please try again later.");
    } catch (Exception $e) {
        error_log("General error in contact_handler: " . $e->getMessage());
        sendResponse(false, "An error occurred while processing your request. Please try again.");
    }
} else {
    sendResponse(false, "Invalid request method.");
}
?>
