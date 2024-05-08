<?php

include_once 'menu1.php';
include_once 'db.php';
$sessionId = $_POST['sessionId'];
$phoneNumber = $_POST['phoneNumber'];
$serviceCode = $_POST['serviceCode'];
$text = $_POST['text'];

$select = $conn->prepare( "SELECT * FROM uzima_users WHERE phone= ?");

$select->execute([$phoneNumber]);

if($select->rowCount() > 0){
    $isRegistered = true;
}
else{
    $isRegistered = false;
}

$uzimaUSSD = new UzimaUSSD($text, $phoneNumber,$conn);
if ($text == "" && !$isRegistered) {
    $uzimaUSSD->mainMenuUnregistered(); 
} 
else if ($text == "" && $isRegistered) {
    $uzimaUSSD->mainMenuRegistered(); 
} 
else if (!$isRegistered) {
    // User is not registered, process registration menu
    $textArray = explode('*', $text);
    switch ($textArray[0]) {
        case 1:
            $uzimaUSSD->registerMenu($textArray,$phoneNumber);
            break;
        default:
            echo "END Invalid option. Please try again.";
    }
} 
else {
    $textArray = explode('*', $text);
    switch ($textArray[0]) {
        case 1:
            $uzimaUSSD->orderChicken($textArray);
            break;
        case 2:
            $uzimaUSSD->updateOrderQuantity($textArray);
            break;
        case 3:
            $uzimaUSSD->reviewActivity($textArray);
            break;
        default:
            echo "END Invalid choice. Please try again.";
    }
}

?>

