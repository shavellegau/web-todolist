<?php
require 'config.php';
if(!empty($_SESSION["id"])){
    header("Location: index.php");
    exit();
}

if (isset($_POST["login"])) {
    $usernameoremail = htmlspecialchars($conn->real_escape_string($_POST["usernameoremail"]));
    $password = $conn->real_escape_string($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $usernameoremail, $usernameoremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($result->num_rows > 0) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Wrong Password');</script>";
        }
    } else {
        echo "<script>alert('User Not Registered');</script>";
    }

    $stmt->close();
}


elseif (isset($_POST["register"])) {
    $name = htmlspecialchars($conn->real_escape_string($_POST["name"]));
    $username = htmlspecialchars($conn->real_escape_string($_POST["username"]));
    $email = htmlspecialchars($conn->real_escape_string($_POST["email"]));
    $password = $conn->real_escape_string($_POST["password"]);
    $confirmpassword = $conn->real_escape_string($_POST["confirmpassword"]);

    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or Email Has Already Been Taken');</script>";
    } else {
        if ($password == $confirmpassword) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO tb_user (name, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                echo "<script>alert('Registration Successful');</script>";
            } else {
                echo "<script>alert('Registration Failed');</script>";
            }
        } else {
            echo "<script>alert('Password Does Not Match');</script>";
        }
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration Page</title>
    <style>
        :root {
            --base-clr: #A0937D;          
            --line-clr: #000000;          
            --hover-clr: #B7E0FF;        
            --text-clr: #ffffff;          
            --accent-clr: #FFCFB3;        
            --secondary-text-clr: #000000;
            --form-bg: #F4F4F4;           
            --form-border: #ccc;          
            --login-bg: #FFCFB3;          
            --button-bg: #A0937D;        
            --button-hover-bg: #FF416C;   
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: var(--form-bg);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
            display: flex;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .login-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .container.active .login-container {
            transform: translateX(100%);
        }

        .register-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.active .register-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        form {
            background-color: var(--login-bg); 
            display: flex;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;
            font-size: 30px;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            font-size: 14px;
        }

        button {
            border-radius: 20px;
            border: none;
            background-color: var(--button-bg); 
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: background-color 0.3s ease, transform 80ms ease-in;
            cursor: pointer;
        }

        button:hover {
            background-color: var(--button-hover-bg);
            opacity: 0.8;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: linear-gradient(to right, #ff4b2b, #ff416c);
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            top: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0 40px;
            height: 100%;
            width: 50%;
            text-align: center;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.active .overlay-right {
            transform: translateX(20%);
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .container.active .overlay-left {
            transform: translateX(0);
        }

        .overlay-panel h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .overlay-panel p {
            font-size: 14px;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        .overlay-panel button {
            background-color: transparent;
            border-color: #fff;
        }

    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container login-container">
            <form action="" method="POST">
                <h2>Login</h2>
                <input type="text" name="usernameoremail" placeholder="Username or Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
        <div class="form-container register-container">
            <form action="" method="POST">
                <h2>Register</h2>
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirmpassword" placeholder="Confirm Password" required>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="login">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="register">Register</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    </script>
</body>
</html>