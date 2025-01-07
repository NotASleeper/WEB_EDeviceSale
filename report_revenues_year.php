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

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Year', 'Expenses', 'Revenues', 'Profit'],
                ['2016', 190, 240, 750],
                ['2017', 100, 420, 680],
                ['2018', 100, 540, 350],
                ['2019', 930, 340, 350],
                ['2020', 740, 930, 220],
                ['2021', 100, 430, 320],
                ['2022', 1030, 540, 350],
            ]);

            var options = {
                // title: 'Company Performance',
                title: 'Expenses, Revenues, and Profit',
                legend: {
                    position: 'bottom',
                },

            };

            var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>
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
        <h2>Revenues</h2>
    </section>
    <!-- section report title end -->

    <!-- section revenues range start -->
    <section class="revenues-range">
        <table class="table">
            <tr>
                <td>By Year</td>
                <td><a href="report_revenues_month.php">By Month</a></td>
            </tr>
            <tr>
                <td>
                    <hr class="green" />
                </td>
                <td>
                    <hr class="gray" />
                </td>
            </tr>
        </table>
    </section>
    <!-- section revenues range end -->

    <!-- section report content start -->
    <section class="column-chart">
        <div id="columnchart_material" style="width: 98%; height: 500px; margin: auto"></div>
    </section>
    <!-- section report content end -->
</body>

</html>