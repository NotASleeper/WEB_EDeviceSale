<?php

header('Content-Type: application/json');
include 'components/connect.php'; // File kết nối database

// Nhận keyword từ request
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';

if (empty($keyword)) {
    echo json_encode(['status' => 'error', 'message' => 'Keyword is required']);
    exit;
}

try {
    // Kiểm tra kết nối database
    if (!$conn) {
        throw new Exception('Không thể kết nối cơ sở dữ liệu');
    }

    // Sử dụng PDO để tìm kiếm sản phẩm với keyword
    $stmt = $conn->prepare("SELECT id_gadget, name_gadget, pic_gadget, exp_gadget FROM gadget WHERE name_gadget LIKE :keyword LIMIT 5");
    $searchTerm = "%{$keyword}%";
    $stmt->bindParam(':keyword', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($products)) {
        echo json_encode([
            'status' => 'success',
            'products' => $products
        ]);
    } else {
        echo json_encode([
            'status' => 'not_found',
            'message' => 'Không tìm thấy sản phẩm nào phù hợp.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
