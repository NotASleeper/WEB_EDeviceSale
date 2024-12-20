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
    echo "Bạn không có quyền thực hiện hành động này!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_gadget = $_POST['pid'];
    $quantity = $_POST['quantity'] ?? 1;

    // Kiểm tra dữ liệu đầu vào
    if (empty($id_gadget) || $quantity <= 0) {
        echo "Dữ liệu không hợp lệ!";
        exit();
    }

    // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
    $stmt = $conn->prepare("SELECT * FROM cart WHERE id_customer = :id_customer AND id_gadget = :id_gadget");
    $stmt->execute([
        ':id_customer' => $user_id,
        ':id_gadget' => $id_gadget,
    ]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Nếu sản phẩm đã tồn tại, tăng số lượng
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + :quantity WHERE id_cart = :id_cart");
        $stmt->execute([
            ':quantity' => $quantity,
            ':id_cart' => $cart_item['id_cart'],
        ]);
    } else {
        // Thêm sản phẩm mới vào giỏ hàng
        $stmt = $conn->prepare("INSERT INTO cart (id_customer, id_gadget, quantity) VALUES (:id_customer, :id_gadget, :quantity)");
        $stmt->execute([
            ':id_customer' => $user_id,
            ':id_gadget' => $id_gadget,
            ':quantity' => $quantity,
        ]);
    }

    header('location: cart.php');
    exit();
}
?>
