<?php
session_start();
include_once 'db.php';

class UzimaUSSD
{
    protected $text;
    protected $phoneNumber;
    protected $conn;

    public function __construct($text, $phoneNumber, $conn)
    {
        $this->text = $text;
        $this->phoneNumber = $phoneNumber;
        $this->conn = $conn;
    }

    public function mainMenuUnregistered()
    {
        $response = "CON Welcome to Uzima Chicken .\n";
        $response .= "1. Register\n";
        echo $response;
    }

    public function registerMenu($textArray)
    {
        $level = count($textArray);
        if ($level == 1) {
            echo "CON Enter your Names\n";
        } elseif ($level == 2) {
            echo "CON Enter your National ID\n";
        } elseif ($level == 3) {
            echo "CON Enter your Phone Number\n";
        } elseif ($level == 4) {
            echo "CON Enter your Address\n";
        } elseif ($level == 5) {
            echo "CON Enter PIN\n";
        } elseif ($level == 6) {
            echo "CON Confirm PIN\n";
        } elseif ($level == 7) {
            $name = $textArray[1];
            $nationalID = $textArray[2];
            $phone = $this->phoneNumber;
            $address = $textArray[4];
            $pin = $textArray[5];
            $confirmPin = $textArray[6];
            if ($pin != $confirmPin) {
                echo "END PINs do not match. Retry.";
            } else {
                $stmt = $this->conn->prepare("INSERT INTO uzima_users (name, national_id, phone, address, pin) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $nationalID, $phone, $address, $pin]);
                $_SESSION['userId'] = $this->conn->lastInsertId();
                echo "END Thank you, $name! You have been successfully registered.";
            }
        }
    }

    public function mainMenuRegistered()
    {
        $response = "CON Welcome back to Uzima Chicken .\n";
        $response .= "1. Order Chicken\n";
        $response .= "2. Update order\n";
        $response .= "3. Review Activity";
        echo $response;
    }

    public function orderChicken($textArray)
    {
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $level = count($textArray);

        try {
            if ($level == 1) {
                echo "CON Select Chicken Type:\n";
                echo "1. Meat Chicken\n";
                echo "2. Laying Chicken\n";
            } elseif ($level == 2) {
                $selectedOption = $textArray[1];
                if ($selectedOption == 1 || $selectedOption == 2) {
                    $_SESSION['selectedOption'] = $selectedOption;
                    echo "CON Enter Quantity:\n";
                } else {
                    echo "END Invalid option. Please try again.";
                }
            } elseif ($level == 3) {
                $selectedOption = $_SESSION['selectedOption'];
                $quantity = $textArray[2];
                if (!is_numeric($quantity) || $quantity <= 0) {
                    echo "END Invalid quantity. Please enter a valid quantity.";
                } else {
                    $_SESSION['quantity'] = $quantity;
                    echo "CON Enter your PIN to confirm the order:\n";
                }
            } elseif ($level == 4) {
                $pin = $textArray[3];
                if (empty($pin)) {
                    echo "END PIN cannot be empty. Please try again.";
                } else {
                    $stmt = $this->conn->prepare("SELECT ID AS user_id, pin FROM uzima_users WHERE phone = ?");
                    $stmt->execute([$this->phoneNumber]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($row && isset($row['pin'])) {
                        $pinFromDb = $row['pin'];
                        if ($pin === $pinFromDb) {
                            $_SESSION['userId'] = $row['user_id'];
                            $selectedOption = $_SESSION['selectedOption'];
                            $quantity = $_SESSION['quantity'];

                            $stmt = $this->conn->prepare("SELECT price FROM chicken_price WHERE type = ?");
                            $stmt->execute([$selectedOption == 1 ? 'Meat' : 'Laying']);
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($row && isset($row['price'])) {
                                $pricePerChicken = $row['price'];
                                $totalCost = $quantity * $pricePerChicken;

                                $stmt = $this->conn->prepare("INSERT INTO chicken_order (user_id, chicken_type, quantity, total_cost, delivery_status) VALUES (?, ?, ?, ?, 'Pending')");
                                $stmt->execute([$_SESSION['userId'], ($selectedOption == 1) ? 'Meat' : 'Laying', $quantity, $totalCost]);

                                echo "END Your order has been placed successfully. Total Cost: $totalCost Rwf\n";
                                echo "CON Please proceed with the payment of $totalCost Rwf via mobile money (momo).";
                            } else {
                                echo "END Failed to retrieve chicken price. Please try again later.";
                            }
                        } else {
                            echo "END Incorrect PIN. Please try again.";
                        }
                    } else {
                        echo "END PIN not found for the current user ID. Please try again later.";
                    }
                }
            }
        } catch (PDOException $e) {
            echo "END Error: " . $e->getMessage();
        }
    }

    public function updateOrderQuantity($textArray)
    {
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $level = count($textArray);
    
        try {
            if ($level == 1) {
                $stmt = $this->conn->prepare("SELECT * FROM chicken_order WHERE user_id = ? AND delivery_status = 'Pending'");
                $stmt->execute([$_SESSION['userId']]);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                if (!$orders) {
                    echo "END No pending orders found.";
                    return;
                }
    
                $_SESSION['currentOrders'] = $orders;
    
                echo "CON Select Order to Update Quantity:\n";
                foreach ($orders as $index => $order) {
                    echo ($index + 1) . ". " . $order['chicken_type'] . ": " . $order['quantity'] . "\n";
                }
            } elseif ($level == 2) {
                $selectedOrderIndex = intval($textArray[1]);
                $orders = $_SESSION['currentOrders'];
    
                if ($selectedOrderIndex < 1 || $selectedOrderIndex > count($orders)) {
                    echo "END Invalid order selection. Please try again.";
                    return;
                }
    
                $_SESSION['selectedOrderIndex'] = $selectedOrderIndex;
                echo "CON Enter new quantity for " . $orders[$selectedOrderIndex - 1]['chicken_type'] . ":\n";
            } elseif ($level == 3) {
                $newQuantity = intval($textArray[2]);
                $_SESSION['newQuantity'] = $newQuantity;
                echo "CON Enter your PIN to confirm the quantity update:\n";
            } elseif ($level == 4) {
                $pin = $textArray[3];
                $stmt = $this->conn->prepare("SELECT pin FROM uzima_users WHERE phone = ?");
                $stmt->execute([$this->phoneNumber]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if (!$row || $row['pin'] != $pin) {
                    echo "END Incorrect PIN. Please try again.";
                    return;
                }
    
                $selectedOrderIndex = $_SESSION['selectedOrderIndex'];
                $selectedOrder = $_SESSION['currentOrders'][$selectedOrderIndex - 1];
    
                $stmt = $this->conn->prepare("UPDATE chicken_order SET quantity = ? WHERE order_id = ?");
                $stmt->execute([$_SESSION['newQuantity'], $selectedOrder['order_id']]);
    
                echo "END Quantity updated successfully.";
            }
        } catch (PDOException $e) {
            echo "END Error: " . $e->getMessage();
        }
    }
    
    public function reviewActivity($textArray)
    {
        if (count($textArray) == 1) {
            echo "CON Enter your PIN to view order history:\n";
        } elseif (count($textArray) == 2) {
            $pin = $textArray[1];
    
            $stmt = $this->conn->prepare("SELECT * FROM uzima_users WHERE phone = ? AND pin = ?");
            $stmt->execute([$this->phoneNumber, $pin]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                $stmt = $this->conn->prepare("SELECT * FROM chicken_order WHERE user_id = ?");
                $stmt->execute([$user['ID']]); // Assuming ID is the primary key of the users table
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                if ($orders) {
                    $response = "CON Order History:\n";
                    foreach ($orders as $order) {
                        $response .= "Order ID: " . $order['order_id'] . "\n";
                        $response .= "Chicken Type: " . $order['chicken_type'] . "\n";
                        $response .= "Quantity: " . $order['quantity'] . "\n";
                        $response .= "Total Cost: " . $order['total_cost'] . " Rwf\n";
                        $response .= "Delivery Status: " . $order['delivery_status'] . "\n";
                        $response .= "------------------------\n";
                    }
                    echo $response;
                } else {
                    echo "END No order history found.";
                }
            } else {
                echo "END Incorrect PIN. Please try again.";
            }
        } else {
            echo "END Invalid input. Please try again.";
        }
    }
    
}
?>
