<?php
header("Content-Type: application/json");
include("../config/db.php");

$user_id = $_GET['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($order = $result->fetch_assoc()) {
    $order_id = $order['id'];
    $items = [];
    $res2 = $conn->query("SELECT p.name, oi.quantity, oi.price 
                          FROM order_items oi 
                          JOIN products p ON p.id = oi.product_id 
                          WHERE oi.order_id = $order_id");
    while ($item = $res2->fetch_assoc()) {
        $items[] = $item;
    }
    $order['items'] = $items;
    $orders[] = $order;
}

echo json_encode(["status" => "success", "orders" => $orders]);
?>
