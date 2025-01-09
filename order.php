<?php
include 'components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}


$role = $_SESSION['role']; // Role của người dùng hiện tại
$user_id = $_SESSION['user_id']; // ID người dùng hiện tại

// Câu truy vấn để lấy thông tin đơn hàng và chi tiết sản phẩm
if ($role === 'employee') {
    // Câu truy vấn sửa lại để nhóm theo id_order và tính tổng số lượng và giá trị
    $sql = "
        SELECT o.id_order, o.status, o.created_at, o.updated_at,
               SUM(od.quantity) AS total_quantity,
               SUM(od.quantity * g.imp_gadget) AS total_price
        FROM orders o
        JOIN order_details od ON o.id_order = od.id_order
        JOIN gadget g ON od.id_gadget = g.id_gadget
        WHERE o.status IN ('Pending', 'Cancelled')
        GROUP BY o.id_order
        ORDER BY 
            FIELD(o.status, 'Pending', 'Cancelled'),
            o.updated_at DESC";
} elseif ($role === 'customer') {
    // Câu truy vấn cho customer
    $sql = "
        SELECT o.id_order, o.status, o.created_at, o.updated_at,
               g.id_gadget, g.name_gadget, g.category, g.pic_gadget, g.imp_gadget, g.exp_gadget, 
               od.quantity, e.name_employee
        FROM orders o
        JOIN order_details od ON o.id_order = od.id_order
        JOIN gadget g ON od.id_gadget = g.id_gadget
        JOIN employee e ON o.id_employee = e.id_employee
        WHERE o.id_customer = :user_id AND o.status IN ('Confirmed', 'Shipped')
        GROUP BY o.id_order
        ORDER BY 
            FIELD(o.status, 'Confirmed', 'Shipped'),
            o.updated_at DESC";
}

// Chuẩn bị và thực thi truy vấn lấy danh sách đơn hàng
$stmt = $conn->prepare($sql);
if (strpos($sql, ':user_id') !== false) {
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
}
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy thông tin người dùng
$user_sql = $role === 'employee' ? "SELECT * FROM employee WHERE id_employee = :user_id" : "SELECT * FROM customer WHERE id_customer = :user_id";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$user_stmt->execute();
$user_info = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Biến tổng số lượng và tổng giá trị
$total_quantity = 0;
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logocart.png" type="image/png">

    <title>Quản Lý Đơn Hàng</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="css/header_footer.css">

    <script>
    function handleOrder(action, orderId) {
        let message = "";
        if (action === "delete") message = "Bạn có chắc chắn muốn xóa đơn hàng này?";
        if (action === "confirm") message = "Bạn chắc chắn muốn xác nhận đơn hàng?";
        if (action === "cancel") message = "Bạn chắc chắn muốn hủy đơn hàng này?";
        if (action === "receive") message = "Bạn chắc chắn muốn nhận hàng?";
        if (confirm(message)) {
            window.location.href = `order_${action}.php?order_id=${orderId}`;
        }
    }
    </script>

    <link rel="stylesheet" href="components/header footer.css">
</head>

