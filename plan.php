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

// Logout
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$editMode = false;
$plan_id = "";
$plan_name = "";
$duration = "";
$price = "";

/* ================================
   ADD / UPDATE PLAN
================================ */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save_plan"])) {
    $plan_name = trim($_POST["plan_name"]);
    $duration = trim($_POST["duration"]);
    $price = trim($_POST["price"]);

    // if editing - use session plan_id
    if (!empty($_SESSION["plan_id"])) {
        $plan_id = $_SESSION["plan_id"];
        $query = "UPDATE Plan SET plan_name=?, duration_month=?, price=? WHERE plan_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdi", $plan_name, $duration, $price, $plan_id);
        $stmt->execute();

        unset($_SESSION["plan_id"]); // clear session after update
    } else {
        // Add new plan
        $query = "INSERT INTO Plan (plan_name, duration_month, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssd", $plan_name, $duration, $price);
        $stmt->execute();
    }

    header("Location: plan.php");
    exit;
}

/* ================================
   EDIT PLAN (Load data into form)
================================ */
if (isset($_GET["edit"])) {
    $editMode = true;
    $plan_id = $_GET["edit"];
    $_SESSION["plan_id"] = $plan_id; // save in session

    $query = "SELECT * FROM Plan WHERE plan_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $plan_name = $row["plan_name"];
        $duration = $row["duration_month"];
        $price = $row["price"];
    }
}

/* ================================
   DELETE PLAN
================================ */
if (isset($_GET["delete"])) {
    $plan_id = $_GET["delete"];
    $deleteQuery = "DELETE FROM Plan WHERE plan_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    header("Location: plan.php");
    exit;
}

/* ================================
   FETCH ALL PLANS
================================ */
$query = "SELECT * FROM Plan ORDER BY plan_id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Membership Plans - Gym Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
    body {
        background: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
        color: #fff; min-height: 100vh; display: flex; flex-direction: column; position: relative;
    }
    body::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7); z-index: 0; }
    header {
        background: rgba(26, 188, 156, 0.9); padding: 15px 40px; display: flex;
        justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        position: relative; z-index: 2;
    }
    header h1 { font-size: 22px; letter-spacing: 1px; }
    header .logout-btn {
        background: #fff; color: #1abc9c; border: none; padding: 8px 15px;
        border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s;
    }
    header .logout-btn:hover { background: #f1f1f1; transform: scale(1.05); }
    main { flex: 1; padding: 50px; z-index: 2; position: relative; }
    h2 { font-size: 24px; margin-bottom: 25px; font-weight: 600; text-shadow: 0 0 8px rgba(255, 255, 255, 0.3); }
    .back-btn {
        display: inline-block; background: #1abc9c; color: #fff; padding: 8px 15px;
        border-radius: 6px; text-decoration: none; font-weight: 600; transition: 0.3s;
        margin-bottom: 25px;
    }
    .back-btn:hover { background: #16a085; transform: scale(1.05); }
    table {
        width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.1);
        border-radius: 12px; overflow: hidden; backdrop-filter: blur(10px);
    }
    th, td { padding: 15px; text-align: left; }
    th {
        background: rgba(26, 188, 156, 0.8); color: #fff; text-transform: uppercase; font-size: 14px;
    }
    tr:nth-child(even) { background: rgba(255, 255, 255, 0.05); }
    td { color: #eee; font-size: 15px; }
    .action-btn {
        padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; transition: 0.3s; text-decoration: none;
    }
    .edit-btn { background: #3498db; color: #fff; }
    .edit-btn:hover { background: #2980b9; transform: scale(1.05); }
    .delete-btn { margin-left: 15px; background: #e74c3c; color: #fff; }
    .delete-btn:hover { background: #c0392b; transform: scale(1.05); }
    .form-container {
        background: rgba(255, 255, 255, 0.1); padding: 25px; border-radius: 12px;
        backdrop-filter: blur(8px); margin-bottom: 40px;
    }
    input[type=text], input[type=number] {
        width: 100%; padding: 10px; border-radius: 6px; border: none; margin: 8px 0;
        background: rgba(255, 255, 255, 0.2); color: #fff;
    }
    input::placeholder { color: #ddd; }
    .save-btn {
        background: #27ae60; color: #fff; border: none; padding: 10px 18px;
        border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s;
    }
    .save-btn:hover { background: #229954; transform: scale(1.05); }
    footer {
        background: rgba(0, 0, 0, 0.6); text-align: center; padding: 12px;
        font-size: 13px; color: #ccc; position: relative; z-index: 2;
    }
    footer span { color: #1abc9c; font-weight: 600; }
</style>
</head>
<body>

<header>
    <h1>🏋️‍♂️ Gym Management - Membership Plans</h1>
    <form method="POST" action="">
        <button type="submit" name="logout" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</header>

<main>
    <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</a>

    <div class="form-container">
        <h2><?php echo $editMode ? "Edit Plan" : "Add New Plan"; ?></h2>
        <form method="POST" action="">
            <label>Plan Name</label>
            <input type="text" name="plan_name" placeholder="Enter plan name" value="<?php echo htmlspecialchars($plan_name); ?>" required>

            <label>Duration (Months)</label>
            <input type="text" name="duration" placeholder="Enter duration" value="<?php echo htmlspecialchars($duration); ?>" required>

            <label>Price (₹)</label>
            <input type="number" name="price" placeholder="Enter price" step="0.01" value="<?php echo htmlspecialchars($price); ?>" required>

            <button type="submit" name="save_plan" class="save-btn">
                <i class="fa-solid fa-floppy-disk"></i> <?php echo $editMode ? "Update Plan" : "Add Plan"; ?>
            </button>
        </form>
    </div>

    <h2>Membership Plans</h2>
    <table>
        <thead>
            <tr>
                <th>Plan ID</th>
                <th>Plan Name</th>
                <th>Duration (Months)</th>
                <th>Price (₹)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['plan_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['duration_month']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <a href="plan.php?edit=<?php echo $row['plan_id']; ?>" class="action-btn edit-btn">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <a href="plan.php?delete=<?php echo $row['plan_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this plan?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; color:#ccc;">No plans available</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    © <?php echo date("Y"); ?> <span>Gym Management System</span>. All Rights Reserved.
</footer>

</body>
</html>
