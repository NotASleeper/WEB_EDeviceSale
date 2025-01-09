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

$query = "SELECT 
  MONTHNAME(COALESCE(a.Month, b.Month)) AS Month,
  COALESCE(a.Revenues, 0) AS Revenues,
  COALESCE(b.Expenses, 0) AS Expenses,
  (COALESCE(a.Revenues, 0) - COALESCE(b.Expenses, 0)) AS Profit
FROM (
  SELECT Month, SUM(total_order + total_export) AS Revenues
  FROM (
    SELECT DATE_FORMAT(o.created_at, '%Y-%m-01') AS Month, SUM(g.exp_gadget * od.quantity) AS total_order, 0 AS total_export
    FROM orders o
    JOIN order_details od ON o.id_order = od.id_order
    JOIN gadget g ON od.id_gadget = g.id_gadget
    WHERE YEAR(o.created_at) = YEAR(CURDATE())
    GROUP BY Month
    UNION
    SELECT DATE_FORMAT(e.date, '%Y-%m-01') AS Month, 0 AS total_order, SUM(ed.ex_price * ed.quantity) AS total_export
    FROM export e
    JOIN export_detail ed ON e.id_export = ed.id_export
    WHERE YEAR(e.date) = YEAR(CURDATE())
    GROUP BY Month
  ) AS combined
  GROUP BY Month
) a
LEFT JOIN (
  SELECT DATE_FORMAT(i.date, '%Y-%m-01') AS Month, SUM(id.im_price * id.quantity) AS Expenses
  FROM import i
  JOIN import_detail id ON i.id_import = id.id_import
  WHERE YEAR(i.date) = YEAR(CURDATE())
  GROUP BY Month
) b ON a.Month = b.Month
ORDER BY a.Month ASC;
";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
foreach ($result as $row) {
    $data[] = [$row['Month'], (int)$row['Revenues'], (int)$row['Expenses'], (int)$row['Profit']];
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
                ['Month', 'Expenses', 'Revenues', 'Profit'],
                <?php
                foreach ($data as $entry) {
                    echo "['{$entry[0]}', {$entry[1]}, {$entry[2]}, {$entry[3]}],";
                }
                ?>
            ]);

            var options = {
                // title: 'Company Performance',
                title: 'Expenses, Revenues, and Profit: current year',
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
                <td><a href="report_revenues_year.php">By Year</a></td>
                <td>By Month</td>
            </tr>
            <tr>
                <td>
                    <hr class="gray" />
                </td>
                <td>
                    <hr class="green" />
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

    <script src="js/index.js"></script>

</body>

</html>