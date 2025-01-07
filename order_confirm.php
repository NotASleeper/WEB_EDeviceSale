<?php
include 'components/connect.php';
session_start();

// Kiểm tra quyền truy cập và bảo mật
if ($_SESSION['role'] !== 'employee') {
    header('Location: home.php');
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $user_id = $_SESSION['user_id'];

    try {
        $sql = "UPDATE `orders` SET `status` = 'Confirmed', `updated_at` = NOW(), `id_employee` = :id_employee WHERE `id_order` = :id_order";
        $stmt = $conn->prepare($sql); // Assume $conn is a PDO instance
        $stmt->bindValue(':id_employee', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id_order', $order_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
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
?>
