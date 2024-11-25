<?php
include 'components/connect.php';

if (isset($_POST['submit'])) {
    $name_employee = $_POST['name_employee'];     //name
    $name_employee = filter_var($name_employee, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['date_of_birth'];     //import price
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    $citizen_card = $_POST['citizen_card'];     //export price
    $citizen_card = filter_var($citizen_card, FILTER_SANITIZE_STRING);

    $gender = $_POST['gender-select'];     //gender
    $gender = filter_var($gender, FILTER_SANITIZE_STRING);

    $phone_to = $_POST['phone_to'];     //export price
    $phone_to = filter_var($phone_to, FILTER_SANITIZE_STRING);

    $role = $_POST['role-select'];     //role-select
    $role = filter_var($role, FILTER_SANITIZE_STRING);

    $usr_name = $_POST['usr_name'];     //usr_name
    $usr_name = filter_var($usr_name, FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'];     //pass-select
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);


    //check if already exists
    $select_emp = $conn->prepare("SELECT * FROM `employee` WHERE phone_to = ?");
    $select_emp->execute([$phone_to]);
    $row = $select_emp->fetch(PDO::FETCH_ASSOC);
    //if existed
    if ($select_emp->rowCount() > 0) {
        $message[] = "Phone has already existed!";
    } else {
        //if new, not exists

        //insert to db
        $insert_emp = $conn->prepare("INSERT INTO `employee` (name_employee, date_of_birth, citizen_card, gender, phone_to, role, state, username, password) VALUES (?,?,?,?,?,?,?,?,?)");
        $insert_emp->execute([$name_employee, $date_of_birth, $citizen_card, $gender, $phone_to, $role, "Available", $usr_name, $pass]);

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
</head>

<body>
    <!-- starts header -->
    <?php include 'components\header_sim.php' ?>
    <!-- ends header -->

    <!-- section create_new_gadget starts -->
    <section class="create-gadget">
        <h1 style="color: yellow">CREATE A NEW EMPLOYEE</h1>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
            <h3>Name:</h3>
            <input name="name_employee" placeholder="Name" maxlength="50" value="" required>
            <h3>Date of Birth:</h3>
            <input name="date_of_birth" placeholder="Date of Birth" type="date" value="" required>
            <h3>Citizen Card:</h3>
            <input name="citizen_card" placeholder="Citizen Card" maxlength="12" min="0" required>
            <div class="div-container">
                <label style="color: white;">Gender:</label>
                <select class="gender-select" name="gender-select">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <h3>Phone Number:</h3>
            <input name="phone_to" placeholder="Phone Number" maxlength="10" required>

            <div class="div-container">
                <label style="color: white;">Role:</label>
                <select class="role-select" name="role-select">
                    <option value="Employee">employee</option>
                    <option value="Manager">manager</option>
                </select>
            </div>

            <h3>Username</h3>
            <input name="usr_name" placeholder="Username" maxlength="99" required>

            <h3>Password</h3>
            <input name="pass" placeholder="Password" maxlength="99" required>


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