<?php
include './components/connect.php';

session_start();



if (!isset($_SESSION['user_id'])) {
  header('location:login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'customer') {
  echo "Bạn không có quyền xem trang này!";
  exit();
}




$role = $_SESSION['role']; // Role của người dùng hiện tại
$user_id = $_SESSION['user_id']; // ID người dùng hiện tại

// Câu truy vấn và thông tin riêng cho từng role
if ($role === 'employee') {
  $sql = "
    SELECT o.id_order, g.id_gadget, g.name_gadget, g.category, g.pic_gadget, g.imp_gadget, g.exp_gadget, 
           o.quantity, o.status, o.created_at, o.updated_at
    FROM orders o
    JOIN gadget g ON o.id_gadget = g.id_gadget
    WHERE o.status IN ('Pending', 'Cancelled')
    ORDER BY 
        FIELD(o.status, 'Pending', 'Cancelled'),
        o.updated_at DESC";

  $user_sql = "SELECT * FROM employee WHERE id_employee = :user_id";
} elseif ($role === 'customer') {
  $sql = "
    SELECT o.id_order, g.id_gadget, g.name_gadget, g.category, g.pic_gadget, g.imp_gadget, g.exp_gadget, 
           o.quantity, o.status, o.created_at, o.updated_at, e.name_employee
    FROM orders o
    JOIN gadget g ON o.id_gadget = g.id_gadget
    JOIN employee e ON o.id_employee = e.id_employee
    WHERE o.id_customer = :user_id AND o.status IN ('Confirmed', 'Shipped')
    ORDER BY 
        FIELD(o.status, 'Confirmed', 'Shipped'),
        o.updated_at DESC";

  $user_sql = "SELECT * FROM customer WHERE id_customer = :user_id";
}

// Chuẩn bị và thực thi truy vấn
$stmt = $conn->prepare($sql);

// Bind :user_id nếu truy vấn cần
if (strpos($sql, ':user_id') !== false) {
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Lấy thông tin người dùng
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
  <title>Quản Lý Đơn Hàng</title>
  <link rel="stylesheet" href="css/style.css">
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
</head>

<body>

  <?php if ($role === 'customer'): ?>
    <?php include 'components/cus_header.php' ?>
  <?php elseif ($role === 'employee'): ?>
    <?php include 'components/header.php' ?>
  <?php endif; ?>

  <div class="order-page">
    <!-- Bảng Đơn Hàng -->
    <div class="shopping-cart">
      <h2><?php echo $role === 'employee' ? 'Quản lý đơn hàng' : 'Đơn hàng của bạn'; ?></h2>

      <?php foreach ($result as $row):
        if ($role === 'employee' && $row['status'] === 'Pending') {
          $total_quantity += $row['quantity'];
          $total_price += $row['quantity'] * $row['imp_gadget'];
        } elseif ($role === 'customer' && $row['status'] === 'Confirm') {
          $total_quantity += $row['quantity'];
          $total_price += $row['quantity'] * $row['imp_gadget'];
        }
      ?>
        <div class="gadget-card <?php echo $row['status'] === 'Shipped' ? 'done' : ($row['status'] === 'Cancelled' ? 'cancel' : ''); ?>">
          <img src="./images/img_gadget/<?php echo $row['pic_gadget'] ? $row['pic_gadget'] : '../assets/img/sample.png'; ?>" alt="<?php echo htmlspecialchars($row['name_gadget']); ?>" class="gadget-image">
          <div class="gadget-details">
            <h2 class="gadget-name"><a href="order_detail.php?id_gadget=<?php echo $row['id_gadget']; ?>"><?php echo htmlspecialchars($row['name_gadget']); ?></a></h2>
            <p class="gadget-category"><?php echo htmlspecialchars($row['category']); ?></p>
            <p class="gadget-price">Giá: <del><?php echo number_format($row['exp_gadget'], 0); ?></del> <?php echo number_format($row['imp_gadget'], 0); ?> VND</p>
            <p class="order-status">Trạng thái: <?php echo htmlspecialchars($row['status']); ?></p>
            <?php if ($role === 'customer'): ?>
              <p class="order-employee">Người xác nhận: <?php echo htmlspecialchars($row['name_employee']); ?></p>
            <?php endif; ?>
            <p class="order-date">
              <?php echo $row['status'] === 'Pending' ? "Ngày đặt: " : ($row['status'] === 'Cancelled' ? "Ngày hủy: " : "Ngày cập nhật: "); ?>
              <?php echo date("d/m/Y", strtotime($row['updated_at'])); ?>
            </p>
            <div class="quantity-controls">
              <p class="quantity">Số lượng: <?php echo $row['quantity']; ?></p>
              <p class="subtotal">Thành tiền: <?php echo number_format($row['quantity'] * $row['imp_gadget'], 0); ?> VND</p>



              <!-- Các nút hành động -->
              <?php if ($role === 'employee' && $row['status'] === 'Pending'): ?>
                <button class="confirm-button" onclick="handleOrder('confirm', <?php echo $row['id_order']; ?>)">Gửi hàng</button>
              <?php elseif ($role === 'customer' && $row['status'] === 'Confirmed'): ?>
                <button class="confirm-button" onclick="handleOrder('receive', <?php echo $row['id_order']; ?>)">Nhận hàng</button>
                <button class="delete-button" onclick="handleOrder('cancel', <?php echo $row['id_order']; ?>)">Hủy đơn</button>
              <?php elseif ($role === 'employee' && $row['status'] === 'Cancelled'): ?>
                <button class="delete-button" onclick="handleOrder('delete', <?php echo $row['id_order']; ?>)">Xóa đơn</button>
              <?php endif; ?>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

    <!-- Thông Tin Người Dùng và Tổng Kết -->
    <div class="user-info-summary">
      <div class="info">
        <h2><?php echo $role === 'employee' ? 'Thông tin nhân viên' : 'Thông tin người dùng'; ?></h2>
        <p><strong>Tên:</strong> <?php echo htmlspecialchars($user_info[$role === 'employee' ? 'name_employee' : 'name_customer']); ?></p>
        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($user_info[$role === 'employee' ? 'phone_to' : 'phone_no']); ?></p>
      </div>

      <div class="summary">
        <h2>Tổng Hợp Đơn Hàng</h2>
        <p><strong>Tổng số lượng:</strong> <?php echo $total_quantity; ?></p>
        <p><strong>Tổng giá trị:</strong> <?php echo number_format($total_price, 0); ?> VND</p>
      </div>
    </div>
  </div>

  <?php include 'components\footer.php' ?>
</body>

</html>