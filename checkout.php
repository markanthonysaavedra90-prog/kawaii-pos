<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$conn->begin_transaction();

try {

    // create sale
    $conn->query("INSERT INTO sales (total) VALUES (0)");
    $sale_id = $conn->insert_id;

    $total = 0;

    foreach($data as $item){

        $product_id = $item['id'];
        $qty = $item['qty'];
        $price = $item['price'];

        // CALL STORED PROCEDURE (ADBMS REQUIREMENT)
        $conn->query("CALL process_sale($product_id, $qty)");

        $subtotal = $price * $qty;
        $total += $subtotal;

        $conn->query("
            INSERT INTO sales_details (sale_id, product_id, quantity, price)
            VALUES ($sale_id, $product_id, $qty, $price)
        ");
    }

    // update total
    $conn->query("UPDATE sales SET total = $total WHERE id = $sale_id");

    $conn->query("INSERT INTO audit_logs (action) VALUES ('Sale completed ID: $sale_id')");

    $conn->commit();

    echo "Transaction Successful. Receipt: receipt.php?sale_id=$sale_id";

} catch (Exception $e) {

    $conn->rollback();
    echo "Transaction Failed: " . $e->getMessage();
}
?>