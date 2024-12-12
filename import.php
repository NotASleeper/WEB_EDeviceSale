<?php
include 'components/connect.php';

session_start();

// not sure 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';

    // //pls un-cmt this when done
    // header('location:login.php');
}

//find var -> empty
$search_query = '';

$search_from='';
$search_to='';

if (isset($_GET['txt_input_from'])) {
    $search_from=$_GET['txt_input_from'];
    
}

if (isset($_GET['txt_input_to'])) {
    $search_to=$_GET['txt_input_to'];
}

if ($search_from != '' && $search_to != '')
{
    $select_emp = $conn->prepare("SELECT import.*, employee.name_employee
        FROM import 
        JOIN employee ON import.id_employee = employee.id_employee
        WHERE `date` >= :search_from AND `date` <= :search_to");
        
    $select_emp->bindValue(':search_from',$search_from);
    $select_emp->bindValue(':search_to',$search_to);
} else if ($search_from != '') {
    $select_emp = $conn->prepare("SELECT import.*, employee.name_employee
        FROM import 
        JOIN employee ON import.id_employee = employee.id_employee
        WHERE `date` >= :search_from");
        
    $select_emp->bindValue(':search_from',$search_from);

} else if ($search_to != '') {
    $select_emp = $conn->prepare("SELECT import.*, employee.name_employee
        FROM import 
        JOIN employee ON import.id_employee = employee.id_employee
        WHERE`date` <= :search_to");
        
    $select_emp->bindValue(':search_to',$search_to);

} else {
    $select_emp = $conn->prepare("SELECT import.*, employee.name_employee 
        FROM import 
        JOIN employee ON import.id_employee = employee.id_employee");
}

$select_emp->execute();

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import</title>
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
        <a href="import.php">import</a>
    </section>
    <!-- section title ends -->

    <!-- section search starts -->
    <section class="">
        <form class="" action="import.php" method="GET" enctype="multipart/form-data">
            <label>From:</label>
            <input name="txt_input_from" type="date"
                value="<?= isset($_GET['txt_input_from']) ? $_GET['txt_input_from'] : ''; ?>">
            <label>To: </label>
            <input name="txt_input_to" type="date"
                value="<?= isset($_GET['txt_input_to']) ? $_GET['txt_input_to'] : ''; ?>">
            <button style="background-color: white;" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section>
    <!-- section search ends -->

    <!-- section products starts -->
    <section class="products">
        <div class="product-title">
            <h2>Import</h2>
            <a class="btn-add" href="create_import.php">+ Add New</a>
        </div>
        <div class="container-employee" style="overflow-x: auto; overflow-y: auto;">
            <table class="tbl-employee">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Sum</th>
                        <th>VAT</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    // $select_products = $conn->prepare("SELECT * FROM `gadget`");
                    // $select_products->execute();
                    if ($select_emp->rowCount() > 0) {
                        while ($fetch_emp = $select_emp->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="pid" value="<?= $fetch_emp['id_import']; ?>">
                            <?= $fetch_emp['id_import']; ?>
                        </td>
                        <td><?= $fetch_emp['name_employee']; ?></td>
                        <td><?= date('d-m-Y', strtotime($fetch_emp['date'])) ?></td>
                        <td><?= $fetch_emp['sum']; ?></td>
                        <td><?= $fetch_emp['vat']; ?></td>
                        <td>
                            <a href="import_detail.php?id_import=<?= $fetch_emp['id_import']; ?>">View</a>
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