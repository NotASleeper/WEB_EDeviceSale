<?php
include 'components/connect.php';
// not sure 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];

    //da tung import -> khong the xoa
    $find_emp_import = $conn->prepare("SELECT * FROM `import` WHERE id_employee = ?");
    $find_emp_import->execute([$emp_id]);

    $find_emp_export = $conn->prepare("SELECT * FROM `export` WHERE id_employee = ?");
    $find_emp_export->execute([$emp_id]);

    if ($find_emp_import->rowCount() > 0) {
        $message[] = "Cannot delete";
        // exit();
    } else if ($find_emp_export->rowCount() > 0) {
        $message[] = "Cannot delete";
        // exit();
    } else {

        //find category -> delete detail
        $select_emp = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
        $select_emp->execute([$emp_id]);
        if ($select_emp->rowCount() > 0) {

            $delete_emp = $conn->prepare("DELETE FROM `employee` WHERE id_employee = ?");
            $delete_emp->execute([$emp_id]);

            $confirm_delete  = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
            $confirm_delete->execute([$emp_id]);
            if ($confirm_delete->rowCount() == 0) {
                header("Location: employee.php"); // Replace with your page

            } else {
                $message[] = "Cannot delete";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Gadget</title>

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
        <h2 style='padding-bottom: 0.5rem'>CANNOT DELETE THIS EMPLOYEE</h2>
        <h2>Go back to Employee</h2>
        <a href="employee.php" class="btn-success" style="margin-top: 1rem; padding-top: 1rem">Employee</a>
    </section>
</body>

</html>