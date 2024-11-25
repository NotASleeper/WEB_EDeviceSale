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

if (isset($_POST['submit'])) {
    $name_customer = $_POST['name_customer'];     //name
    $name_customer = filter_var($name_customer, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['date_of_birth'];     //import price
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    $phone_no = $_POST['phone_no'];     //export price
    $phone_no = filter_var($phone_no, FILTER_SANITIZE_STRING);

    //check if already exists
    $select_cus = $conn->prepare("SELECT * FROM `customer` WHERE phone_no = ?");
    $select_cus->execute([$phone_no]);
    $row = $select_cus->fetch(PDO::FETCH_ASSOC);
    //if existed
    if ($select_cus->rowCount() > 0) {
        $message[] = "Phone has already existed!";
    } else {
        //if new, not exists

        //insert to db
        $insert_cus = $conn->prepare("INSERT INTO `customer` (name_customer, date_of_birth, phone_no, total_spending) VALUES (?,?,?,?)");
        $insert_cus->execute([$name_customer, $date_of_birth, $phone_no, 0]);

        //check
        $confirm_cus = $conn->prepare("SELECT * FROM `customer` WHERE phone_no = ?");
        $confirm_cus->execute([$phone_no]);
        if ($confirm_cus->rowCount() > 0) {
            $message[] = "Inserted successfully";
        } else {
            $message[] = "Failed to Insert";
        }
    }

    // echo "<script>
    //     alert('Name: $name, Import Price: $im_price, Export Price: $ex_price, Description: $description, Category: $category');
    // </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- starts header -->
    <?php include 'components\header_sim.php' ?>
    <!-- ends header -->

    <!-- section create_new_gadget starts -->
    <section class="create-gadget">
        <h1 style="color: yellow">CREATE A NEW CUSTOMER</h1>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
            <h3>Name:</h3>
            <input name="name_customer" placeholder="Name" maxlength="50" value="" required>
            <h3>Date of Birth:</h3>
            <input name="date_of_birth" placeholder="Date of Birth" type="date" value="" required>
            <h3>Phone Number:</h3>
            <input name="phone_no" placeholder="Phone Number" maxlength="10" required>

            <div class="gadget-buttons" style="margin-top: 1rem;">
                <button type="submit" name="submit" class="btn-success">Add</button>
                <button class="btn-second-green addgg-clear">Clear</button>
            </div>

        </form>
    </section>
    <!-- section create_new_gadget ends -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>
</body>

</html>

<!-- onkeydown="return event.keyCode !== 69" -->

<!-- neu k filter + sani -> <script>alert('Hacked!');</script> -->