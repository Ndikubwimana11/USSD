<?php
include_once 'menu1.php';
include_once 'db.php';

function isNotRegistered($phoneNumber, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM uzima_users WHERE phone = ?");
    $stmt->execute([$phoneNumber]);
    $count = $stmt->fetchColumn();
    return $count == 0;
}

// Check if 'from' and 'text' keys exist in the $_POST array
if(isset($_POST['from']) && isset($_POST['text'])) {
    $phoneNumber = $_POST['from'];
    $text = $_POST['text']; 

    $textArray = explode(" ", $text);

    if(isset($textArray[0]) && isset($textArray[1]) && isset($textArray[2]) && isset($textArray[3]) && isset($textArray[4])) {
        $name = $textArray[0];
        $national_id = $textArray[1];
        $phone = $textArray[2];
        $address = $textArray[3];
        $pin = $textArray[4];
        
        if(isNotRegistered($phoneNumber, $conn)) {
            $stmt = $conn->prepare("INSERT INTO uzima_users (name, national_id, phone, address, pin) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $national_id, $phoneNumber, $address, $pin])) {
                $stmt = $conn->prepare("SELECT * FROM uzima_users WHERE name = ?");
                $stmt->execute([$name]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    echo "END Thank you $name, you have been successfully registered!";
                } else {
                    echo "END Registration failed. Please try again later.";
                }
            } else {
                echo "END Registration failed. Please try again later.";
            }
        } else {
            echo "END User is already registered.";
        }
    } else {
        // If name or password is missing in the SMS, prompt the user to provide both
        echo "END Your SMS must contain name, national ID, phone, address, and PIN.";
    }
} else {
    // If 'from' or 'text' keys are missing in the $_POST array, prompt the user to provide them
    echo "END Please provide 'from' and 'text' parameters.";
}
?>
