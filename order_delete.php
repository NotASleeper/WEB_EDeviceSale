<?php
include './components/connect.php';
session_start();

if ($_SESSION['role'] !== 'employee') {
    header('Location: home.php');
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        // Assume $conn is a PDO connection instance
        $sql = "DELETE FROM `orders` WHERE `id_order` = :id_order";
        $stmt = $conn->prepare($sql); // Prepare the SQL statement
        $stmt->bindValue(':id_order', $order_id, PDO::PARAM_INT); // Bind the order_id parameter

        if ($stmt->execute()) {
            header('Location: order.php');
            exit();
        } else {
            echo "Có lỗi xảy ra khi xóa đơn hàng.";
        }
    } catch (PDOException $e) {
        die("Lỗi khi thực thi SQL: " . $e->getMessage());
    }
} else {
    echo "Dữ liệu không hợp lệ.";
}
?>
