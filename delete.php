<?php
session_start();
require('dbconnect.php');

header('Content-Type: application/json'); // Indicate JSON response

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acid'])) {
    $acid = $_POST['acid'];

    try {
        $conn = connect();
        if (!$conn) {
            throw new Exception("Connection failed.");
        }

        // Delete the row from the database
        $stmt = $conn->prepare("DELETE FROM Accounts WHERE ACID = :acid");
        $stmt->bindParam(':acid', $acid, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Account successfully deleted']);
        } else {
            // No rows affected, meaning the account wasn't found or already deleted
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'error', 'message' => 'Account not found or already deleted']);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => "Error: " . $e->getMessage()]);
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => "Error: " . $e->getMessage()]);
    } finally {
        if ($conn) {
            $conn = null; // Close the database connection
        }
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Request method not allowed or missing account ID.']);
}
?>