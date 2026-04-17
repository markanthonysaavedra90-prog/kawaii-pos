<?php
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}

// TOTAL SALES
$totalSales = $conn->query("SELECT SUM(total) as total FROM sales")->fetch_assoc()['total'];

// TOTAL TRANSACTIONS
$totalTransactions = $conn->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'];

// BEST SELLING PRODUCTS
$best = $conn->query("
SELECT 
    products.name,
    SUM(sales_details.quantity) as total_sold
FROM sales_details
INNER JOIN products ON sales_details.product_id = products.id
GROUP BY product_id
ORDER BY total_sold DESC
LIMIT 5
");

$bestProducts = [];
while($row = $best->fetch_assoc()){
    $bestProducts[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Kawaii POS</title>
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
        <h1>Dashboard</h1>

        <div class="grid">
            <div class="stat-card">
                <p>Total Sales</p>
                <h3>₱<?php echo $totalSales ? number_format($totalSales, 2) : '0.00'; ?></h3>
            </div>

            <div class="stat-card">
                <p>Total Transactions</p>
                <h3><?php echo $totalTransactions; ?></h3>
            </div>
        </div>

        <div class="card">
            <h2>Best Selling Products</h2>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Units Sold</th>
                </tr>
                <?php foreach($bestProducts as $product): ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><span class="badge badge-primary"><?php echo $product['total_sold']; ?> units</span></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>