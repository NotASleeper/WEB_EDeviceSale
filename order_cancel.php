<?php
include 'components/connect.php';
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
            // Bắt đầu transaction
            $conn->beginTransaction();

            // Lấy danh sách các sản phẩm trong đơn hàng
            $sql_order_details = "SELECT id_gadget, quantity 
                                  FROM order_details 
                                  WHERE id_order = :id_order";
            $stmt_order_details = $conn->prepare($sql_order_details);
            $stmt_order_details->bindValue(':id_order', $order_id, PDO::PARAM_INT);
            $stmt_order_details->execute();
            $order_details = $stmt_order_details->fetchAll(PDO::FETCH_ASSOC);

            // Cập nhật số lượng hàng trong kho (cộng lại)
            foreach ($order_details as $detail) {
                $sql_update_gadget = "UPDATE gadget SET quantity = quantity + :quantity 
                                      WHERE id_gadget = :id_gadget";
                $stmt_update_gadget = $conn->prepare($sql_update_gadget);
                $stmt_update_gadget->bindValue(':quantity', $detail['quantity'], PDO::PARAM_INT);
                $stmt_update_gadget->bindValue(':id_gadget', $detail['id_gadget'], PDO::PARAM_INT);
                $stmt_update_gadget->execute();
            }

            // Cập nhật trạng thái đơn hàng thành 'Cancelled'
            $sql_update_order = "UPDATE orders SET status = 'Cancelled', updated_at = NOW() 
                                 WHERE id_order = :id_order AND id_customer = :id_customer";
            $stmt_update_order = $conn->prepare($sql_update_order);
            $stmt_update_order->bindValue(':id_order', $order_id, PDO::PARAM_INT);
            $stmt_update_order->bindValue(':id_customer', $user_id, PDO::PARAM_INT);
            $stmt_update_order->execute();

            // Commit transaction
            $conn->commit();

            // Chuyển hướng về trang đơn hàng sau khi cập nhật
            header('Location: order.php');
            exit();
        } catch (PDOException $e) {
            // Rollback transaction nếu có lỗi
            $conn->rollBack();
            die("Lỗi khi thực thi SQL: " . $e->getMessage());
        }
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Dữ liệu không hợp lệ.";
}
?>
