<?php
include 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Chỉ role 'customer' mới có thể thêm vào giỏ hàng

if ($role !== 'customer') {
    echo "Bạn không có quyền thực hiện hành động này!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_gadget = $_POST['pid'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($id_gadget)) {
        echo "Dữ liệu không hợp lệ!";
        exit();
    }

    // Thêm đơn hàng vào bảng 'orders' với trạng thái 'Pending'
    $stmt = $conn->prepare("INSERT INTO orders (id_gadget, id_customer, quantity, status, created_at) 
                            VALUES (:id_gadget, :id_customer, 1, 'Pending', NOW())");
    $stmt->bindValue(':id_gadget', $id_gadget, PDO::PARAM_INT);
    $stmt->bindValue(':id_customer', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Quay trở lại trang trước hoặc giỏ hàng
    header('location: home_cus.php');
    exit();
}
?>
