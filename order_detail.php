<?php

include 'components/connect.php';

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




// Lấy ID sản phẩm từ URL
$id_gadget = isset($_GET['id_gadget']) ? (int)$_GET['id_gadget'] : 0;

$role = $_SESSION['role']; // Role của người dùng hiện tại
$user_id = $_SESSION['user_id']; // ID người dùng hiện tại

// Truy vấn thông tin chung của sản phẩm từ bảng `gadget`
$sql = "SELECT * FROM gadget WHERE id_gadget = :id_gadget";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':id_gadget', $id_gadget, PDO::PARAM_INT);
$stmt->execute();
$gadget = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy sản phẩm
if (!$gadget) {
  echo "<p>Sản phẩm không tồn tại.</p>";
  exit();
}

// Lấy thông tin chi tiết của sản phẩm dựa vào loại (category)
$category = $gadget['category'];
$details_sql = "SELECT * FROM $category WHERE id_gadget = :id_gadget";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bindValue(':id_gadget', $id_gadget, PDO::PARAM_INT);
$details_stmt->execute();
$details = $details_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi Tiết Sản Phẩm</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <?php if ($role === 'customer'): ?>
    <?php include 'components/cus_header.php' ?>
  <?php elseif ($role === 'employee'): ?>
    <?php include 'components/header.php' ?>
  <?php endif; ?>

  <div class="gadget-detail-container">
    <h1>Chi Tiết Sản Phẩm</h1>
    <div class="gadget-main-section">
      <!-- Gadget Info Section -->
      <img src="./images/img_gadget/<?php echo $gadget['pic_gadget'] ?>" alt="<?php echo htmlspecialchars($gadget['name_gadget']); ?>" class="gadget-image">
      <div class="gadget-info">
        <h2 class="gadget-name"><?php echo htmlspecialchars($gadget['name_gadget']); ?></h2>
        <p class="gadget-type"> <?php echo htmlspecialchars($category); ?></p>
        <p class="gadget-price"> <del><?php echo number_format($gadget['exp_gadget'], 2); ?></del> <?php echo number_format($gadget['imp_gadget'], 2); ?> VND</p>
        <p class="gadget-description"><strong>Giới thiệu: </strong><?php echo htmlspecialchars($gadget['des_gadget']); ?></p>
      </div>
    </div>

    <!-- Gadget Details Section -->
    <div class="gadget-details">
      <h2>Thông Tin Chi Tiết</h2>
      <table>
        <?php foreach ($details as $key => $value): ?>
          <?php if ($key !== 'id_gadget' && strpos($key, 'id_') === false): // Loại bỏ id_gadget và các khóa chính 
          ?>
            <tr>
              <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong></td>
              <td><?php echo htmlspecialchars($value); ?></td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  <?php include 'components\footer.php' ?>
</body>

</html>