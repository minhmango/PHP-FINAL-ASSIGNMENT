<?php
include 'includes/functions.php';
include 'includes/header.php';

// Xử lý lọc sản phẩm
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$filter_max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 3; // Số sản phẩm trên mỗi trang

// Xác định số sản phẩm cần bỏ qua (offset) cho phân trang
$offset = ($page - 1) * $products_per_page;

// Lấy danh sách các loại sản phẩm từ cơ sở dữ liệu
// $types = getProductTypes();

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$products = getFilteredProducts($filter_type, $filter_min_price, $filter_max_price, $offset, $products_per_page);
$total_products = countFilteredProducts($filter_type, $filter_min_price, $filter_max_price);
$total_pages = ceil($total_products / $products_per_page);

// Lấy tên người dùng từ session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!-- Câu chào mừng -->
<div class="welcome-message">
    <h1>Welcome<?php if ($username) echo ', ' . htmlspecialchars($username); ?>!</h1>
    <p>Browse our collection of high-quality products and find what you’re looking for.</p>
</div>
<h2>Product List</h2>
<!-- Form lọc sản phẩm -->
<form method="GET">
    <label for="type">Type:</label>
    <select id="type" name="type">
        <option value="">All Types</option>
        <?php foreach ($types as $type): ?>
            <option value="<?php echo htmlspecialchars($type['type']); ?>" <?php if ($type['type'] === $filter_type) echo 'selected'; ?>>
                <?php echo htmlspecialchars($type['type']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label for="min_price">Min Price:</label>
    <input type="number" id="min_price" name="min_price" value="<?php echo htmlspecialchars($filter_min_price); ?>">
    
    <label for="max_price">Max Price:</label>
    <input type="number" id="max_price" name="max_price" value="<?php echo htmlspecialchars($filter_max_price); ?>">
    
    <button type="submit">Filter</button>
</form>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Type</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($product['type']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td>
                    <?php if ($product['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100px;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" >Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&type=<?php echo htmlspecialchars($filter_type); ?>&min_price=<?php echo htmlspecialchars($filter_min_price); ?>&max_price=<?php echo htmlspecialchars($filter_max_price); ?>">&laquo; Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&type=<?php echo htmlspecialchars($filter_type); ?>&min_price=<?php echo htmlspecialchars($filter_min_price); ?>&max_price=<?php echo htmlspecialchars($filter_max_price); ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>&type=<?php echo htmlspecialchars($filter_type); ?>&min_price=<?php echo htmlspecialchars($filter_min_price); ?>&max_price=<?php echo htmlspecialchars($filter_max_price); ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script>
document.getElementById('selectAll').addEventListener('click', function() {
    var checkboxes = document.querySelectorAll('input[name="product_ids[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
});

function confirmDelete() {
    return confirm('Are you sure you want to delete this product?');
}

function confirmBulkDelete() {
    return confirm('Are you sure you want to delete the selected products?');
}
</script>