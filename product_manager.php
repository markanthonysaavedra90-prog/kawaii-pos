<?php 
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Kawaii POS</title>
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

        <h1>Product Manager</h1>

        <!-- ADD PRODUCT FORM -->
        <div class="card" style="max-width: 500px; margin-bottom: 30px;">
            <h2>Add New Product</h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="number" name="price" placeholder="Price" step="0.01" required>

                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    $cats = $conn->query("SELECT * FROM categories");
                    while($c = $cats->fetch_assoc()){
                        echo "<option value='{$c['id']}'>{$c['name']}</option>";
                    }
                    ?>
                </select>

                <input type="file" name="image" accept="image/*" required>

                <button type="submit" name="add" style="width: 100%; margin-top: 15px;">Add Product</button>
            </form>
        </div>

        <?php
        if(isset($_POST['add'])){
            $name = $_POST['name'];
            $price = $_POST['price'];
            $cat = $_POST['category_id'];
            $img = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];

            move_uploaded_file($tmp, "uploads/".$img);
            $conn->query("INSERT INTO products (name, image, price, category_id) VALUES ('$name','$img','$price','$cat')");
            
            echo "<div class='card' style='background: #c8e6c9; border-left: 5px solid #66bb6a; margin-bottom: 20px;'><p style='color: #2e7d32; font-weight: 700;'>Product added successfully!</p></div>";
        }
        ?>

        <!-- PRODUCT LIST -->
        <div class="card">
            <h2>All Products</h2>
            <div class="product-grid">
                <?php
                $result = $conn->query("
                SELECT products.*, categories.name AS category
                FROM products
                INNER JOIN categories ON products.category_id = categories.id
                ");

                while($row = $result->fetch_assoc()){
                ?>
                <div class="product-card">
                    <?php $imgSrc = (strpos($row['image'], 'http') !== false) ? $row['image'] : 'uploads/' . $row['image']; ?>
                    <img src="<?php echo $imgSrc; ?>" alt="<?php echo $row['name']; ?>" style="object-fit: cover;">
                    <h3><?php echo $row['name']; ?></h3>
                    <p style="color: #999; font-size: 0.9rem;">Category: <?php echo $row['category']; ?></p>
                    <div class="price">₱<?php echo number_format($row['price'], 2); ?></div>
                </div>
                <?php } ?>
            </div>
        </div>

</div>

</div>