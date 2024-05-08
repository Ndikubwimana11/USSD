<?php
include_once 'db.php';
session_start();

// Check if user is logged in
// if (!isset($_SESSION['username'])) {
//     header("Location: login.php");
//     exit;
// }

// Check if action is set
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $orderId = $_GET['orderId'];

    // Perform action based on the selected option
    if ($action == 'reject') {
        // Update the order status to rejected
        $sql = "UPDATE chicken_order SET delivery_status = 'rejected' WHERE order_id = :orderId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':orderId', $orderId);
        if ($stmt->execute()) {
            echo "Order rejected successfully.";
        } else {
            echo "Error rejecting order: " . $stmt->errorInfo()[2];
        }
    } elseif ($action == 'prove') {
        // Update the order status to proved
        $sql = "UPDATE chicken_order SET delivery_status = 'proved' WHERE order_id = :orderId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':orderId', $orderId);
        if ($stmt->execute()) {
            echo "Order proved successfully.";
        } else {
            echo "Error proving order: " . $stmt->errorInfo()[2];
        }
    }
}

// Query the pending orders
$sql = "SELECT * FROM chicken_order WHERE delivery_status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display the pending orders
if (count($orders) > 0) {
    foreach ($orders as $row) {
        echo "Order ID: " . $row["order_id"] . "<br>";
        echo "Customer ID: " . $row["user_id"] . "<br>";
        echo "Order Status: " . $row["delivery_status"] . "<br>";
        echo "<a href='dashboard.php?action=reject&orderId=" . $row["order_id"] . "'>Reject</a> ";
        echo "<a href='dashboard.php?action=prove&orderId=" . $row["order_id"] . "'>Prove</a>";
        echo "<hr>";
    }
} else {
    echo "No pending orders.";
}

// Close the database connection
$conn = null;
?>
