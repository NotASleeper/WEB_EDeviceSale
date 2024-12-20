<?php
include 'components/connect.php';
session_start();

// Kiểm tra đăng nhập
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

// Lấy sản phẩm trong giỏ hàng
$sql = "SELECT c.id_cart, g.id_gadget, g.name_gadget, g.pic_gadget, c.quantity, g.imp_gadget
        FROM cart c
        JOIN gadget g ON c.id_gadget = g.id_gadget
        WHERE c.id_customer = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/cus_header.php'; ?>

    <div class="cart-page">
        <h2>Giỏ Hàng Của Bạn</h2>
        <form method="POST" action="checkout.php">
            <?php if (!empty($cart_items)): ?>
                <div class="cart-items">
                    <?php
                    $total_quantity = 0;
                    $total_price = 0;
                    foreach ($cart_items as $item):
                        $subtotal = $item['quantity'] * $item['imp_gadget'];
                        $total_quantity += $item['quantity'];
                        $total_price += $subtotal;
                    ?>
                        <div class="cart-item">
                            <input type="checkbox" name="selected_items[]" value="<?= $item['id_cart']; ?>">
                            <img src="images/img_gadget/<?= $item['pic_gadget']; ?>" alt="<?= $item['name_gadget']; ?>">
                            <div class="details">
                                <a href="view_gadget_cus.php?id=<?= $item['id_gadget']; ?>">
                                    <h3><?= htmlspecialchars($item['name_gadget']); ?></h3>
                                </a>
                                <p>Số lượng: <?= $item['quantity']; ?></p>
                                <p>Giá: <?= number_format($item['imp_gadget'], 0); ?> VND</p>
                                <p>Thành tiền: <?= number_format($subtotal, 0); ?> VND</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary">
                    <button type="submit" name="checkout" class="checkout-button">Đặt Hàng</button>
                    <div>
                        <h3>Tổng Số Lượng: <?= $total_quantity; ?></h3>
                        <h3>Tổng Giá Trị: <?= number_format($total_price, 0); ?> VND</h3>
                    </div>
                </div>
            <?php else: ?>
                <p>Giỏ hàng của bạn trống!</p>
            <?php endif; ?>
        </form>
    </div>

    <?php include 'components/footer.php'; ?>
</body>

</html>
