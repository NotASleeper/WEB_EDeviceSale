<?php
include 'components/connect.php';

session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $username = filter_var($username, FILTER_SANITIZE_STRING);

    $password = $_POST['password'];
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `employee` WHERE username = ? AND password = ? AND state = 'Available'");
    $select_user->execute([$username, $password]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        $_SESSION['user_id'] = $row['id_employee'];
        $_SESSION['role'] = 'employee';
        header('location:home.php');
    } else {
        $select_user_cus = $conn->prepare("SELECT * FROM `customer` WHERE username = ? AND password = ?");
        $select_user_cus->execute([$username, $password]);
        $row = $select_user_cus->fetch(PDO::FETCH_ASSOC);

        if ($select_user_cus->rowCount() > 0) {
            $_SESSION['user_id'] = $row['id_customer'];
            $_SESSION['role'] = 'customer';
            header('location:home_cus.php');
        } else {
            echo '<script>alert("Username or Password is not Valid");</script>';
        }
    }
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
            <h2>LOG IN</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="username" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-login">Log In</button>
            </form>
        </div>
    </div>
</body>

</html>