<?php
session_start();
include("database.php");

// Redirect if not logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];

// Logout functionality
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gym Management Dashboard</title>

<!-- Google Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
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
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        position: relative;
        z-index: 2;
    }

    header h1 {
        font-size: 22px;
        letter-spacing: 1px;
    }

    header .logout-btn {
        background: #fff;
        color: #bc1aa4ff;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s;
    }

    header .logout-btn:hover {
        background: #f1f1f1;
        transform: scale(1.05);
    }

    main {
        flex: 1;
        padding: 50px;
        z-index: 2;
        position: relative;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 25px;
        font-weight: 600;
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    a.card {
        display: block;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        backdrop-filter: blur(10px);
        box-shadow: 0 0 15px rgba(0,0,0,0.5);
        transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
        text-decoration: none;
        color: #fff;
    }

    a.card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 25px rgba(26, 188, 156, 0.6);
        background: rgba(26, 188, 156, 0.2);
    }

    .card i {
        font-size: 40px;
        color: #1abc9c;
        margin-bottom: 10px;
    }

    .card h3 {
        font-size: 20px;
        margin-bottom: 5px;
    }

    .card p {
        color: #ddd;
        font-size: 15px;
    }

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
    <h1>🏋️‍♂️ Gym Management Dashboard</h1>
    <form method="POST" action="">
        <button type="submit" name="logout" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</header>

<main>
    <h2>Welcome, <?php echo ucfirst($username); ?> 👋</h2>

    <div class="cards">
        <a href="members.php" class="card">
            <i class="fa-solid fa-user"></i>
            <h3>Members</h3>
            <?php
            // 🔢 Count total members
            $count_query = mysqli_query($conn, "SELECT COUNT(*) AS total_members FROM Member");
            $count_data = mysqli_fetch_assoc($count_query);
            $total_members = $count_data['total_members'];
            ?>
            <p>
                <?php echo $total_members . " Active Members"; ?>
            </p>
        </a>

        <a href="plan.php" class="card">
            <i class="fa-solid fa-dumbbell"></i>
            <h3>Membership Plans</h3>
            <?php
            // 🔢 Count total members
            $count_query = mysqli_query($conn, "SELECT COUNT(*) AS total_plan FROM Plan");
            $count_data = mysqli_fetch_assoc($count_query);
            $total_plan = $count_data['total_plan'];
            ?>
            <p><?php echo $total_plan . " Active Plan"; ?></p>
        </a>

        <a href="payment.php" class="card">
            <i class="fa-solid fa-wallet"></i>
            <h3>Payments</h3>
            <?php
            // 🔢 Count total payment
            $count_query = mysqli_query($conn, "SELECT SUM(amount_paid) AS total_paid FROM Payment;");
            $count_data = mysqli_fetch_assoc($count_query);
            $total_amount = $count_data['total_paid'];
            ?>
            <p><?php echo "₹ " .  $total_amount . " Collected"; ?> </p>
        </a>

        <a href="due_payment.php" class="card">
            <i class="fa-solid fa-calendar-check"></i>
            <h3>Upcoming Renewals</h3>
            <?php
            $count_query = mysqli_query($conn, 
            "SELECT COUNT(*) AS total_member
            FROM Member
            WHERE amount_to_pay > 0;");
            $count_data = mysqli_fetch_assoc($count_query);
            $total_amount = $count_data['total_member'];
            ?>
            <p><?php echo $total_amount . " Member Not Paid"; ?> </p>
        </a>

        <a href="setting.php" class="card">
            <i class="fa-solid fa-gear"></i>
            <h3>Settings</h3>
            <p>Manage Password</p>
        </a>
    </div>
</main>

<footer>
    © <?php echo date("Y"); ?> <span>Gym Management System</span>. All Rights Reserved.
</footer>

</body>
</html>
