<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Logic to validate credentials
    if ($email == "admin@example.com" && $password == "password") {
        echo "<script>alert('Login successful!');</script>";
    } else {
        echo "<script>alert('Invalid credentials!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
            <img src="images/img_gadget/1731913363.jpg" alt="Illustration of people" />
        </div>
        <div class="login-form">
            <h2>LOG IN</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
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