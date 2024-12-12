<?php
include 'components/connect.php';
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
        <h2>Customers</h2>
    </section>
    <!-- section report title end -->

    <!-- section report content start -->
    <section class="report-content">
        <table class="table">
            <tr class="table-header">
                <th>ID</th>
                <th class="name">Name</th>
                <th>Purchased</th>
                <th>Spent</th>
            </tr>
            <tr>
                <td>ID</td>
                <td class="name">Name</td>
                <td>Purchased</td>
                <td>Spent</td>
            </tr>
        </table>
    </section>
    <!-- section report content end -->
</body>

</html>