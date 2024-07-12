<?php
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_ids'])) {
    $product_ids = $_POST['product_ids'];
    
    foreach ($product_ids as $product_id) {
        // Gọi hàm xóa sản phẩm trong cơ sở dữ liệu
        deleteProduct($product_id);
    }

    // Chuyển hướng lại trang products.php sau khi xóa xong
    header('Location: products.php');
    exit;
}
?>
