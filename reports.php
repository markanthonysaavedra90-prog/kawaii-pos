<?php 
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}

// GET SALES DATA
$sales = $conn->query("SELECT SUM(total) as total FROM sales")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - Kawaii POS</title>
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

        <h1>Reports</h1>

        <!-- SALES SUMMARY -->
        <div class="stat-card">
            <p>Total Sales Revenue</p>
            <h3>₱ <?php echo number_format($sales ?? 0, 2); ?></h3>
        </div>

        <!-- BEST SELLING PRODUCTS -->
        <div class="card">
            <h2>Best Selling Products</h2>

            <table>
                <tr>
                    <th>Product</th>
                    <th>Qty Sold</th>
                </tr>

                <?php
                $best = $conn->query("
                SELECT products.name, SUM(sales_details.quantity) as qty
                FROM sales_details
                INNER JOIN products ON products.id = sales_details.product_id
                GROUP BY sales_details.product_id
                ORDER BY qty DESC
                ");

                while($b = $best->fetch_assoc()){
                ?>
                <tr>
                    <td><?php echo $b['name']; ?></td>
                    <td><span class="badge badge-success"><?php echo $b['qty']; ?> units</span></td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- INVENTORY REPORT -->
        <div class="card">
            <h2>Inventory Status</h2>

            <table>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>

                <?php
                $inv = $conn->query("
                SELECT products.name, inventory.stock
                FROM inventory
                INNER JOIN products ON products.id = inventory.product_id
                ");

                while($i = $inv->fetch_assoc()){

                    $status = ($i['stock'] <= 10) ? "LOW" : "OK";
                ?>
                <tr>
                    <td><?php echo $i['name']; ?></td>
                    <td><?php echo $i['stock']; ?></td>
                    <td style="color:<?php echo ($status=='LOW')?'#f7b7a6':'#98d8c8'; ?>; font-weight: 700;">
                        <?php echo $status; ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>