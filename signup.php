<?php
include 'components/connect.php';

session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //////logic code for login of Customer
    $name_customer = $_POST['name'];     //name
    $name_customer = filter_var($name_customer, FILTER_SANITIZE_STRING);

    $date_of_birth = $_POST['bday'];     //import price
    $date_of_birth = filter_var($date_of_birth, FILTER_SANITIZE_STRING);

    $phone_no = $_POST['phone-no'];     //phone no
    $phone_no = filter_var($phone_no, FILTER_SANITIZE_STRING);

    $username_customer = $_POST['username'];     //username
    $username_customer = filter_var($username_customer, FILTER_SANITIZE_STRING);

    $pass_customer = $_POST['password'];     //password
    $pass_customer = filter_var($pass_customer, FILTER_SANITIZE_STRING);

    $cfpass_customer = $_POST['cf-password'];     //password
    $cfpass_customer = filter_var($cfpass_customer, FILTER_SANITIZE_STRING);

    if ($pass_customer === $cfpass_customer) {
        //check if already exists
        $select_cus = $conn->prepare("SELECT * FROM `customer` WHERE phone_no = ? OR username = ?");
        $select_cus->execute([$phone_no, $username_customer]);
        $row = $select_cus->fetch(PDO::FETCH_ASSOC);
        //if existed
        if ($select_cus->rowCount() > 0) {
            $message[] = "Phone or username has already existed!";
        } else {
            //check emp
            $select_emp = $conn->prepare("SELECT * FROM `employee` WHERE phone_to = ? OR username = ?");
            $select_emp->execute([$phone_no, $username_customer]);
            $row = $select_emp->fetch(PDO::FETCH_ASSOC);
            if ($select_emp->rowCount() > 0) {
                $message[] = "Phone or username has already existed!";
            } else {
                //if new, not exists

                //hash password
                $hashed_password = password_hash($pass_customer, PASSWORD_DEFAULT);

                //insert to db
                $insert_cus = $conn->prepare("INSERT INTO `customer` (name_customer, date_of_birth, phone_no, total_spending, username, password) VALUES (?,?,?,?,?,?)");
                $insert_cus->execute([$name_customer, $date_of_birth, $phone_no, 0, $username_customer, $hashed_password]);

                //check
                $confirm_cus = $conn->prepare("SELECT * FROM `customer` WHERE phone_no = ?");
                $confirm_cus->execute([$phone_no]);
                if ($confirm_cus->rowCount() > 0) {
                    header('location:login.php');
                } else {
                    echo '<script>alert("Failed to insert");</script>';
                }
            }
        }
    } else {
        echo '<script>alert("Password and Confirm password not matched");</script>';
    }


    ///ends
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            margin: 0 2rem;

            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login-illustration {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fef8e7;
        }

        .login-illustration img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-form {
            flex: 1;
            padding: 40px;
        }

        h2 {
            text-align: center;
            width: 100%;
            color: #7ed957;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .btn-login {
            width: 100%;
            background-color: #7ed957;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #007b4a;
        }

        div.signup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        div.signup-container span,
        div.signup-container a {
            margin: 0 5px;
            font-size: 14px;
            color: #333;
            text-decoration: none;
        }

        div .signup-container a {
            color: #007b4a;
        }

        div.signup-container a:hover {
            text-decoration: underline;
            color: #7ed957;
        }


        /* Responsive Design */
        @media (max-width: 700px) {
            .login-container {
                flex-direction: column;
                margin: 0 2rem;
            }

            .login-illustration {
                display: none;
            }

            .login-form {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-illustration">
            <img src="images/shopping_online.jpg" alt="Illustration of the store" />
        </div>
        <div class="login-form">
            <h2>SIGN UP</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="phone-no">Phone Number</label>
                    <input type="number" id="phone-no" name="phone-no" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label for="bday">Date of Birth</label>
                    <input type="date" id="bday" name="bday" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="username" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div style="display: flex; flex-direction:row">
                    <div class="form-group" style="margin-right: 5px;">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="form-group" style="margin-left: 5px;">
                        <label for="cf-password">Confirm Password</label>
                        <input type="password" id="cf-password" name="cf-password" placeholder="Confirm your password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">Sign Up</button>
            </form>
            <div class="signup-container">
                <span>Already have an account?</span>
                <a href="login.php">Login!</a>
            </div>
        </div>
    </div>
</body>

</html>