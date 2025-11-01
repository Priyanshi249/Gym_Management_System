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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Payments</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        text-align: center;
        color: #f1c40f;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0,0,0,0.4);
    }

    th, td {
        padding: 12px 15px;
        text-align: center;
    }

    th {
        background-color: #1abc9c;
        color: #000;
        font-weight: 600;
    }

    tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.05);
    }

    tr:hover {
        background-color: rgba(26, 188, 156, 0.15);
    }

    .no-data {
        text-align: center;
        padding: 20px;
        color: #ccc;
        font-size: 18px;
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
    <a href="dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <h1>Fee Management System</h1>
</header>

<main>
    <h2>Members with Pending Payments</h2>

    <table>
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Name</th>
                <th>Plan Name</th>
                <th>Total Plan Amount</th>
                <th>Pending Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // 🔹 Fetch members with amount_to_pay > 0 and get plan details
        $query = "
            SELECT 
                m.member_id,
                m.name,
                p.plan_name,
                p.price AS total_amount,
                m.amount_to_pay
            FROM Member m
            JOIN Plan p ON m.plan_id = p.plan_id
            WHERE m.amount_to_pay > 0
        ";

        $result = mysqli_query($conn, $query);
        $rows_shown = 0;

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows_shown++;
                echo "<tr>
                        <td>{$row['member_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['plan_name']}</td>
                        <td>₹ {$row['total_amount']}</td>
                        <td style='color:#e74c3c;'>₹ {$row['amount_to_pay']}</td>
                      </tr>";
            }
        }

        if ($rows_shown === 0) {
            echo "<tr><td colspan='6' class='no-data'>🎉 All members have cleared their payments!</td></tr>";
        }
        ?>
        </tbody>
    </table>
</main>

<footer>
    © 2025 <span>Fee Management System</span>. All Rights Reserved.
</footer>

</body>
</html>