<body>

    <?php
    if ($role === 'customer') {
        include 'components/cus_header.php';
    } elseif ($role === 'employee') {
        include 'components/header.php';
    }
    ?>

    <div class="order-page">
        <!-- Bảng Đơn Hàng -->
        <div class="shopping-cart">
            <h2><?php echo $role === 'employee' ? 'Quản lý đơn hàng' : 'Đơn hàng của bạn'; ?></h2>

            <?php foreach ($orders as $order):
                // Xác định class cho từng đơn hàng dựa trên trạng thái
                $class = '';
                if (($role === 'employee' && $order['status'] === 'Pending') || ($role === 'customer' && $order['status'] === 'Shipped')) {
                    $class = 'done'; // Class done cho "Pending" với employee hoặc "Shipped" với customer
                } elseif ($role === 'employee' && $order['status'] === 'Cancelled') {
                    $class = 'cancel'; // Class cancel cho "Cancelled" với employee
                }
            ?>
            <div class="shopping-cart <?php echo $class; ?>">
                <h3>Đơn hàng #<?php echo $order['id_order']; ?></a></h3>
                <p>Trạng thái: <?php echo htmlspecialchars($order['status']); ?></p>
                <p>Ngày tạo: <?php echo date("d/m/Y", strtotime($order['created_at'])); ?></p>
                <p>Ngày cập nhật: <?php echo date("d/m/Y", strtotime($order['updated_at'])); ?></p>
                <p>Sản phẩm:</p>
                <ul class="gadget-list-card">
                    <?php
                        // Câu truy vấn lấy chi tiết sản phẩm trong đơn hàng
                        $details_sql = "SELECT g.id_gadget, g.name_gadget, g.category, g.pic_gadget, g.imp_gadget, g.exp_gadget, od.quantity
                                        FROM order_details od
                                        JOIN gadget g ON od.id_gadget = g.id_gadget
                                        WHERE od.id_order = :id_order";
                        $details_stmt = $conn->prepare($details_sql);
                        $details_stmt->execute([':id_order' => $order['id_order']]);
                        $details = $details_stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($details as $detail):

                            if (($role === 'customer' && $order['status'] === 'Confirmed') ||
                                ($role === 'employee' && $order['status'] === 'Pending')
                            ) {
                                $total_quantity += $detail['quantity'];
                                $total_price += $detail['quantity'] * $detail['imp_gadget'];
                            }
                        ?>
                    <ul class="gadget-card">
                        <img src="./images/img_gadget/<?php echo $detail['pic_gadget'] ?: 'default.png'; ?>"
                            alt="<?php echo htmlspecialchars($detail['name_gadget']); ?>" class="gadget-image" />
                        <div class="details">
                            <a href="view_gadget_cus.php?id=<?php echo $detail['id_gadget']; ?>">
                                <h3><?php echo htmlspecialchars($detail['name_gadget']); ?></h3>
                            </a>
                            <p>Số lượng: <?php echo $detail['quantity']; ?></p>
                            <p>Giá: <?php echo number_format($detail['imp_gadget'], 0); ?> VND</p>
                            <p>Thành tiền: <?php echo $detail['quantity'] * $detail['imp_gadget']; ?> VND</p>
                        </div>
                    </ul>
                    <?php endforeach; ?>
                </ul>

                <!-- Các nút hành động -->
                <div class="action-buttons">
                    <?php if ($role === 'employee' && $order['status'] === 'Pending'): ?>
                    <button class="btn-confirm" onclick="handleOrder('confirm', <?php echo $order['id_order']; ?>)">Gửi
                        hàng</button>
                    <?php elseif ($role === 'customer' && $order['status'] === 'Confirmed'): ?>
                    <button class="btn-confirm" onclick="handleOrder('receive', <?php echo $order['id_order']; ?>)">Nhận
                        hàng</button>
                    <button class="btn-delete" onclick="handleOrder('cancel', <?php echo $order['id_order']; ?>)">Hủy
                        đơn</button>
                    <?php elseif ($role === 'employee' && $order['status'] === 'Cancelled'): ?>
                    <button class="btn-delete" onclick="handleOrder('delete', <?php echo $order['id_order']; ?>)">Xóa
                        đơn</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Thông Tin Người Dùng và Tổng Kết -->
        <div class="user-info-summary">
            <div class="info">
                <h2><?php echo $role === 'employee' ? 'Thông tin nhân viên' : 'Thông tin người dùng'; ?></h2>
                <p><strong>Tên:</strong>
                    <?php echo htmlspecialchars($user_info[$role === 'employee' ? 'name_employee' : 'name_customer']); ?>
                </p>
                <p><strong>Số điện thoại:</strong>
                    <?php echo htmlspecialchars($user_info[$role === 'employee' ? 'phone_to' : 'phone_no']); ?></p>
            </div>

            <div class="summary">
                <h2>Tổng Hợp Đơn Hàng</h2>
                <p><strong>Tổng số lượng:</strong> <?php echo $total_quantity; ?></p>
                <p><strong>Tổng giá trị:</strong> <?php echo number_format($total_price, 0); ?> VND</p>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>

</html>