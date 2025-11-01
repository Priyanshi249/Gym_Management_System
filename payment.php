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
$edit_mode = false;
$edit_payment = null;

// Handle Add Payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_payment"])) {
    $member_id = $_POST["member_id"];
    $plan_id = $_POST["plan_id"];
    $amount = floatval($_POST["amount"]);
    $payment_date = date('Y-m-d');
    $next_due_date = date('Y-m-d', strtotime('+30 days')); // default 30 days later

    // 1️⃣ Fetch current amount_to_pay
    $check = mysqli_query($conn, "SELECT amount_to_pay FROM Member WHERE member_id = '$member_id'");
    $member = mysqli_fetch_assoc($check);

    if (!$member) {
        $message = "<p class='error'>❌ Member not found!</p>";
    } else {
        $amount_due = floatval($member['amount_to_pay']);

        // 2️⃣ Validate payment
        if ($amount <= 0) {
            $message = "<p class='error'>❌ Invalid payment amount.</p>";
        } elseif ($amount > $amount_due) {
            $message = "<p class='error'>⚠️ Payment exceeds pending amount (₹$amount_due).</p>";
        } else {
            // 3️⃣ Insert into Payment
            $query = "INSERT INTO Payment (member_id, plan_id, amount_paid, payment_date, next_due_date) 
                      VALUES ('$member_id', '$plan_id', '$amount', '$payment_date', '$next_due_date')";
            
            if (mysqli_query($conn, $query)) {
                // 4️⃣ Update Member table (reduce amount_to_pay)
                $new_due = $amount_due - $amount;
                $update = "UPDATE Member SET amount_to_pay = '$new_due' WHERE member_id = '$member_id'";
                mysqli_query($conn, $update);

                $message = "<p class='success'>✅ Payment added successfully! Remaining due: ₹$new_due</p>";
            } else {
                $message = "<p class='error'>❌ Error: " . mysqli_error($conn) . "</p>";
            }
        }
    }
}

// Handle Edit Load
if (isset($_GET["edit"])) {
    $edit_id = $_GET["edit"];
    $edit_mode = true;
    $result = mysqli_query($conn, "SELECT * FROM Payment WHERE payment_id = '$edit_id'");
    $edit_payment = mysqli_fetch_assoc($result);
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_payment"])) {
    $payment_id = $_POST["payment_id"];
    $member_id = $_POST["member_id"];
    $plan_id = $_POST["plan_id"];
    $amount = $_POST["amount"];
    $payment_date = $_POST["payment_date"];
    $next_due_date = $_POST["next_due_date"];

    $update_query = "UPDATE Payment 
                     SET member_id='$member_id', plan_id='$plan_id', amount_paid='$amount', 
                         payment_date='$payment_date', next_due_date='$next_due_date'
                     WHERE payment_id='$payment_id'";

    if (mysqli_query($conn, $update_query)) {
        $message = "<p class='success'>✅ Payment updated successfully!</p>";
    } else {
        $message = "<p class='error'>❌ Error updating payment: " . mysqli_error($conn) . "</p>";
    }

    $edit_mode = false;
}

// Handle Delete
if (isset($_GET["delete"])) {
    $payment_id = $_GET["delete"];
    $delete_query = "DELETE FROM Payment WHERE payment_id = '$payment_id'";
    mysqli_query($conn, $delete_query);
    header("Location: payment.php");
    exit;
}

