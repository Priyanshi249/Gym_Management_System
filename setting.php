<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("database.php");

// Redirect if not logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];
$message = "";

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Fetch current password
    $query = mysqli_query($conn, "SELECT password FROM User WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);
    $current_password = $data["password"];

    if ($old_password !== $current_password) {
        $message = "<p class='error-msg'>❌ Old password is incorrect.</p>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<p class='warn-msg'>⚠️ New passwords do not match.</p>";
    } else {
        $update = mysqli_query($conn, "UPDATE User SET password='$new_password' WHERE username='$username'");
        if ($update) {
            $message = "<p class='success-msg'>✅ Password changed successfully!</p>";
        } else {
            $message = "<p class='error-msg'>⚠️ Something went wrong. Try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Change Password</title>
<style>
/* Base Styling */
* {
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
}

body {
    background: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
}

body::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 0;
}

header {
    background: rgba(26, 188, 156, 0.9);
    padding: 15px 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.4);
    position: relative;
    z-index: 2;
}

header h1 {
    font-size: 22px;
    letter-spacing: 1px;
    color: #fff;
    text-align: center;
    flex: 1;
}

.btn-back {
    position: absolute;
    left: 40px;
    background: #fff;
    color: #1abc9c;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-back:hover {
    background: #f1f1f1;
    transform: scale(1.05);
}

/* Main Form Layout */
main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2;
    position: relative;
    padding: 40px;
}

.settings-box {
    width: 400px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(0,0,0,0.6);
    padding: 30px 35px;
    text-align: center;
}

.settings-box h2 {
    color: #f1c40f;
    font-size: 24px;
    margin-bottom: 25px;
    text-shadow: 0 0 10px rgba(255,255,255,0.3);
}

.settings-box input {
    width: 100%;
    padding: 12px;
    margin: 10px 0 15px;
    border-radius: 8px;
    border: none;
    font-size: 15px;
    outline: none;
}

.settings-box input::placeholder {
    color: #777;
}

.settings-box button {
    width: 100%;
    padding: 12px;
    background: #1abc9c;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: 0.3s;
}

.settings-box button:hover {
    background: #16a085;
    transform: scale(1.02);
}

/* Message Styles */
.success-msg, .error-msg, .warn-msg {
    margin-top: 15px;
    font-weight: 600;
    text-align: center;
}

.success-msg { color: #2ecc71; }
.error-msg { color: #e74c3c; }
.warn-msg { color: #f1c40f; }

/* Footer */
footer {
    background: rgba(0, 0, 0, 0.6);
    text-align: center;
    padding: 12px;
    font-size: 13px;
    color: #ccc;
    position: relative;
    z-index: 2;
}

footer span {
    color: #1abc9c;
    font-weight: 600;
}
</style>
</head>

<body>
<header>
    <a href="dashboard.php" class="btn-back">← Back</a>
    <h1>Settings</h1>
</header>

<main>
    <div class="settings-box">
        <h2>Change Password</h2>
        <form method="POST">
            <input type="password" name="old_password" placeholder="Enter Old Password" required>
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit" name="change_password">Update Password</button>
        </form>
        <?php echo $message; ?>
    </div>
</main>

<footer>
    © 2025 Fee Management System
</footer>
</body>
</html>
