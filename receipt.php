<?php
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}

$sale_id = $_GET['sale_id'];

$sale = $conn->query("
SELECT * FROM sales WHERE id=$sale_id
")->fetch_assoc();

$items = $conn->query("
SELECT products.name, sales_details.quantity, sales_details.price
FROM sales_details
INNER JOIN products ON sales_details.product_id = products.id
WHERE sales_details.sale_id=$sale_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt - Kawaii POS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>Kawaii POS</h2>
        <a href="pos.php">POS</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="product_manager.php">Products</a>
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <h1>Kawaii Receipt</h1>

        <div class="card">
            <h2>Sale ID: <?php echo $sale_id; ?></h2>

            <table>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>

                <?php while($row=$items->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>₱<?php echo number_format($row['price'], 2); ?></td>
                </tr>
                <?php } ?>
            </table>

            <h3>Total: ₱<?php echo number_format($sale['total'], 2); ?></h3>

            <button onclick="window.print()">Print Receipt</button>
            <a href="pos.php"><button>Back to POS</button></a>
        </div>
    </div>

</div>

</body>
</html>