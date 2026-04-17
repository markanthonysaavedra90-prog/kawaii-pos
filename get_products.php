<?php
include 'db.php';

$sql = "
SELECT 
    products.id,
    products.name,
    products.image,
    products.price,
    categories.name AS category,
    inventory.stock
FROM products
INNER JOIN categories ON products.category_id = categories.id
INNER JOIN inventory ON products.id = inventory.product_id
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $stock_status = ($row['stock'] <= 10) ? 'LOW' : 'OK';
    $row['status'] = $stock_status;
    $data[] = $row;
}

echo json_encode($data);
?>