<?php
session_start();
include 'db.php';

function register($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        return false; // Tên người dùng đã tồn tại
    }
    
    $stmt->close();
    
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    return $stmt->execute();
}

function login($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $db_password);
    if ($stmt->fetch() && $password === $db_password) {
        $_SESSION['user_id'] = $id;
        return true;
    }
    return false;
}

function addProduct($name, $price, $type, $description, $image) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO products (name, price, type, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $name, $price, $type, $description, $image);
    return $stmt->execute();
}

function editProduct($id, $name, $price, $type, $description, $image) {
    global $conn;
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, type = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sdsssi", $name, $price, $type, $description, $image, $id);
    return $stmt->execute();
}

function deleteProduct($id) {
    global $conn;
    $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

function deleteMultipleProducts($ids) {
    global $conn;
    $ids_str = implode(',', array_map('intval', $ids));
    $sql = "DELETE FROM products WHERE id IN ($ids_str)";
    return $conn->query($sql);
}

function getProducts($filters = [], $search = "", $limit = 10, $offset = 0) {
    global $conn;
    $sql = "SELECT * FROM products WHERE 1=1";

    if (!empty($filters)) {
        foreach ($filters as $key => $value) {
            $sql .= " AND $key='$value'";
        }
    }

    if (!empty($search)) {
        $sql .= " AND name LIKE '%$search%'";
    }

    $sql .= " LIMIT $limit OFFSET $offset";
    return $conn->query($sql);
}

function getProductTypes() {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT type FROM products ORDER BY type");
    $types = [];
    while ($row = $stmt->fetch_assoc()) {
        $types[] = $row;
    }
    return $types;
}

function getFilteredProducts($filter_type, $filter_min_price, $filter_max_price, $offset, $limit) {
    global $conn;
    $sql = "SELECT * FROM products WHERE 1=1";

    if (!empty($filter_type)) {
        $filter_type = $conn->real_escape_string($filter_type);
        $sql .= " AND type='$filter_type'";
    }

    if (!empty($filter_min_price) && is_numeric($filter_min_price)) {
        $filter_min_price = (float)$filter_min_price;
        $sql .= " AND price >= $filter_min_price";
    }

    if (!empty($filter_max_price) && is_numeric($filter_max_price)) {
        $filter_max_price = (float)$filter_max_price;
        $sql .= " AND price <= $filter_max_price";
    }

    $sql .= " LIMIT $offset, $limit";
    $result = $conn->query($sql);

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

function countFilteredProducts($filter_type, $filter_min_price, $filter_max_price) {
    global $conn;
    $sql = "SELECT COUNT(*) AS total FROM products WHERE 1=1";

    if (!empty($filter_type)) {
        $filter_type = $conn->real_escape_string($filter_type);
        $sql .= " AND type='$filter_type'";
    }

    if (!empty($filter_min_price) && is_numeric($filter_min_price)) {
        $filter_min_price = (float)$filter_min_price;
        $sql .= " AND price >= $filter_min_price";
    }

    if (!empty($filter_max_price) && is_numeric($filter_max_price)) {
        $filter_max_price = (float)$filter_max_price;
        $sql .= " AND price <= $filter_max_price";
    }

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function doesProductExist($name) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count > 0;
}
?>
