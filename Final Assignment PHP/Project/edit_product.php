<?php
include 'includes/functions.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$product = getProducts(['id' => $id])->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $description = $_POST['description'];

    $image = $product['image']; // Keep the old image if no new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File is not an image.";
            $uploadOk = 0;
        }

        if (file_exists($target_file)) {
            $error = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if ($_FILES["image"]["size"] > 500000) {
            $error = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $_FILES["image"]["name"];
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }

    if (editProduct($id, $name, $price, $type, $description, $image)) {
        header("Location: products.php");
        exit();
    } else {
        $error = "Failed to edit product.";
    }
    if ($name != $product['name'] && doesProductExist($name)) {
        $error_message = 'A product with this name already exists.';
    } else {
        // Tiến hành cập nhật sản phẩm vào cơ sở dữ liệu
        // Code cập nhật sản phẩm ở đây
        $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, type = :type, description = :description, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        header('Location: products.php');
        exit();
    }
}

include 'includes/header.php';
?>

<h2>Edit Product</h2>
<form method="POST" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    <label for="price">Price:</label>
    <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
    <label for="type">Type:</label>
    <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($product['type']); ?>" required>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
    <label for="image">Image:</label>
    <input type="file" id="image" name="image">
    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
    <button type="submit">Save Changes</button>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</form>

<?php include 'includes/footer.php'; ?>
