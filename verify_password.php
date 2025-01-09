<?php
include 'components/connect.php';
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$password = $_POST['password'] ?? '';

// Lấy mật khẩu đã mã hóa từ database
$select_user = $conn->prepare("SELECT password FROM `employee` WHERE id_employee = ?");
$select_user->execute([$user_id]);
if ($select_user->rowCount() > 0) {
    $stored_password = $select_user->fetchColumn();

    // Kiểm tra mật khẩu
    if (password_verify($password, $stored_password)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
exit();
