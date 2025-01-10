<?php
include 'components/connect.php';

session_start();

// Kiểm tra đăng nhập và quyền
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

// Lấy thông tin nhân viên
$select_emp = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
$select_emp->execute([$user_id]);
if ($select_emp->rowCount() == 0) {
    header('Location: login.php');
    exit();
}

$fetch_employee = $select_emp->fetch(PDO::FETCH_ASSOC);

// Xác định đường dẫn ảnh đại diện dựa trên giới tính
$avatar_url = $fetch_employee['gender'] === 'Male'
    ? 'https://avatar.iran.liara.run/public/boy'
    : 'https://avatar.iran.liara.run/public/girl';

// Cập nhật thông tin nhân viên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_employee = filter_input(INPUT_POST, 'name_employee', FILTER_SANITIZE_STRING);
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);

    // Kiểm tra tuổi
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    if ($age < 18) {
        $message[] = "User should be over 18 years old";
    } else {
        $citizen_card = filter_input(INPUT_POST, 'citizen_card', FILTER_SANITIZE_STRING);
        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        $phone_to = filter_input(INPUT_POST, 'phone_to', FILTER_SANITIZE_STRING);
        $usr_name = filter_input(INPUT_POST, 'usr_name', FILTER_SANITIZE_STRING);
        $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);

        if ($pass === '') {
            // Cập nhật cơ sở dữ liệu
            $update_emp = $conn->prepare("
        UPDATE `employee` 
        SET name_employee = ?, date_of_birth = ?, citizen_card = ?, gender = ?, phone_to = ?, username = ?
        WHERE id_employee = ?
    ");
            $update_emp->execute([$name_employee, $date_of_birth, $citizen_card, $gender, $phone_to, $usr_name, $user_id]);
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            // Cập nhật cơ sở dữ liệu
            $update_emp = $conn->prepare("
            UPDATE `employee` 
            SET name_employee = ?, date_of_birth = ?, citizen_card = ?, gender = ?, phone_to = ?, username = ?, password = ? 
            WHERE id_employee = ?
        ");
            $update_emp->execute([$name_employee, $date_of_birth, $citizen_card, $gender, $phone_to, $usr_name, $hashed_password, $user_id]);
        }


        header('location:edit_profile.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <!-- <link rel="stylesheet" href="css/header_footer.css"> -->

</head>

<body>
    <!-- Header -->
    <?php include 'components/header.php'; ?>
    <!-- Section: Edit Profile -->
    <section class="user-profile">
        <img src="./images/sub_bg.png" alt="sub_bg" class="bg-1">
        <img src="./images/sub_bg.png" alt="sub_bg" class="bg-2">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($avatar_url) ?>" alt="avatar">
            <p class="header-title">User Profile <br> <span>Employee</span></p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="profile-body">
            <div class="panel">
                <h1 class="panel-title">General Information</h1>
                <div class="profile-item">
                    <label><i class="fa-solid fa-user"></i> Name</label>
                    <input name="name_employee" placeholder="Name" maxlength="50" value="<?= htmlspecialchars($fetch_employee['name_employee']) ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-cake-candles"></i> Date of Birth</label>
                    <input name="date_of_birth" placeholder="Date of Birth" type="date" value="<?= htmlspecialchars($fetch_employee['date_of_birth']) ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-id-card"></i> Citizen Card</label>
                    <input name="citizen_card" placeholder="Citizen Card" maxlength="12" value="<?= htmlspecialchars($fetch_employee['citizen_card']) ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-venus-mars"></i> Gender</label>
                    <div class="gender-select">
                        <label>
                            <input type="radio" name="gender" value="Male" <?= $fetch_employee['gender'] === "Male" ? "checked" : "" ?>>
                            Male
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Female" <?= $fetch_employee['gender'] === "Female" ? "checked" : "" ?>>
                            Female
                        </label>
                    </div>
                </div>
            </div>
            <div class="panel">
                <h1 class="panel-title">Account Information</h1>
                <div class="profile-item">
                    <label><i class="fa-regular fa-circle-user"></i> Username</label>
                    <input name="usr_name" placeholder="Username" maxlength="99" value="<?= htmlspecialchars($fetch_employee['username']) ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-key"></i> Password</label>
                    <div class="password-wrapper">
                        <input name="pass" placeholder="Password" maxlength="99" value="">
                        <i id="toggle-password" class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-address-book"></i> Contact</label>
                    <input name="phone_to" placeholder="Phone Number" maxlength="15" value="<?= htmlspecialchars($fetch_employee['phone_to']) ?>" required>
                </div>
                <div class="profile-item row">
                    <button type="submit" name="submit" class="profile-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                    <button type="reset" class="profile-btn clear addgg-clear"><i class="fa-solid fa-eraser"></i> Clear</button>
                </div>
            </div>
        </form>
    </section>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <script src="js/index.js"></script>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.classList.toggle('fa-eye-slash', isPassword);
            this.classList.toggle('fa-eye', !isPassword);
        });
    </script>
    <script src="js/index.js"></script>
</body>

</html>