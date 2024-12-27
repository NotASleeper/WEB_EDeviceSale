<?php
include 'components/connect.php';
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'customer') {
    echo "Bạn không có quyền thực hiện hành động này!";
    exit();
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $selected_items = $_POST['selected_items'] ?? [];

    if (!empty($selected_items)) {
        try {
            $conn->beginTransaction();

            // Tạo đơn hàng mới
            $stmt = $conn->prepare("INSERT INTO orders (id_customer, status, created_at) VALUES (:id_customer, 'Pending', NOW())");
            $stmt->bindValue(':id_customer', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $id_order = $conn->lastInsertId();

            // Chuyển dữ liệu từ `cart` sang `order_details`
            $stmt = $conn->prepare("INSERT INTO order_details (id_order, id_gadget, quantity, id_customer) 
                                    SELECT :id_order, id_gadget, quantity, id_customer FROM cart WHERE id_cart = :id_cart");
            foreach ($selected_items as $id_cart) {
                $stmt->bindValue(':id_order', $id_order, PDO::PARAM_INT);
                $stmt->bindValue(':id_cart', $id_cart, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Xóa các mục đã chọn khỏi giỏ hàng
            $stmt = $conn->prepare("DELETE FROM cart WHERE id_cart = :id_cart");
            foreach ($selected_items as $id_cart) {
                $stmt->bindValue(':id_cart', $id_cart, PDO::PARAM_INT);
                $stmt->execute();
            }

            $conn->commit();
            header('location: order.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            echo "Lỗi: " . $e->getMessage();
        }
    } else {
        echo "Vui lòng chọn ít nhất một sản phẩm để đặt hàng.";
    }
}

// Xử lý xóa hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $selected_items = $_POST['selected_items'] ?? [];

    if (!empty($selected_items)) {
        try {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id_cart = :id_cart");
            foreach ($selected_items as $id_cart) {
                $stmt->bindValue(':id_cart', $id_cart, PDO::PARAM_INT);
                $stmt->execute();
            }
            echo "Các sản phẩm đã được xóa khỏi giỏ hàng.";
            header('location: cart.php');
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    } else {
        echo "Vui lòng chọn ít nhất một sản phẩm để xóa.";
    }
}
?>
