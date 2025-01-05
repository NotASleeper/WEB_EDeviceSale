<?php
include 'components/connect.php';

session_start();

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

$is_Saved = false;
$select_detail = null;

if (isset($_GET['id_import'])) {
    $id_import = $_GET['id_import'];

    // Truy vấn chi tiết phiếu nhập
    $select_detail = $conn->prepare("SELECT import_detail.*, gadget.name_gadget 
        FROM import_detail 
        JOIN gadget ON import_detail.id_gadget = gadget.id_gadget 
        WHERE import_detail.id_import = :id_import");
    $select_detail->bindValue(':id_import', $id_import);
    $select_detail->execute();

    // Truy vấn thông tin phiếu nhập
    $select_import = $conn->prepare("SELECT * FROM import WHERE id_import = :id_import");
    $select_import->bindValue(':id_import', $id_import);
    $select_import->execute();
    if ($select_import->rowCount() > 0) {
        $import_info = $select_import->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "No import found for this ID.";
        exit;
    }
} else {
    // Nếu không có `id_import`, hiển thị form trống để tạo mới
    $import_info = [
        'id_import' => '',
        'id_employee' => '',
        'sum' => 0,
        'vat' => '',
        'date' => date('Y-m-d'),
    ];
}

//Lưu phiếu nhập
if (isset($_POST['save_import'])) {

    $id_employee = $_POST['id_employee'];
    $date = $_POST['date'];
    $sum = $_POST['sum'];
    $vat = $_POST['vat'];

    if (!isset($import_info['id_import']) || $import_info['id_import'] == '') {
        // Thêm phiếu nhập mới vào bảng `import`
        $insert_import = $conn->prepare("INSERT INTO import (id_employee, date, sum, vat) 
            VALUES (:id_employee, :date, :sum, :vat)");
        $insert_import->bindValue(':id_employee', $id_employee);
        $insert_import->bindValue(':date', $date);
        $insert_import->bindValue(':sum', $sum);
        $insert_import->bindValue(':vat', $vat);

        if ($insert_import->execute()) {
            $new_id_import = $conn->lastInsertId(); // Lấy ID phiếu nhập vừa tạo
            header("Location: create_import.php?id_import=" . $new_id_import);
        } else {
            echo "Failed to save import record.";
        }
    } else {
        // Nếu đã tồn tại ID, không cho phép thêm nữa
        echo "<script>alert('This import has already been saved!');</script>";
    }
}

//Xóa chi tiết phiếu nhập
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $id_import = $_GET['id_import'];

    // Lấy giá trị `total` của chi tiết phiếu nhập cần xóa
    $select_total = $conn->prepare("SELECT total FROM import_detail WHERE id_import_detail = :delete_id");
    $select_total->bindValue(':delete_id', $delete_id);
    $select_total->execute();

    if ($select_total->rowCount() > 0) {
        $row = $select_total->fetch(PDO::FETCH_ASSOC);
        $total = $row['total'];

        // Xóa chi tiết phiếu nhập
        $delete_detail = $conn->prepare("DELETE FROM import_detail WHERE id_import_detail = :delete_id");
        $delete_detail->bindValue(':delete_id', $delete_id);
        $delete_detail->execute();

        if ($delete_detail->rowCount() > 0) {
            // Cập nhật tổng tiền (sum) trong bảng import
            $update_import = $conn->prepare("UPDATE `import` 
                SET sum = sum - :total 
                WHERE id_import = :id_import");
            $update_import->bindValue(':total', $total);
            $update_import->bindValue(':id_import', $id_import);
            $update_import->execute();

            // Chuyển hướng để làm mới lại bảng
            header("Location: create_import.php?id_import=" . $id_import);
            exit;
        } else {
            echo "Failed to delete import detail.";
        }
    } else {
        echo "No such import detail found.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Import</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- starts header -->
    <?php include 'components\header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <section class="section-title">
        <a href="create_import.php">create import</a>
    </section>
    <!-- section title ends -->


    <section>
        <form action="create_import.php" method="POST">
            <div>
                <label for=""> ID Employee: </label>
                <input type="text" name="id_employee" value="<?= $import_info['id_employee']; ?>"
                    <?= $import_info['id_import'] ? 'readonly' : ''; ?> required> <br>
                <label for="">Date: </label>
                <input type="date" name="date" value="<?= $import_info['date']; ?>" readonly> <br>
                <label for="">Sum: </label>
                <input type="number" name="sum" value="<?= $import_info['sum']; ?>" readonly> <br>
                <label for="">VAT: </label>
                <input type="number" name="vat" value="<?= $import_info['vat']; ?>"
                    <?= $import_info['id_import'] ? 'readonly' : ''; ?> required> <br>
                <button type="submit" name="save_import" <?= $import_info['id_import'] ? 'disabled' : ''; ?>>Save
                    Import</button>
            </div>
        </form>
    </section>

    <!-- section products starts -->
    <section class="products">
        <div class="product-title">
            <h2>Import Detail</h2>
            <a class="btn-add" href="create_import_detail.php?id_import=<?= $import_info['id_import']; ?>">
                + Add New
            </a>
        </div>
        <div class="container-employee" style="overflow-x: auto; overflow-y: auto;">
            <table class="tbl-employee">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gadget</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    if ($select_detail && $select_detail->rowCount() > 0) {
                        while ($fetch_emp = $select_detail->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="pid" value="<?= $fetch_emp['id_import_detail']; ?>">
                                    <?= $fetch_emp['id_import_detail']; ?>
                                </td>
                                <td><?= $fetch_emp['name_gadget']; ?></td>
                                <td><?= $fetch_emp['im_price']; ?></td>
                                <td><?= $fetch_emp['quantity']; ?></td>
                                <td><?= $fetch_emp['total']; ?></td>
                                <td>
                                    <a href="create_import.php?delete_id=<?= $fetch_emp['id_import_detail']; ?>&id_import=<?= $id_import; ?>"
                                        onclick="return confirm('Are you sure you want to delete this item?');"><i
                                            class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td style="font-weight: bold;" colspan="8">NO DATA FOUND</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <!-- section products ends -->
    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->
</body>

</html>