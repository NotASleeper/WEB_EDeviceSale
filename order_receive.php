<?php
include './components/connect.php';
session_start();

if ($_SESSION['role'] !== 'customer') {
    header('Location: home.php');
    exit();
}

// Kiểm tra nếu có tham số 'order_id' trong URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Lấy id_customer từ session
    $user_id = $_SESSION['user_id'];

    try {
        // Câu lệnh SQL để cập nhật trạng thái đơn hàng
        $sql = "UPDATE orders SET status = 'Shipped', updated_at = NOW() WHERE id_order = :id_order AND id_customer = :id_customer";

        // Chuẩn bị câu lệnh SQL
        $stmt = $conn->prepare($sql);

        // Liên kết tham số vào câu lệnh SQL
        $stmt->bindValue(':id_order', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':id_customer', $user_id, PDO::PARAM_INT);

        // Thực thi câu lệnh SQL
        if ($stmt->execute()) {
            // Chuyển hướng về trang đơn hàng sau khi cập nhật trạng thái
            header('Location: order.php');
            exit();
        } else {
            // Nếu có lỗi xảy ra khi thực thi câu lệnh
            echo "Có lỗi xảy ra khi cập nhật trạng thái.";
        }
    } catch (PDOException $e) {
        // Xử lý lỗi kết nối hoặc lỗi SQL
        die("Lỗi khi thực thi SQL: " . $e->getMessage());
    }
} else {
    // Nếu không có 'order_id' hợp lệ trong URL
    echo "Dữ liệu không hợp lệ.";
}
