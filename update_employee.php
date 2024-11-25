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

if (isset($_GET['id_employee'])) {
    $emp_id = intval($_GET['id_employee']);

    $select_emp = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
    $select_emp->execute([$emp_id]);
    if ($select_emp->rowCount() == 0) {
        header('Location: employee.php');
        exit();
    }
} else {
    header('Location: employee.php');
    exit();
}

if (isset($_POST['submit'])) {
    $name_employee = $_POST['name_employee'];     //name
    $name_employee = filter_var($name_employee, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['date_of_birth'];     //bday
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    $citizen_card = $_POST['citizen_card'];     //citizen card
    $citizen_card = filter_var($citizen_card, FILTER_SANITIZE_STRING);

    $gender = $_POST['gender-select'];     //gender
    $gender = filter_var($gender, FILTER_SANITIZE_STRING);

    $phone_to = $_POST['phone_to'];     //phone number
    $phone_to = filter_var($phone_to, FILTER_SANITIZE_STRING);

    $role = $_POST['role-select'];     //role-select
    $role = filter_var($role, FILTER_SANITIZE_STRING);

    $state = $_POST['state-select'];     //state-select
    $state = filter_var($state, FILTER_SANITIZE_STRING);

    $usr_name = $_POST['usr_name'];     //usr_name
    $usr_name = filter_var($usr_name, FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'];     //pass-select
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $update_emp = $conn->prepare("UPDATE `employee` SET name_employee = ?, date_of_birth = ?, citizen_card = ?, gender = ?, phone_to = ?, role = ?, state = ?, username = ?, password = ? WHERE id_employee = ?");
    $update_emp->execute([$name_employee, $date_of_birth, $citizen_card, $gender, $phone_to, $role, $state, $usr_name, $pass, $emp_id]);



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
    <title>Update Employee</title>

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
        <h1 style="color: yellow">UPDATE EMPLOYEE</h1>
        <?php
        $select_employee = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
        $select_employee->execute([$emp_id]);
        if ($fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
                <h3>Name:</h3>
                <input name="name_employee" placeholder="Name" maxlength="50" value="<?= $fetch_employee['name_employee'] ?>" required>
                <h3>Date of Birth:</h3>
                <input name="date_of_birth" placeholder="Date of Birth" type="date" value="<?= $fetch_employee['date_of_birth'] ?>" required>
                <h3>Citizen Card:</h3>
                <input name="citizen_card" placeholder="Citizen Card" maxlength="12" min="0" required value="<?= $fetch_employee['citizen_card'] ?>">
                <div class="div-container">
                    <label style="color: white;">Gender:</label>
                    <select class="gender-select" name="gender-select">
                        <option value="Male" <?= $fetch_employee['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                        <option value="Female" <?= $fetch_employee['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                    </select>
                </div>
                <h3>Phone Number:</h3>
                <input name="phone_to" placeholder="Phone Number" maxlength="10" required value="<?= $fetch_employee['phone_to'] ?>">

                <div class="div-container">
                    <label style="color: white;">Role:</label>
                    <select class="role-select" name="role-select">
                        <option value="Employee" <?= $fetch_employee['role'] == "Employee" ? "selected" : "" ?>>Employee</option>
                        <option value="Manager" <?= $fetch_employee['role'] == "Manager" ? "selected" : "" ?>>Manager</option>
                    </select>
                </div>

                <div class="div-container">
                    <label style="color: white;">State:</label>
                    <select class="state-select" name="state-select">
                        <option value="Available" <?= $fetch_employee['state'] == "Available" ? "selected" : "" ?>>available</option>
                        <option value="Not Working" <?= $fetch_employee['state'] == "Not Working" ? "selected" : "" ?>>Not Working</option>
                    </select>
                </div>

                <h3>Username</h3>
                <input name="usr_name" placeholder="Username" maxlength="99" required value="<?= $fetch_employee['username'] ?>">

                <h3>Password</h3>
                <input name="pass" placeholder="Password" maxlength="99" required value="<?= $fetch_employee['password'] ?>">

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

<!-- onkeydown="return event.keyCode !== 69" -->

<!-- neu k filter + sani -> <script>alert('Hacked!');</script> -->