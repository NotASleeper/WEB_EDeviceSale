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



if (isset($_GET['cus_id'])) {
    $cus_id = $_GET['cus_id'];

    //da tung import -> khong the xoa
    $find_cus_export = $conn->prepare("SELECT * FROM `export` WHERE id_customer = ?");
    $find_cus_export->execute([$cus_id]);

    if ($find_cus_export->rowCount() > 0) {
        $message[] = "Cannot delete";
        // exit();
    } else {

        //find category -> delete detail
        $select_cus = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
        $select_cus->execute([$cus_id]);
        if ($select_cus->rowCount() > 0) {

            $delete_cus = $conn->prepare("DELETE FROM `customer` WHERE id_customer = ?");
            $delete_cus->execute([$cus_id]);

            $confirm_delete  = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
            $confirm_delete->execute([$cus_id]);
            if ($confirm_delete->rowCount() == 0) {
                header("Location: customer.php"); // Replace with your page

            } else {
                $message[] = "Cannot delete";
            }
        } else {
            $message[] = "Cannot delete";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Customer</title>

    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- starts header -->
    <?php include 'components\header.php' ?>
    <!-- ends header -->

    <section class="sec-delete-gadget">
        <h2 style='padding-bottom: 0.5rem'>CANNOT DELETE THIS CUSTOMER</h2>
        <h2>Go back to Customer</h2>
        <a href="customer.php" class="btn-success" style="margin-top: 1rem; padding-top: 1rem">Customer</a>
    </section>
</body>

</html>