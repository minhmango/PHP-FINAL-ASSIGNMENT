<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoe Store Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Shoe Store Management</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav>
                <a href="products.php">Products</a>
                <a href="add_product.php">Add Product</a>
                <a href="logout.php">Logout</a>
            </nav>
        <?php endif; ?>
    </header>
    <main>
