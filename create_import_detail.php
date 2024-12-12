<?php
include 'components/connect.php';

session_start();

// Kiểm tra người dùng đã đăng nhập
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    // header('location:login.php');
}

// Lấy danh sách gadget
$gadgets = $conn->prepare("SELECT id_gadget, name_gadget, imp_gadget FROM gadget");
$gadgets->execute();
$gadget_list = $gadgets->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $id_import = $_POST['id_import'];
    // $id_import = filter_var($id_import, FILTER_SANITIZE_STRING);

    $id_gadget = $_POST['id_gadget'];
    $id_gadget = filter_var($id_gadget, FILTER_SANITIZE_STRING);

    $im_price = $_POST['im_price'];
    $im_price = filter_var($im_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $quantity = $_POST['quantity'];
    $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);

    // Tính tổng
    $total = $im_price * $quantity;

    // Kiểm tra trùng lặp chi tiết phiếu nhập
    $select_detail = $conn->prepare("SELECT * FROM `import_detail` WHERE id_import = ? AND id_gadget = ?");
    $select_detail->execute([$id_import, $id_gadget]);

    if ($select_detail->rowCount() > 0) {
        $message[] = "This import detail already exists!";
    } else {
        // Thêm chi tiết phiếu nhập vào cơ sở dữ liệu
        $insert_detail = $conn->prepare("INSERT INTO `import_detail` (id_import, id_gadget, im_price, quantity, total) VALUES (?,?,?,?,?)");
        $insert_detail->execute([$id_import, $id_gadget, $im_price, $quantity, $total]);

        // Kiểm tra kết quả
        if ($insert_detail->rowCount() > 0) {
            // Cập nhật tổng tiền (sum) của phiếu nhập
            $update_import = $conn->prepare("UPDATE `import` 
                SET sum = sum + :total 
                WHERE id_import = :id_import");
            $update_import->bindValue(':total', $total);
            $update_import->bindValue(':id_import', $id_import);
            $update_import->execute();
            
            // $message[] = "Import detail added successfully!";
            header("Location: create_import.php?id_import=" . $id_import);
        } else {
            $message[] = "Failed to add import detail.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Import Detail</title>

    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Header -->
    <?php include 'components\header_sim.php'; ?>

    <!-- Section Create Import Detail -->
    <section class="create-gadget">
        <h1 style="color: yellow">CREATE A NEW IMPORT DETAIL</h1>

        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
            <h3>Import ID:</h3>
            <input name="id_import" placeholder="Import ID" maxlength="50" value="<?= $_GET['id_import'] ?>" readonly
                required>
            <h3>Gadget:</h3>
            <!-- <input name="id_gadget" placeholder="Gadget ID" maxlength="50" value="" required> -->
            <select name="id_gadget" id="id_gadget" onchange="updatePrice()" required>
                <option value="" disabled selected>Select a gadget</option>
                <?php foreach ($gadget_list as $gadget): ?>
                <option value="<?= $gadget['id_gadget']; ?>" data-price="<?= $gadget['imp_gadget']; ?>">
                    <?= $gadget['name_gadget']; ?>
                </option>
                <?php endforeach; ?>
            </select>
            <h3>Import Price:</h3>
            <input name="im_price" id="im_price" placeholder="Import Price" type="number" step="0.01" value="" readonly
                required>
            <h3>Quantity:</h3>
            <input name="quantity" placeholder="Quantity" type="number" value="" required>

            <div class="gadget-buttons" style="margin-top: 1rem;">
                <button type="submit" name="submit" class="btn-success">Add</button>
                <button type="reset" class="btn-second-green addgg-clear">Clear</button>
            </div>
        </form>
        <script>
        function updatePrice() {
            const gadgetSelect = document.getElementById('id_gadget');
            const priceInput = document.getElementById('im_price');

            const selectedOption = gadgetSelect.options[gadgetSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');

            priceInput.value = price || '';
        }
        </script>
    </section>

    <!-- Footer -->
    <?php include 'components\footer.php'; ?>
</body>

</html>