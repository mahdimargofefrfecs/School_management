<?php
include 'header.php';
include 'db.php';

// Fetch all teachers
$teachers = $pdo->query('SELECT * FROM teachers')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Add new teacher
        $stmt = $pdo->prepare('INSERT INTO teachers (first_name, last_name, subject, hire_date) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['subject'], $_POST['hire_date']]);
        header('Location: teachers.php');
    } elseif (isset($_POST['update'])) {
        // Update teacher
        $stmt = $pdo->prepare('UPDATE teachers SET first_name = ?, last_name = ?, subject = ?, hire_date = ? WHERE teacher_id = ?');
        $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['subject'], $_POST['hire_date'], $_POST['teacher_id']]);
        header('Location: teachers.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM teachers WHERE teacher_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: teachers.php');
}

// Get teacher data for update
$edit_teacher = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM teachers WHERE teacher_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_teacher = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Teachers</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="teacher_id" value="<?= $edit_teacher['teacher_id'] ?? '' ?>">
        <input type="text" name="first_name" placeholder="First Name" value="<?= $edit_teacher['first_name'] ?? '' ?>" required>
        <input type="text" name="last_name" placeholder="Last Name" value="<?= $edit_teacher['last_name'] ?? '' ?>" required>
        <input type="text" name="subject" placeholder="Subject" value="<?= $edit_teacher['subject'] ?? '' ?>" required>
        <input type="date" name="hire_date" value="<?= $edit_teacher['hire_date'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_teacher ? 'update' : 'add' ?>">
            <?= $edit_teacher ? 'Update Teacher' : 'Add Teacher' ?>
        </button>
    </form>

    <!-- Teachers List -->
    <h3>Teachers List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Subject</th>
            <th>Hire Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= $teacher['teacher_id'] ?></td>
                <td><?= $teacher['first_name'] ?></td>
                <td><?= $teacher['last_name'] ?></td>
                <td><?= $teacher['subject'] ?></td>
                <td><?= $teacher['hire_date'] ?></td>
                <td>
                    <a href="?edit=<?= $teacher['teacher_id'] ?>">Edit</a>
                    <a href="?delete=<?= $teacher['teacher_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>