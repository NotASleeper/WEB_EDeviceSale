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

// Truy xuất thông tin khách hàng
$select_cus = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
$select_cus->execute([$cus_id]);
if ($select_cus->rowCount() == 0) {
    header('Location: login.php');
    exit();
}
$fetch_customer = $select_cus->fetch(PDO::FETCH_ASSOC);
$total_spending = $fetch_customer['total_spending']; // Lấy tổng chi tiêu của khách hàng

if (isset($_POST['submit'])) {
    $name_customer = filter_var($_POST['name_customer'], FILTER_SANITIZE_STRING);
    $date_of_birth = filter_var($_POST['date_of_birth'], FILTER_SANITIZE_STRING);

    // Kiểm tra tuổi
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    if ($age < 18) {
        $message[] = "Khách hàng phải từ 18 tuổi trở lên!";
    } else {
        $phone_no = filter_var($_POST['phone_no'], FILTER_SANITIZE_STRING);
        $username_customer = filter_var($_POST['username_customer'], FILTER_SANITIZE_STRING);
        $pass_customer = filter_var($_POST['pass_customer'], FILTER_SANITIZE_STRING);

        if ($pass_customer === '') {
            // Cập nhật thông tin khách hàng
            $update_customer = $conn->prepare("UPDATE `customer` SET name_customer = ?, date_of_birth = ?, phone_no = ?, username = ? WHERE id_customer = ?");
            $update_customer->execute([$name_customer, $date_of_birth, $phone_no, $username_customer, $cus_id]);
        } else {
            $hashed_password = password_hash($pass_customer, PASSWORD_DEFAULT);

            // Cập nhật thông tin khách hàng
            $update_customer = $conn->prepare("UPDATE `customer` SET name_customer = ?, date_of_birth = ?, phone_no = ?, username = ?, password = ? WHERE id_customer = ?");
            $update_customer->execute([$name_customer, $date_of_birth, $phone_no, $username_customer, $hashed_password, $cus_id]);
        }

        header('location:edit_profile_cus.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer Profile</title>
    <link rel="icon" href="images/logocart.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/header_footer.css">


</head>

<body>
    <!-- starts header -->
    <?php include 'components/cus_header.php'; ?>
    <!-- ends header -->
    <section class="user-profile">
        <img src="./images/sub_bg.png" alt="sub_bg" class="bg-1">
        <img src="./images/sub_bg.png" alt="sub_bg" class="bg-2">
        <div class="profile-header">
            <img src="https://avatar.iran.liara.run/public" alt="avatar">
            <p class="header-title">User profile <br> <span>Customer</span></p>
        </div>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data" class="profile-body">
            <div class="panel">
                <h1 class="panel-title">General Information</h1>
                <div class="profile-item">
                    <label><i class="fa-solid fa-user"></i> Username</label>
                    <input name="name_customer" placeholder="Name" maxlength="50" value="<?= $fetch_customer['name_customer'] ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-cake-candles"></i> Day of birth</label>
                    <input name="date_of_birth" placeholder="Date of Birth" type="date" value="<?= $fetch_customer['date_of_birth'] ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-phone"></i> Phone number</label>
                    <input name="phone_no" placeholder="Phone Number" maxlength="10" required value="<?= $fetch_customer['phone_no'] ?>">
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-wallet"></i> Total Spending</label>
                    <input id="total_spending" name="total_spending" type="text" placeholder="Total Spending" value="<?= number_format($total_spending, 2) ?>" readonly>
                </div>
            </div>
            <div class="panel">
                <h1 class="panel-title">Account Information</h1>
                <div class="profile-item">
                    <label><i class="fa-regular fa-circle-user"></i> Account</label>
                    <input name="username_customer" placeholder="Username" maxlength="99" value="<?= $fetch_customer['username'] ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-key"></i> Password</label>
                    <div class="password-wrapper">
                        <input id="password" name="pass_customer" type="password" placeholder="Password" maxlength="99" value="">
                        <i id="toggle-password" class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="profile-item row">
                    <button type="submit" name="submit" class="profile-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                    <button type="reset" class="profile-btn clear"><i class="fa-solid fa-eraser"></i> Clear</button>
                </div>
            </div>
        </form>
    </section>
    <?php include 'components/footer.php'; ?>



    <script src="js/index.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');

            togglePassword.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.classList.remove('fa-eye');
                    togglePassword.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    togglePassword.classList.remove('fa-eye-slash');
                    togglePassword.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>

</html>