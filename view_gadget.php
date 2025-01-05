<?php
include 'components/connect.php';

session_start();

// Kiểm tra người dùng đăng nhập và quyền truy cập
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'employee') {
    echo "Bạn không có quyền xem trang này!";
    exit();
}

// Lấy thông tin gadget và chi tiết theo danh mục
$gadget_data = null;
$gadget_details = null;

if (isset($_GET['id'])) {
    $gadget_id = intval($_GET['id']);
    $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
    $select_gadget->execute([$gadget_id]);

    if ($select_gadget->rowCount() > 0) {
        $gadget_data = $select_gadget->fetch(PDO::FETCH_ASSOC);

        // Lấy chi tiết theo danh mục (laptop, smartphone, smartwatch, accessory)
        $category = $gadget_data['category'];
        $detail_query = $conn->prepare("SELECT * FROM `$category` WHERE id_gadget = ?");
        $detail_query->execute([$gadget_id]);
        $gadget_details = $detail_query->fetch(PDO::FETCH_ASSOC);
    } else {
        header('Location: home.php');
        exit();
    }
} else {
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Gadget</title>
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header_footer.css">
</head>

<body>
    <!-- Header -->
    <?php include 'components/header_sim.php'; ?>

    <div class="gadget-detail-container">
        <?php if ($gadget_data): ?>
            <!-- Title -->
            <h1>Gadget Information</h1>

            <!-- Main Section -->
            <div class="gadget-main-section">
                <!-- Gadget Image -->
                <div>
                    <img src="images/img_gadget/<?php echo htmlspecialchars($gadget_data['pic_gadget'] ?? 'default.jpg'); ?>" alt="Gadget Image">
                </div>
                <!-- Gadget Information -->
                <div class="gadget-info">
                    <p class="gadget-name"><?php echo htmlspecialchars($gadget_data['name_gadget']); ?></p>
                    <p class="gadget-type"><?php echo ucfirst(htmlspecialchars($gadget_data['category'])); ?></p>
                    <p class="gadget-price">
                        <?php echo number_format($gadget_data['exp_gadget'], 0, '.', ','); ?> VND
                        <del><?php echo number_format($gadget_data['imp_gadget'], 0, '.', ','); ?> VND</del>
                    </p>
                    <p class="gadget-description"><?php echo htmlspecialchars($gadget_data['des_gadget']); ?></p>

                </div>

            </div>

            <!-- Detailed Information -->
            <div class="gadget-details">
                <h2><?php echo ucfirst(htmlspecialchars($gadget_data['category'])); ?> Information</h2>
                <table>
                    <tbody>
                        <?php foreach ($gadget_details as $key => $value): ?>
                            <tr>
                                <!-- Hiển thị tên cột bằng tiếng Anh, định dạng đẹp -->
                                <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></td>
                                <!-- Hiển thị giá trị của trường -->
                                <td><?php echo htmlspecialchars($value ?? 'Unknown'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Back Button -->
            <div style="text-align: center;">
                <button class="btn-success" onclick="window.location.href='home.php'">Back</button>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: red;">Gadget not found!</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php' ?>

    <script src="js/index.js"></script>
</body>

</html>