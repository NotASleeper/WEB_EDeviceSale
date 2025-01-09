<?php
include 'components/connect.php';

session_start();

// Kiểm tra session đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Kiểm tra quyền truy cập
if ($role !== 'employee') {
    echo "Bạn không có quyền xem trang này!";
    exit();
}

$select_password = $conn->prepare("SELECT password FROM `employee` WHERE id_employee = ?");
$select_password->execute([$user_id]);
$user_password = $select_password->fetchColumn();

// Lấy thông tin nhân viên từ cơ sở dữ liệu
$select_employee = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
$select_employee->execute([$user_id]);
$fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);

$avatar_url = $fetch_employee['gender'] === 'Male'
    ? 'https://avatar.iran.liara.run/public/boy'
    : 'https://avatar.iran.liara.run/public/girl';

// Xử lý khi người dùng gửi form
if (isset($_POST['submit'])) {
    $name_employee = filter_var($_POST['name_employee'], FILTER_SANITIZE_STRING);
    $date_of_birth = filter_var($_POST['date_of_birth'], FILTER_SANITIZE_STRING);
    $citizen_card = filter_var($_POST['citizen_card'], FILTER_SANITIZE_STRING);
    $phone_to = filter_var($_POST['phone_to'], FILTER_SANITIZE_STRING);
    $usr_name = filter_var($_POST['usr_name'], FILTER_SANITIZE_STRING);
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);

    // Kiểm tra radio button gender và state
    $gender = isset($_POST['gender']) ? filter_var($_POST['gender'], FILTER_SANITIZE_STRING) : null;
    $state = isset($_POST['status-select']) ? filter_var($_POST['status-select'], FILTER_SANITIZE_STRING) : null;

    // Thực hiện cập nhật thông tin
    $update_emp = $conn->prepare("UPDATE `employee` SET 
        name_employee = ?, 
        date_of_birth = ?, 
        citizen_card = ?, 
        gender = ?, 
        phone_to = ?, 
        state = ?, 
        username = ?, 
        password = ? 
        WHERE id_employee = ?");
    $update_emp->execute([
        $name_employee,
        $date_of_birth,
        $citizen_card,
        $gender,
        $phone_to,
        $state,
        $usr_name,
        $pass,
        $user_id
    ]);
    header("location: employee.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>

    <!-- Favicon -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>
    <?php include 'components/header_sim.php'; ?>

    <!-- User Profile Section -->
    <section class="user-profile">
        <img src="./images/sub_bg.png" alt="Background" class="bg-1">
        <img src="./images/sub_bg.png" alt="Background" class="bg-2">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($avatar_url) ?>" alt="avatar">
            <p class="header-title">User Profile <br> <span>Employee</span></p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="updateForm" class="profile-body">
            <div class="panel">
                <h1 class="panel-title">General Information</h1>
                <div class="profile-item">
                    <label><i class="fa-solid fa-user"></i> Name</label>
                    <input name="name_employee" placeholder="Name" maxlength="50" value="<?= htmlspecialchars($fetch_employee['name_employee']) ?>" required>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-cake-candles"></i> Date of Birth</label>
                    <input name="date_of_birth" type="date" value="<?= htmlspecialchars($fetch_employee['date_of_birth']) ?>" required>
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
                <div class="profile-item">
                    <label><i class="fa-solid fa-briefcase"></i> Status</label>
                    <div class="status-select">
                        <label>
                            <input type="radio" name="status-select" value="Available" <?= $fetch_employee['state'] === "Available" ? "checked" : "" ?>>
                            Available
                        </label>
                        <label>
                            <input type="radio" name="status-select" value="Not Working" <?= $fetch_employee['state'] === "Not Working" ? "checked" : "" ?>>
                            Unavailable
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
                        <input type="password" name="pass" placeholder="Password" maxlength="99" value="<?= htmlspecialchars($fetch_employee['password']) ?>" required>
                        <i id="toggle-password" class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="profile-item">
                    <label><i class="fa-solid fa-address-book"></i> Contact</label>
                    <input name="phone_to" placeholder="Phone Number" maxlength="15" value="<?= htmlspecialchars($fetch_employee['phone_to']) ?>" required>
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

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

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

            // Xử lý khi nhấn Confirm trong hộp thoại
            confirmPasswordBtn.addEventListener('click', function() {
                const enteredPassword = confirmPasswordInput.value;

                // Mật khẩu lấy từ PHP
                const storedPassword = '<?php echo $user_password; ?>';

                if (enteredPassword === storedPassword) {
                    // Nếu mật khẩu đúng, đóng hộp thoại và cho phép gửi form
                    isPasswordConfirmed = true;
                    confirmPasswordDialog.style.display = 'none';

                    // Thực hiện click lại nút Edit để gửi form
                    editBtn.click();
                } else {
                    alert('Incorrect password! Please try again.');
                }
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