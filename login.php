<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("database.php");

$error_message = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $query = "SELECT * FROM User WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($password === $row["password"]) {
            $_SESSION["username"] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "❌ Invalid password!";
        }
    } else {
        $error_message = "⚠️ Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gym Management System - Login</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        margin: 0;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
    }

    .overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1;
    }

    .login-container {
        position: relative;
        z-index: 2;
        width: 380px;
        padding: 40px 35px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        backdrop-filter: blur(10px);
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.5);
        color: #fff;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 15px;
        outline: none;
        transition: background 0.3s;
    }

    input::placeholder {
        color: #ccc;
    }

    input:focus {
        background: rgba(255, 255, 255, 0.25);
    }

    button {
        width: 100%;
        padding: 12px;
        background: #1abc9c;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
        font-weight: 600;
    }

    button:hover {
        background: #16a085;
        transform: scale(1.02);
    }

    .error {
        text-align: center;
        margin-top: 10px;
        color: #ff7675;
        background: rgba(255, 255, 255, 0.1);
        padding: 8px;
        border-radius: 8px;
    }

    .footer-text {
        text-align: center;
        margin-top: 15px;
        font-size: 13px;
        color: #ccc;
    }

    .footer-text span {
        color: #1abc9c;
        font-weight: 600;
    }
</style>
</head>
<body>

<div class="overlay"></div>

<div class="login-container">
    <h2>🏋️‍♂️ Gym Management Login</h2>
    <form method="POST" action="" autocomplete="off">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>

    <?php if ($error_message != "") { echo "<p class='error'>$error_message</p>"; } ?>

    <div class="footer-text">
        © <?php echo date("Y"); ?> <span>Gym Management System</span>
    </div>
</div>

</body>
</html>
