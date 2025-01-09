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
    // $import_info = [
    //     'id_import' => '',
    //     'id_employee' => '',
    //     'sum' => '',
    //     'vat' => '',
    //     'date' => date('Y-m-d'),
    // ];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Detail</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header_footer.css">

</head>

<body>
    <!-- starts header -->
    <?php include 'components\header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <section class="section-title">
        <a href="import_detail.php">import detail</a>
    </section>
    <!-- section title ends -->


    <section>
        <div>
            <label for=""> ID Employee: </label>
            <input type="text" name="id_employee" value="<?= $import_info['id_employee']; ?>" readonly> <br>
            <label for="">Date: </label>
            <input type="date" name="date" value="<?= $import_info['date']; ?>" readonly> <br>
            <label for="">Sum: </label>
            <input type="number" name="sum" value="<?= $import_info['sum']; ?>" readonly> <br>
            <label for="">VAT: </label>
            <input type="number" name="vat" value="<?= $import_info['vat']; ?>" readonly> <br>
        </div>
    </section>

    <!-- section products starts -->
    <section class="products">
        <div class="product-title">
            <h2>Import Detail</h2>
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

    <script src="js/index.js"></script>

</body>

</html>