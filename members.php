<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("database.php");

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// 🧩 Add Member
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_member'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $plan_id = mysqli_real_escape_string($conn, $_POST['plan_id']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $join_date = date("Y-m-d");

    // 🧠 Fetch amount from Plan table
    $plan_amount_query = mysqli_query($conn, "SELECT price FROM Plan WHERE plan_id = '$plan_id'");
    $plan_data = mysqli_fetch_assoc($plan_amount_query);
    $amount_to_pay = $plan_data ? $plan_data['price'] : 0;

    // 📝 Insert member with amount_to_pay
    $query = "INSERT INTO Member (name, email, phone, gender, age, join_date, plan_id, address, amount_to_pay, created_at)
              VALUES ('$name', '$email', '$phone', '$gender', '$age', '$join_date', '$plan_id', '$address', '$amount_to_pay', NOW())";

    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = "<p class='success'>✅ Member added successfully!</p>";
        header("Location: members.php");
        exit;
    } else {
        $message = "<p class='error'>❌ Error adding member: " . mysqli_error($conn) . "</p>";
    }
}

// 🗑️ Delete Member
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM Member WHERE member_id=$id");
    $_SESSION['msg'] = "<p class='error'>🗑️ Member deleted successfully!</p>";
    header("Location: members.php");
    exit;
}

// ✏️ Update Member
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_member'])) {
    $id = intval($_POST['member_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $plan_id = mysqli_real_escape_string($conn, $_POST['plan_id']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // 🧠 Fetch new plan amount
    $plan_amount_query = mysqli_query($conn, "SELECT price FROM Plan WHERE plan_id = '$plan_id'");
    $plan_data = mysqli_fetch_assoc($plan_amount_query);
    $amount_to_pay = $plan_data ? $plan_data['price'] : 0;

    // 📝 Update member info with new amount
    $update = "UPDATE Member 
               SET name='$name', email='$email', phone='$phone', gender='$gender', 
                   age='$age', plan_id='$plan_id', address='$address', 
                   amount_to_pay='$amount_to_pay'
               WHERE member_id=$id";

    if (mysqli_query($conn, $update)) {
        $_SESSION['msg'] = "<p class='success'>✅ Member updated successfully!</p>";
        header("Location: members.php");
        exit;
    } else {
        $message = "<p class='error'>❌ Error updating member: " . mysqli_error($conn) . "</p>";
    }
}

// 📋 Fetch Members with Plan Names
$result = mysqli_query($conn, "
    SELECT m.*, p.plan_name 
    FROM Member m 
    LEFT JOIN Plan p ON m.plan_id = p.plan_id 
    ORDER BY m.member_id DESC
");

// 📋 Fetch Plans for Dropdown (with amount and ascending order)
$plan_query = mysqli_query($conn, "SELECT plan_id, plan_name, price FROM Plan ORDER BY plan_id ASC");

// 🔍 Check Edit Mode
$edit_mode = false;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit']);
    $edit_query = mysqli_query($conn, "SELECT * FROM Member WHERE member_id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_query);
}

// Show any session message
if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Members - Gym Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
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
    form input, form select, textarea {
        width: 100%; padding: 12px; margin: 10px 0; border-radius: 6px; border: none; font-size: 15px;
    }
    form button {
        background: #1abc9c; color: #fff; border: none; padding: 12px; width: 100%;
        border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s;
    }
    form button:hover { background: #16a085; transform: scale(1.03); }
    .success { color: #2ecc71; font-weight: 600; margin-bottom: 15px; }
    .error { color: #e74c3c; font-weight: 600; margin-bottom: 15px; }
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
    #amountDisplay {
        text-align: center; font-weight: 600; margin: 10px 0;
        color: #1abc9c; font-size: 18px;
    }
</style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <h1>🏋️‍♂️ Manage Members</h1>
    <?php echo $message; ?>

    <!-- 🧾 Add / Edit Member Form -->
    <div class="form-container">
        <form method="POST" action="" autocomplete="off">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="member_id" value="<?php echo $edit_data['member_id']; ?>">
            <?php endif; ?>

            <input type="text" name="name" placeholder="Full Name" 
                value="<?php echo $edit_mode ? htmlspecialchars($edit_data['name']) : ''; ?>" required>
            <input type="number" name="age" placeholder="Age" min="10" max="100"
                value="<?php echo $edit_mode ? htmlspecialchars($edit_data['age']) : ''; ?>" required>
            <input type="email" name="email" placeholder="Email Address" 
                value="<?php echo $edit_mode ? htmlspecialchars($edit_data['email']) : ''; ?>" required>
            <input type="text" name="phone" placeholder="Phone Number"
                value="<?php echo $edit_mode ? htmlspecialchars($edit_data['phone']) : ''; ?>" required>

            <select name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male" <?php if($edit_mode && $edit_data['gender']=='Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if($edit_mode && $edit_data['gender']=='Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if($edit_mode && $edit_data['gender']=='Other') echo 'selected'; ?>>Other</option>
            </select>

            <select name="plan_id" id="planSelect" required>
                <option value="">-- Select Plan --</option>
                <?php
                mysqli_data_seek($plan_query, 0);
                while ($plan = mysqli_fetch_assoc($plan_query)) {
                    $selected = ($edit_mode && $plan['plan_id'] == $edit_data['plan_id']) ? 'selected' : '';
                    echo "<option value='{$plan['plan_id']}' data-amount='{$plan['price']}' $selected>
                        {$plan['plan_name']} (₹{$plan['price']})
                    </option>";
                }
                ?>
            </select>

            <div id="amountDisplay"></div>

            <textarea name="address" placeholder="Address" required><?php echo $edit_mode ? htmlspecialchars($edit_data['address']) : ''; ?></textarea>

            <?php if ($edit_mode): ?>
                <button type="submit" name="update_member">Update Member</button>
                <a href="members.php" style="display:block;text-align:center;margin-top:10px;color:#fff;">Cancel Edit</a>
            <?php else: ?>
                <button type="submit" name="add_member">Add Member</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- 🧍 Members Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Plan Name</th>
            <th>Amount to Pay</th>
            <th>Join Date</th>
            <th>Action</th>
        </tr>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['member_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['age']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['gender']}</td>
                    <td>" . ($row['plan_name'] ? $row['plan_name'] : 'N/A') . "</td>
                    <td>₹{$row['amount_to_pay']}</td>
                    <td>{$row['join_date']}</td>
                    <td>
                        <a href='members.php?edit={$row['member_id']}' class='btn btn-edit'><i class='fa-solid fa-pen'></i></a>
                        <a href='members.php?delete={$row['member_id']}' class='btn btn-delete' onclick=\"return confirm('Are you sure you want to delete this member?');\"><i class='fa-solid fa-trash'></i></a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='10' style='text-align:center;'>No members found.</td></tr>";
        }
        ?>
    </table>
</div>

<script>
// 💰 Show selected plan's price dynamically
document.getElementById('planSelect').addEventListener('change', function() {
    const amount = this.options[this.selectedIndex].getAttribute('data-amount');
    const display = document.getElementById('amountDisplay');
    if (amount) {
        display.textContent = "Amount to Pay: ₹" + amount;
    } else {
        display.textContent = "";
    }
});
</script>

</body>
</html>
