<?php
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $image = '';

    // Xử lý tải lên hình ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra xem tệp có thực sự là hình ảnh hay không
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            // Kiểm tra kích thước tệp
            if ($_FILES["image"]["size"] <= 5000000) { // 5MB
                // Cho phép một số định dạng tệp nhất định
                if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image = basename($_FILES["image"]["name"]);
                    } else {
                        $error = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                $error = "Sorry, your file is too large.";
            }
        } else {
            $error = "File is not an image.";
        }
    } else {
        $error = "Sorry, your file was not uploaded.";
    }

    if (empty($error) && addProduct($name, $price, $type, $description, $image)) {
        header("Location: products.php");
        exit();
    }
    if (doesProductExist($name)) {
        $error_message = 'A product with this name already exists.';
    } else {
        // Tiến hành thêm sản phẩm vào cơ sở dữ liệu
        // Code thêm sản phẩm ở đây
        $stmt = $pdo->prepare("INSERT INTO products (name, price, type, description, image) VALUES (:name, :price, :type, :description, :image)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->execute();
        
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        header('Location: products.php');
        exit();
    }
}


include 'includes/header.php';
?>

<h2>Add Product</h2>
<form method="POST" enctype="multipart/form-data">
        <!-- Các trường khác như price, type, description, image -->
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>
    <label for="price">Price:</label>
    <input type="text" id="price" name="price" required>
    <label for="type">Type:</label>
    <input type="text" id="type" name="type" required>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea>
    <label for="image">Image:</label>
    <input type="file" id="image" name="image" required>
    <button type="submit">Add Product</button>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</form>

<?php include 'includes/footer.php'; ?>
