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
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Lấy danh sách các sản phẩm trong đơn hàng
        $sql_order_details = "SELECT od.id_gadget, od.quantity 
                              FROM order_details od
                              WHERE od.id_order = :id_order";
        $stmt_order_details = $conn->prepare($sql_order_details);
        $stmt_order_details->bindValue(':id_order', $order_id, PDO::PARAM_INT);
        $stmt_order_details->execute();
        $order_details = $stmt_order_details->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra số lượng hàng tồn kho
        foreach ($order_details as $detail) {
            // Lấy số lượng hiện có của sản phẩm trong kho
            $sql_check_stock = "SELECT quantity FROM gadget WHERE id_gadget = :id_gadget";
            $stmt_check_stock = $conn->prepare($sql_check_stock);
            $stmt_check_stock->bindValue(':id_gadget', $detail['id_gadget'], PDO::PARAM_INT);
            $stmt_check_stock->execute();
            $stock_quantity = $stmt_check_stock->fetchColumn();

            if ($stock_quantity === false || $detail['quantity'] > $stock_quantity) {
                // Nếu không đủ hàng, rollback và thông báo lỗi
                $conn->rollBack();
                die("Sản phẩm ID: {$detail['id_gadget']} không đủ hàng trong kho.");
            }
        }

        // Trừ số lượng hàng trong kho
        foreach ($order_details as $detail) {
            $sql_update_gadget = "UPDATE gadget SET quantity = quantity - :quantity 
                                  WHERE id_gadget = :id_gadget";
            $stmt_update_gadget = $conn->prepare($sql_update_gadget);
            $stmt_update_gadget->bindValue(':quantity', $detail['quantity'], PDO::PARAM_INT);
            $stmt_update_gadget->bindValue(':id_gadget', $detail['id_gadget'], PDO::PARAM_INT);
            $stmt_update_gadget->execute();
        }

        // Cập nhật trạng thái đơn hàng
        $sql_update_order = "UPDATE `orders` SET `status` = 'Confirmed', `updated_at` = NOW(), `id_employee` = :id_employee 
                             WHERE `id_order` = :id_order";
        $stmt_update_order = $conn->prepare($sql_update_order);
        $stmt_update_order->bindValue(':id_employee', $user_id, PDO::PARAM_INT);
        $stmt_update_order->bindValue(':id_order', $order_id, PDO::PARAM_INT);
        $stmt_update_order->execute();

        // Commit transaction
        $conn->commit();
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
?>