// Fetch Members (with plan info and due amount)
$members = mysqli_query($conn, "
    SELECT m.member_id, m.name, m.amount_to_pay, p.plan_id, p.plan_name, p.price
    FROM Member m
    JOIN Plan p ON m.plan_id = p.plan_id
");

// Fetch all payments
$payments = mysqli_query($conn, "
    SELECT p.payment_id, m.name AS member_name, pl.plan_name, p.amount_paid, p.payment_date, p.next_due_date
    FROM Payment p
    JOIN Member m ON p.member_id = m.member_id
    JOIN Plan pl ON p.plan_id = pl.plan_id
    ORDER BY p.payment_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Management</title>
<style>
<?php // same styles as before ?>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
body {
    background: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
    color: #fff; min-height: 100vh; position: relative;
}
body::before {
    content: ""; position: absolute; top: 0; left: 0;
    width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 0;
}
.container { position: relative; z-index: 2; padding: 50px; }
h1 { text-align: center; font-size: 30px; margin-bottom: 25px; color: #1abc9c; }
.btn-back { background: #3498db; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; transition: 0.3s; }
.btn-back:hover { background: #2980b9; transform: scale(1.05); }
.form-container {
    background: rgba(255,255,255,0.1); padding: 25px; border-radius: 12px; backdrop-filter: blur(10px);
    box-shadow: 0 0 15px rgba(0,0,0,0.5); max-width: 600px; margin: 0 auto 40px;
}
form input, form select {
    width: 100%; padding: 12px; margin: 10px 0; border-radius: 6px; border: none; font-size: 15px;
}
form button {
    background: #1abc9c; color: #fff; border: none; padding: 12px; width: 100%;
    border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s;
}
form button:hover { background: #16a085; transform: scale(1.03); }
.success { color: #2ecc71; font-weight: 600; margin-bottom: 15px; text-align:center; }
.error { color: #e74c3c; font-weight: 600; margin-bottom: 15px; text-align:center; }
table {
    width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.1);
    border-radius: 12px; overflow: hidden; box-shadow: 0 0 15px rgba(0,0,0,0.5);
}
th, td { padding: 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
th { background: rgba(26,188,156,0.7); color: #fff; }
tr:hover { background: rgba(255,255,255,0.08); }
.btn { padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; color: #fff; transition: 0.3s; }
.btn-edit { background: #f39c12; }
.btn-edit:hover { background: #e67e22; }
.btn-delete { background: #e74c3c; }
.btn-delete:hover { background: #c0392b; }
#planInfo, #amountDisplay, #dueDisplay {
    text-align: center; font-weight: 600; margin: 10px 0;
    color: #1abc9c; font-size: 18px;
}
</style>
<script>
function showPlanInfo() {
    var member = document.getElementById("member_id");
    var selectedOption = member.options[member.selectedIndex];
    var planName = selectedOption.getAttribute("data-plan");
    var planId = selectedOption.getAttribute("data-planid");
    var price = selectedOption.getAttribute("data-price");
    var due = selectedOption.getAttribute("data-due");

    document.getElementById("planInfo").innerHTML = "📋 Plan: " + planName;
    document.getElementById("amountDisplay").innerHTML = "💰 Plan Price: ₹" + price;
    document.getElementById("dueDisplay").innerHTML = "🧾 Pending Due: ₹" + due;
    document.getElementById("plan_id").value = planId;
}
</script>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    <h1>Payment Management</h1>
    <?php echo $message; ?>

    <div class="form-container">
        <form method="POST">
            <?php if ($edit_mode) { ?>
                <input type="hidden" name="payment_id" value="<?php echo $edit_payment['payment_id']; ?>">
            <?php } ?>

            <label>Member</label>
            <select name="member_id" id="member_id" onchange="showPlanInfo()" required>
                <option value="">Select Member</option>
                <?php 
                mysqli_data_seek($members, 0);
                while ($m = mysqli_fetch_assoc($members)) { 
                    $selected = ($edit_mode && $edit_payment['member_id'] == $m['member_id']) ? "selected" : "";
                ?>
                    <option value="<?php echo $m['member_id']; ?>" <?php echo $selected; ?>
                            data-plan="<?php echo $m['plan_name']; ?>"
                            data-planid="<?php echo $m['plan_id']; ?>"
                            data-price="<?php echo $m['price']; ?>"
                            data-due="<?php echo $m['amount_to_pay']; ?>">
                        <?php echo $m['name']; ?>
                    </option>
                <?php } ?>
            </select>

            <input type="hidden" id="plan_id" name="plan_id" value="<?php echo $edit_mode ? $edit_payment['plan_id'] : ''; ?>">

            <label>Amount Paying (₹)</label>
            <input type="number" id="amount" name="amount" min="1" step="0.01" required value="<?php echo $edit_mode ? $edit_payment['amount_paid'] : ''; ?>">

            <?php if ($edit_mode) { ?>
                <label>Payment Date</label>
                <input type="date" name="payment_date" value="<?php echo $edit_payment['payment_date']; ?>" required>

                <label>Next Due Date</label>
                <input type="date" name="next_due_date" value="<?php echo $edit_payment['next_due_date']; ?>" required>
            <?php } ?>

            <div id="planInfo"></div>
            <div id="amountDisplay"></div>
            <div id="dueDisplay"></div>

            <button type="submit" name="<?php echo $edit_mode ? 'update_payment' : 'add_payment'; ?>">
                <?php echo $edit_mode ? 'Update Payment' : 'Add Payment'; ?>
            </button>
        </form>
    </div>

    <h1>Payment Records</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Member</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Next Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($payments)) { ?>
                <tr>
                    <td><?php echo $row['payment_id']; ?></td>
                    <td><?php echo $row['member_name']; ?></td>
                    <td><?php echo $row['plan_name']; ?></td>
                    <td>₹<?php echo $row['amount_paid']; ?></td>
                    <td><?php echo $row['payment_date']; ?></td>
                    <td><?php echo $row['next_due_date']; ?></td>
                    <td>
                        <a href="payment.php?edit=<?php echo $row['payment_id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="payment.php?delete=<?php echo $row['payment_id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this payment?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
