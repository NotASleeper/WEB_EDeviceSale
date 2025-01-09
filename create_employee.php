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

$avatar_url = 'https://avatar.iran.liara.run/public/boy';

if (isset($_POST['submit'])) {
    $name_employee = $_POST['name_employee'];     //name
    $name_employee = filter_var($name_employee, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['date_of_birth'];     //import price
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    $citizen_card = $_POST['citizen_card'];     //export price
    $citizen_card = filter_var($citizen_card, FILTER_SANITIZE_STRING);

    $gender = isset($_POST['gender']) ? filter_var($_POST['gender'], FILTER_SANITIZE_STRING) : null;

    $phone_to = $_POST['phone_to'];     //export price
    $phone_to = filter_var($phone_to, FILTER_SANITIZE_STRING);

    // $role = $_POST['role-select'];     //role-select
    // $role = filter_var($role, FILTER_SANITIZE_STRING);

    $usr_name = $_POST['usr_name'];     //usr_name
    $usr_name = filter_var($usr_name, FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'];     //pass-select
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);


    //check if already exists
    $select_emp = $conn->prepare("SELECT * FROM `employee` WHERE phone_to = ? OR username = ?");
    $select_emp->execute([$phone_to, $user_name]);
    $row = $select_emp->fetch(PDO::FETCH_ASSOC);
    //if existed
    if ($select_emp->rowCount() > 0) {
        $message[] = "Phone has already existed!";
    } else {
        //if new, not exists
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        //insert to db
        $insert_emp = $conn->prepare("INSERT INTO `employee` (name_employee, date_of_birth, citizen_card, gender, phone_to, state, username, password) VALUES (?,?,?,?,?,?,?,?)");
        $insert_emp->execute([$name_employee, $date_of_birth, $citizen_card, $gender, $phone_to, "Available", $usr_name, $hashed_password]);

        //check
        $confirm_emp = $conn->prepare("SELECT * FROM `employee` WHERE phone_to = ?");
        $confirm_emp->execute([$phone_to]);
        if ($confirm_emp->rowCount() > 0) {
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
    <title>Create Employee</title>

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

    <!-- section create_new_gadget starts -->
    <section class="user-profile">
        <img src="./images/sub_bg.png" alt="Background" class="bg-1">
        <img src="./images/sub_bg.png" alt="Background" class="bg-2">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($avatar_url) ?>" alt="avatar">
            <p class="header-title">Create new User Profile <br> <span>Employee</span></p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="updateForm" class="profile-body">
            <div class="panel">
                <h1 class="panel-title">General Information</h1>
                <div class="profile-item">
                    <label><i class="fa-solid fa-user"></i> Name</label>
                    <input name="name_employee" placeholder="Name" maxlength="50" value="" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-cake-candles"></i> Date of Birth</label>
                    <input name="date_of_birth" type="date" value="" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-id-card"></i> Citizen Card</label>
                    <input name="citizen_card" placeholder="Citizen Card" maxlength="12" value="" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-venus-mars"></i> Gender</label>
                    <div class="gender-select">
                        <label>
                            <input type="radio" name="gender" value="Male">
                            Male
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Female">
                            Female
                        </label>
                    </div>
                </div>

            </div>

            <div class="panel">
                <h1 class="panel-title">Account Information</h1>
                <div class="profile-item">
                    <label><i class="fa-regular fa-circle-user"></i> Username</label>
                    <input name="usr_name" placeholder="Username" maxlength="99" value="" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-key"></i> Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="pass" placeholder="Password" maxlength="99" value="">
                        <i id="toggle-password" class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-address-book"></i> Contact</label>
                    <input name="phone_to" placeholder="Phone Number" maxlength="15" value="" required>
                </div>
                <div class="profile-item row">
                    <button type="submit" id="editBtn" name="submit" class="profile-btn"><i class="fa-solid fa-pen-to-square"></i> Create</button>
                    <button type="reset" class="profile-btn clear addgg-clear"><i class="fa-solid fa-eraser"></i> Clear</button>
                </div>
            </div>
        </form>
    </section>
    <!-- section create_employee ends -->

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
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.classList.toggle('fa-eye-slash', isPassword);
            this.classList.toggle('fa-eye', !isPassword);
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