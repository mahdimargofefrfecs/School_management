<?php
include 'header.php'; // Navigation header
include 'db.php'; // Database connection

// Fetch all enrollments
$enrollments = $pdo->query('SELECT * FROM enrollments')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO enrollments (student_id, class_id, enrollment_date) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['student_id'], $_POST['class_id'], $_POST['enrollment_date']]);
        header('Location: enrollments.php');
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare('UPDATE enrollments SET student_id = ?, class_id = ?, enrollment_date = ? WHERE enrollment_id = ?');
        $stmt->execute([$_POST['student_id'], $_POST['class_id'], $_POST['enrollment_date'], $_POST['enrollment_id']]);
        header('Location: enrollments.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM enrollments WHERE enrollment_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: enrollments.php');
}

// Get enrollment data for update
$edit_enrollment = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM enrollments WHERE enrollment_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Enrollments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Enrollments</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="enrollment_id" value="<?= $edit_enrollment['enrollment_id'] ?? '' ?>">
        <input type="number" name="student_id" placeholder="Student ID" value="<?= $edit_enrollment['student_id'] ?? '' ?>" required>
        <input type="number" name="class_id" placeholder="Class ID" value="<?= $edit_enrollment['class_id'] ?? '' ?>" required>
        <input type="date" name="enrollment_date" value="<?= $edit_enrollment['enrollment_date'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_enrollment ? 'update' : 'add' ?>">
            <?= $edit_enrollment ? 'Update Enrollment' : 'Add Enrollment' ?>
        </button>
    </form>

    <!-- Enrollments List -->
    <h3>Enrollments List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>Class ID</th>
            <th>Enrollment Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($enrollments as $enrollment): ?>
            <tr>
                <td><?= $enrollment['enrollment_id'] ?></td>
                <td><?= $enrollment['student_id'] ?></td>
                <td><?= $enrollment['class_id'] ?></td>
                <td><?= $enrollment['enrollment_date'] ?></td>
                <td>
                    <a href="?edit=<?= $enrollment['enrollment_id'] ?>">Edit</a>
                    <a href="?delete=<?= $enrollment['enrollment_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>