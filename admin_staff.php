<?php
include 'header.php';
include 'db.php';

// Fetch all admin staff
$staff = $pdo->query('SELECT * FROM admin_staff')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO admin_staff (first_name, last_name, role, hire_date) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['hire_date']]);
        header('Location: admin_staff.php');
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare('UPDATE admin_staff SET first_name = ?, last_name = ?, role = ?, hire_date = ? WHERE staff_id = ?');
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['hire_date'], $_POST['staff_id']]);
        header('Location: admin_staff.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM admin_staff WHERE staff_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: admin_staff.php');
}

// Get admin staff data for update
$edit_staff = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM admin_staff WHERE staff_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_staff = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Admin Staff</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Admin Staff</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="staff_id" value="<?= $edit_staff['staff_id'] ?? '' ?>">
        <input type="text" name="first_name" placeholder="First Name" value="<?= $edit_staff['first_name'] ?? '' ?>" required>
        <input type="text" name="last_name" placeholder="Last Name" value="<?= $edit_staff['last_name'] ?? '' ?>" required>
        <input type="text" name="role" placeholder="Role" value="<?= $edit_staff['role'] ?? '' ?>" required>
        <input type="date" name="hire_date" value="<?= $edit_staff['hire_date'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_staff ? 'update' : 'add' ?>">
            <?= $edit_staff ? 'Update Staff' : 'Add Staff' ?>
        </button>
    </form>

    <!-- Admin Staff List -->
    <h3>Admin Staff List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Role</th>
            <th>Hire Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($staff as $member): ?>
            <tr>
                <td><?= $member['staff_id'] ?></td>
                <td><?= $member['first_name'] ?></td>
                <td><?= $member['last_name'] ?></td>
                <td><?= $member['role'] ?></td>
                <td><?= $member['hire_date'] ?></td>
                <td>
                    <a href="?edit=<?= $member['staff_id'] ?>">Edit</a>
                    <a href="?delete=<?= $member['staff_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>