<?php
header("Content-Type: application/json");
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"));
$user_id = $data->user_id;
$cart_items = $data->cart; // array of {product_id, quantity, price}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item->price * $item->quantity;
}

$sql = "INSERT INTO orders (user_id, total) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $conn->insert_id;

foreach ($cart_items as $item) {
    $sql2 = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("iiid", $order_id, $item->product_id, $item->quantity, $item->price);
    $stmt2->execute();
}

echo json_encode(["status" => "success", "order_id" => $order_id]);
?>
