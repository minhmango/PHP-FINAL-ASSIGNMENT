<?php
include 'includes/functions.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteProduct($id)) {
        header("Location: products.php");
        exit();
    } else {
        $error = "Failed to delete product.";
    }
}

include 'includes/header.php';
?>

<h2>Delete Product</h2>
<p>Are you sure you want to delete this product?</p>
<form method="POST">
    <button type="submit">Yes, Delete</button>
    <a href="products.php">Cancel</a>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</form>

<?php include 'includes/footer.php'; ?>
