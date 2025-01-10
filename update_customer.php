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

$select_password = $conn->prepare("SELECT password FROM `employee` WHERE id_employee = ?");
$select_password->execute([$user_id]);
$user_password = $select_password->fetchColumn();

if (isset($_GET['id_customer'])) {
    $cus_id = intval($_GET['id_customer']);

    $select_cus = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
    $select_cus->execute([$cus_id]);
    if ($select_cus->rowCount() == 0) {
        header('Location: customer.php');
        exit();
    }
} else {
    header('Location: customer.php');
    exit();
}

$select_customer = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
$select_customer->execute([$cus_id]);
$fetch_customer = $select_customer->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    $name_customer = $_POST['name_customer'];     //name
    $name_customer = filter_var($name_customer, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['date_of_birth'];     //bday
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    // Kiểm tra tuổi
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    if ($age < 18) {
        $message[] = "Customer should be over 18 years old";
    } else {
        $phone_no = $_POST['phone_no'];     //phone no
        $phone_no = filter_var($phone_no, FILTER_SANITIZE_STRING);

        $username_customer = $_POST['username_customer'];     //username
        $username_customer = filter_var($username_customer, FILTER_SANITIZE_STRING);

        $pass_customer = $_POST['pass_customer'];     //password
        $pass_customer = filter_var($pass_customer, FILTER_SANITIZE_STRING);

        if ($pass_customer === '') {
            $update_customer = $conn->prepare("UPDATE `customer` SET name_customer = ?, date_of_birth = ?, phone_no = ?, username = ? WHERE id_customer = ?");
            $update_customer->execute([$name_customer, $date_of_birth, $phone_no, $username_customer, $cus_id]);
        } else {
            $hashed_password = password_hash($pass_customer, PASSWORD_DEFAULT);

            $update_customer = $conn->prepare("UPDATE `customer` SET name_customer = ?, date_of_birth = ?, phone_no = ?, username = ?, password = ? WHERE id_customer = ?");
            $update_customer->execute([$name_customer, $date_of_birth, $phone_no, $username_customer, $hashed_password, $cus_id]);
        }

        header("location: customer.php");
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
    <title>Update Customer</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/header_footer.css">

</head>

<body>
    <!-- starts header -->
    <?php include 'components\header.php' ?>
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
                    <button type="submit" id="editBtn" name="submit" class="profile-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                    <button type="reset" class="profile-btn clear addgg-clear"><i class="fa-solid fa-eraser"></i> Clear</button>
                </div>
            </div>
        </form>
    </section>

    <!-- Password Confirmation Dialog -->
    <div id="confirmPasswordDialog" class="dialog" style="display: none;">
        <div class="dialog-content">
            <h2>Enter Your Password</h2>
            <input type="password" id="confirmPasswordInput" placeholder="Enter your password" required>
            <div class="dialog-buttons">
                <button type="button" id="confirmPasswordBtn">Confirm</button>
                <button type="button" id="cancelDialogBtn">Cancel</button>
            </div>
        </div>
    </div>

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->
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

        document.addEventListener('DOMContentLoaded', function() {
            const editBtn = document.getElementById('editBtn'); // Nút Edit
            const confirmPasswordDialog = document.getElementById('confirmPasswordDialog'); // Hộp thoại xác nhận mật khẩu
            const confirmPasswordInput = document.getElementById('confirmPasswordInput'); // Input mật khẩu
            const confirmPasswordBtn = document.getElementById('confirmPasswordBtn'); // Nút Confirm
            const cancelDialogBtn = document.getElementById('cancelDialogBtn'); // Nút Cancel
            const updateForm = document.getElementById('updateForm'); // Form cần submit
            let isPasswordConfirmed = false; // Biến trạng thái xác nhận mật khẩu

            // Khi nhấn nút Edit, kiểm tra trạng thái xác nhận mật khẩu
            editBtn.onclick = function(event) {
                if (!isPasswordConfirmed) {
                    // Nếu chưa xác nhận mật khẩu, chặn hành vi gửi form
                    event.preventDefault();
                    confirmPasswordDialog.style.display = 'flex'; // Hiển thị hộp thoại
                }
            };


            confirmPasswordBtn.addEventListener('click', function() {
                const enteredPassword = confirmPasswordInput.value;

                // Gửi mật khẩu đến PHP để xác thực
                fetch('verify_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            password: enteredPassword
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Nếu mật khẩu đúng, đóng hộp thoại và cho phép gửi form
                            isPasswordConfirmed = true;
                            confirmPasswordDialog.style.display = 'none';

                            // Thực hiện click lại nút Edit để gửi form
                            editBtn.click();
                        } else {
                            // Hiển thị thông báo lỗi từ server
                            alert(data.message || 'Incorrect password! Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again.');
                    });
            });

            // Xử lý khi nhấn Cancel trong hộp thoại
            cancelDialogBtn.addEventListener('click', function() {
                // Ẩn hộp thoại mà không thực hiện gì
                confirmPasswordDialog.style.display = 'none';
            });
        });
    </script>
    <script src="js/index.js"></script>
</body>

</html>

<!-- onkeydown="return event.keyCode !== 69" -->

<!-- neu k filter + sani -> <script>alert('Hacked!');</script> -->