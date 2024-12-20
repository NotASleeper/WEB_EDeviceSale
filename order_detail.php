<?php

include 'components/connect.php';

session_start();

// Lấy ID đơn hàng từ URL
$id_order = isset($_GET['id_order']) ? (int)$_GET['id_order'] : 0;

$role = $_SESSION['role']; // Vai trò người dùng hiện tại
$user_id = $_SESSION['user_id']; // ID người dùng hiện tại

// Truy vấn thông tin chung của đơn hàng
$order_sql = "
    SELECT o.id_order, o.created_at AS order_date, o.updated_at AS shipped_date, o.status,
           e.name_employee AS confirmed_by, c.name_customer AS customer_name
    FROM orders o
    LEFT JOIN employee e ON o.id_employee = e.id_employee
    LEFT JOIN customer c ON o.id_customer = c.id_customer
    WHERE o.id_order = :id_order";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bindValue(':id_order', $id_order, PDO::PARAM_INT);
$order_stmt->execute();
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy đơn hàng
if (!$order) {
    echo "<p>Đơn hàng không tồn tại.</p>";
    exit();
}

// Truy vấn chi tiết các sản phẩm trong đơn hàng
$order_details_sql = "
    SELECT g.name_gadget, g.category, g.pic_gadget, g.imp_gadget, od.quantity, 
           od.quantity * g.imp_gadget AS total_price
    FROM order_details od
    JOIN gadget g ON od.id_gadget = g.id_gadget
    WHERE od.id_order = :id_order";
$order_details_stmt = $conn->prepare($order_details_sql);
$order_details_stmt->bindValue(':id_order', $id_order, PDO::PARAM_INT);
$order_details_stmt->execute();
$order_details = $order_details_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi Tiết Đơn Hàng</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <?php if ($role === 'customer'): ?>
    <?php include 'components/cus_header.php' ?>
  <?php elseif ($role === 'employee'): ?>
    <?php include 'components/header.php' ?>
  <?php endif; ?>

  <div class="order-detail-container">
    <h1>Chi Tiết Đơn Hàng</h1>

    <!-- Thông tin chung của đơn hàng -->
    <div class="order-info">
      <h2>Thông Tin Đơn Hàng</h2>
      <p><strong>Mã Đơn Hàng:</strong> <?php echo htmlspecialchars($order['id_order']); ?></p>
      <p><strong>Ngày Đặt:</strong> <?php echo htmlspecialchars(date("d/m/Y", strtotime($order['order_date']))); ?></p>
      <p><strong>Ngày Gửi:</strong> <?php echo $order['shipped_date'] ? htmlspecialchars(date("d/m/Y", strtotime($order['shipped_date']))) : 'Chưa gửi'; ?></p>
      <p><strong>Trạng Thái:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
      <?php if ($role === 'employee'): ?>
        <p><strong>Khách Hàng:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
      <?php endif; ?>
      <p><strong>Xác Nhận Bởi:</strong> <?php echo htmlspecialchars($order['confirmed_by'] ?: 'N/A'); ?></p>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="order-details">
      <h2>Sản Phẩm Trong Đơn Hàng</h2>
      <table>
        <thead>
          <tr>
            <th>Hình Ảnh</th>
            <th>Tên Sản Phẩm</th>
            <th>Loại</th>
            <th>Giá</th>
            <th>Số Lượng</th>
            <th>Tổng Giá</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $total_quantity = 0;
          $total_price = 0;
          foreach ($order_details as $detail): 
            $total_quantity += $detail['quantity'];
            $total_price += $detail['total_price'];
          ?>
            <tr>
              <td>
                <img src="./images/img_gadget/<?php echo $detail['pic_gadget'] ?: 'default.png'; ?>" alt="<?php echo htmlspecialchars($detail['name_gadget']); ?>" class="gadget-image-small">
              </td>
              <td><?php echo htmlspecialchars($detail['name_gadget']); ?></td>
              <td><?php echo htmlspecialchars($detail['category']); ?></td>
              <td><?php echo number_format($detail['imp_gadget'], 2); ?> VND</td>
              <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
              <td><?php echo number_format($detail['total_price'], 2); ?> VND</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Tổng hợp -->
    <div class="order-summary">
      <h2>Tổng Hợp</h2>
      <p><strong>Tổng Số Lượng:</strong> <?php echo $total_quantity; ?></p>
      <p><strong>Tổng Giá Trị:</strong> <?php echo number_format($total_price, 2); ?> VND</p>
    </div>
  </div>

  <?php include 'components\footer.php' ?>
</body>

</html>
