<?php
include 'header.php';
include 'db.php';

// Fetch all attendance records
$attendance = $pdo->query('SELECT * FROM attendance')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO attendance (student_id, class_id, attendance_date, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['student_id'], $_POST['class_id'], $_POST['attendance_date'], $_POST['status']]);
        header('Location: attendance.php');
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare('UPDATE attendance SET student_id = ?, class_id = ?, attendance_date = ?, status = ? WHERE attendance_id = ?');
        $stmt->execute([$_POST['student_id'], $_POST['class_id'], $_POST['attendance_date'], $_POST['status'], $_POST['attendance_id']]);
        header('Location: attendance.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM attendance WHERE attendance_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: attendance.php');
}

// Get attendance data for update
$edit_attendance = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM attendance WHERE attendance_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_attendance = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Attendance</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="attendance_id" value="<?= $edit_attendance['attendance_id'] ?? '' ?>">
        <input type="number" name="student_id" placeholder="Student ID" value="<?= $edit_attendance['student_id'] ?? '' ?>" required>
        <input type="number" name="class_id" placeholder="Class ID" value="<?= $edit_attendance['class_id'] ?? '' ?>" required>
        <input type="date" name="attendance_date" value="<?= $edit_attendance['attendance_date'] ?? '' ?>" required>
        <select name="status" required>
            <option value="Present" <?= ($edit_attendance['status'] ?? '') == 'Present' ? 'selected' : '' ?>>Present</option>
            <option value="Absent" <?= ($edit_attendance['status'] ?? '') == 'Absent' ? 'selected' : '' ?>>Absent</option>
            <option value="Late" <?= ($edit_attendance['status'] ?? '') == 'Late' ? 'selected' : '' ?>>Late</option>
        </select>
        <button type="submit" name="<?= $edit_attendance ? 'update' : 'add' ?>">
            <?= $edit_attendance ? 'Update Attendance' : 'Add Attendance' ?>
        </button>
    </form>

    <!-- Attendance List -->
    <h3>Attendance List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Class ID</th>
            <th>Attendance Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($attendance as $record): ?>
            <tr>
                <td><?= $record['attendance_id'] ?></td>
                <td><?= $record['student_id'] ?></td>
                <td><?= $record['class_id'] ?></td>
                <td><?= $record['attendance_date'] ?></td>
                <td><?= $record['status'] ?></td>
                <td>
                    <a href="?edit=<?= $record['attendance_id'] ?>">Edit</a>
                    <a href="?delete=<?= $record['attendance_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>