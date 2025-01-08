<?php
include 'components/connect.php';

session_start();



if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'customer') {
    echo "Bạn không có quyền xem trang này!";
    exit();
}

$cus_id = $user_id;


$select_cus = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
$select_cus->execute([$cus_id]);
if ($select_cus->rowCount() == 0) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['submit'])) {
    $name_customer = $_POST['name_customer'];     //name
    $name_customer = filter_var($name_customer, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['date_of_birth'];     //bday
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    $phone_no = $_POST['phone_no'];     //phone no
    $phone_no = filter_var($phone_no, FILTER_SANITIZE_STRING);

    $username_customer = $_POST['username_customer'];     //username
    $username_customer = filter_var($username_customer, FILTER_SANITIZE_STRING);

    $pass_customer = $_POST['pass_customer'];     //password
    $pass_customer = filter_var($pass_customer, FILTER_SANITIZE_STRING);

    $update_customer = $conn->prepare("UPDATE `customer` SET name_customer = ?, date_of_birth = ?, phone_no = ?, username = ?, password = ? WHERE id_customer = ?");
    $update_customer->execute([$name_customer, $date_of_birth, $phone_no, $username_customer, $pass_customer, $cus_id]);

    $message[] = "Updated successfully";

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
    <title>Edit Customer Profile</title>
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
    <?php include 'components\cus_header.php' ?>
    <!-- ends header -->

    <!-- section edit profile starts -->
    <section class="create-gadget">
        <h1 style="color: yellow">UPDATE PROFILE</h1>
        <?php
        $select_customer = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
        $select_customer->execute([$cus_id]);
        if ($fetch_customer = $select_customer->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
            <h3>Name:</h3>
            <input name="name_customer" placeholder="Name" maxlength="50"
                value="<?= $fetch_customer['name_customer'] ?>" required>
            <h3>Date of Birth:</h3>
            <input name="date_of_birth" placeholder="Date of Birth" type="date"
                value="<?= $fetch_customer['date_of_birth'] ?>" required>
            <h3>Phone Number:</h3>
            <input name="phone_no" placeholder="Phone Number" maxlength="10" required
                value="<?= $fetch_customer['phone_no'] ?>">
            <h3>Username:</h3>
            <input name="username_customer" placeholder="Username" maxlength="99"
                value="<?= $fetch_customer['username'] ?>" required>
            <h3>Password:</h3>
            <input type="password" name="pass_customer" placeholder="Password" maxlength="99"
                value="<?= $fetch_customer['password'] ?>" required>

            <div class="gadget-buttons" style="margin-top: 1rem;">
                <button type="submit" name="submit" class="btn-success">Update</button>
                <button class="btn-second-green addgg-clear">Clear</button>
            </div>
            <?php
        }
            ?>
        </form>
    </section>
    <!-- section create_new_gadget ends -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>
</body>

</html>