<?php
include './components/connect.php';
session_start();

// Kiểm tra quyền truy cập và bảo mật
if ($_SESSION['role'] !== 'customer') {
    header('Location: home.php');
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Xác thực người dùng
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        try {
            // Câu lệnh SQL
            $sql = "UPDATE orders SET status = 'Cancelled', updated_at = NOW() WHERE id_order = :id_order AND id_customer = :id_customer";

            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare($sql);

            // Liên kết tham số
            $stmt->bindValue(':id_order', $order_id, PDO::PARAM_INT);
            $stmt->bindValue(':id_customer', $user_id, PDO::PARAM_INT);

            // Thực thi câu lệnh
            if ($stmt->execute()) {
                // Chuyển hướng về trang đơn hàng sau khi cập nhật
                header('Location: order.php');
                exit();
            } else {
                echo "Có lỗi xảy ra khi cập nhật trạng thái.";
            }
        } catch (PDOException $e) {
            die("Lỗi khi thực thi SQL: " . $e->getMessage());
        }
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Dữ liệu không hợp lệ.";
}
?>
