<?php
include 'components/connect.php';
session_start();

if ($_SESSION['role'] !== 'customer') {
    header('Location: home.php');
    exit();
}

// Kiểm tra nếu có tham số 'order_id' trong URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Bắt đầu giao dịch
        $conn->beginTransaction();

        // Lấy tổng giá trị đơn hàng
        $total_price_sql = "
            SELECT SUM(od.quantity * g.imp_gadget) AS total_price
            FROM order_details od
            JOIN gadget g ON od.id_gadget = g.id_gadget
            WHERE od.id_order = :id_order
        ";
        $price_stmt = $conn->prepare($total_price_sql);
        $price_stmt->bindValue(':id_order', $order_id, PDO::PARAM_INT);
        $price_stmt->execute();
        $order_data = $price_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order_data) {
            throw new Exception("Không tìm thấy đơn hàng hoặc đơn hàng không hợp lệ.");
        }

        $total_price = $order_data['total_price'];

        // Cập nhật trạng thái đơn hàng
        $update_order_sql = "
            UPDATE orders 
            SET status = 'Shipped', updated_at = NOW() 
            WHERE id_order = :id_order AND id_customer = :id_customer
        ";
        $update_stmt = $conn->prepare($update_order_sql);
        $update_stmt->bindValue(':id_order', $order_id, PDO::PARAM_INT);
        $update_stmt->bindValue(':id_customer', $user_id, PDO::PARAM_INT);
        if (!$update_stmt->execute()) {
            throw new Exception("Không thể cập nhật trạng thái đơn hàng.");
        }

        // Cập nhật tổng chi tiêu của khách hàng
        $update_customer_sql = "
            UPDATE customer 
            SET total_spending = total_spending + :total_price 
            WHERE id_customer = :id_customer
        ";
        $customer_stmt = $conn->prepare($update_customer_sql);
        $customer_stmt->bindValue(':total_price', $total_price, PDO::PARAM_INT);
        $customer_stmt->bindValue(':id_customer', $user_id, PDO::PARAM_INT);
        if (!$customer_stmt->execute()) {
            throw new Exception("Không thể cập nhật tổng chi tiêu của khách hàng.");
        }

        // Hoàn tất giao dịch
        $conn->commit();

        // Chuyển hướng về trang đơn hàng
        header('Location: order.php');
        exit();
    } catch (Exception $e) {
        // Hủy giao dịch nếu có lỗi
        $conn->rollBack();
        die("Lỗi: " . $e->getMessage());
    }
} else {
    // Nếu không có 'order_id' hợp lệ trong URL
    echo "Dữ liệu không hợp lệ.";
}
?>