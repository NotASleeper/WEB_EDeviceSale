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

if (isset($_GET['id'])) {
    $gadget_id = $_GET['id'];

    //da tung import -> khong the xoa
    $find_gadget_import = $conn->prepare("SELECT * FROM `import_detail` WHERE id_gadget = ?");
    $find_gadget_import->execute([$gadget_id]);
    if ($find_gadget_import->rowCount() > 0) {
        $message[] = "Cannot delete";
        // exit();
    } else {
        //khong can kt export, vi can import truoc export

        //find category -> delete detail
        $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
        $select_gadget->execute([$gadget_id]);
        $row = $select_gadget->fetch(PDO::FETCH_ASSOC);
        $category = $row['category'];
        $pic = $row['pic_gadget'];

        // Prepare and execute the DELETE query on gadget
        $delete_gadget = $conn->prepare("DELETE FROM `gadget` WHERE id_gadget = ?");
        $delete_gadget->execute([$gadget_id]);

        $confirm_delete  = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
        $confirm_delete->execute([$gadget_id]);

        if ($confirm_delete->rowCount() == 0) {
            if (file_exists('images/img_gadget/' . $pic)) {
                unlink('images/img_gadget/' . $pic);
            }

            if ($category === 'laptop') {
                $delete_lap = $conn->prepare("DELETE FROM `laptop` WHERE id_gadget = ?");
                $delete_lap->execute([$gadget_id]);
            } else if ($category === 'smartphone') {
                $delete_phone = $conn->prepare("DELETE FROM `smartphone` WHERE id_gadget = ?");
                $delete_phone->execute([$gadget_id]);
            } else if ($category === 'smartwatch') {
                $delete_watch = $conn->prepare("DELETE FROM `smartwatch` WHERE id_gadget = ?");
                $delete_watch->execute([$gadget_id]);
            } else {
                $delete_ac = $conn->prepare("DELETE FROM `accessory` WHERE id_gadget = ?");
                $delete_ac->execute([$gadget_id]);
            }
            header("Location: home.php"); // Replace with your page
        } else {
            $message[] = "Cannot delete";
            // exit();
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
        <h2 style='padding-bottom: 0.5rem'>CANNOT DELETE THÍ GADGET</h2>
        <h2>Go back to home</h2>
        <a href="home.php" class="btn-success" style="margin-top: 1rem; padding-top: 1rem">Home</a>
    </section>
</body>

</html>