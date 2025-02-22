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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header_footer.css">

</head>

<body>
    <!-- starts header -->
    <?php include 'components\header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <section class="section-title">
        <a href="">report</a>
    </section>
    <!-- section title ends -->

    <!-- section choose report type start -->
    <?php include 'components\report_buttons.php' ?>
    <!-- section choose report type end -->

    <!-- section report title start -->
    <section class="report-title">
        <h2>Goods</h2>
    </section>
    <!-- section report title end -->

    <!-- section report content start -->
    <section class="products">
        <div class="container-employee" style="overflow-x: auto; overflow-y: auto;">
            <table class="tbl-employee">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="name">Name</th>
                        <th>Sold</th>
                        <th>Remain</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT g.id_gadget, g.name_gadget, IFNULL(SUM(od.quantity), 0) AS sold, g.quantity AS remain
                            FROM gadget g LEFT JOIN order_details od ON g.id_gadget = od.id_gadget
                            GROUP BY g.id_gadget, g.name_gadget;";
                    $result = $conn->query($sql);
                    if ($result->rowCount() > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                            <tr>
                                <td><?= $row['id_gadget']; ?></td>
                                <td class="name"><?= $row['name_gadget']; ?></td>
                                <td><?= $row['sold']; ?></td>
                                <td><?= $row['remain']; ?></td>
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
    <!-- section report content end -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>

</body>

</html>