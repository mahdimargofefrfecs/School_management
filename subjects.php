<?php
include 'header.php'; // Include the navigation header
include 'db.php'; // Include the database connection file

// Fetch all subjects
$subjects = $pdo->query('SELECT * FROM subjects')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Add a new subject
        $stmt = $pdo->prepare('INSERT INTO subjects (subject_name, teacher_id, credit_hours) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['subject_name'], $_POST['teacher_id'], $_POST['credit_hours']]);
        header('Location: subjects.php');
    } elseif (isset($_POST['update'])) {
        // Update a subject
        $stmt = $pdo->prepare('UPDATE subjects SET subject_name = ?, teacher_id = ?, credit_hours = ? WHERE subject_id = ?');
        $stmt->execute([$_POST['subject_name'], $_POST['teacher_id'], $_POST['credit_hours'], $_POST['subject_id']]);
        header('Location: subjects.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM subjects WHERE subject_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: subjects.php');
}

// Get subject data for update
$edit_subject = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM subjects WHERE subject_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_subject = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Subjects</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="subject_id" value="<?= $edit_subject['subject_id'] ?? '' ?>">
        <input type="text" name="subject_name" placeholder="Subject Name" value="<?= $edit_subject['subject_name'] ?? '' ?>" required>
        <input type="number" name="teacher_id" placeholder="Teacher ID" value="<?= $edit_subject['teacher_id'] ?? '' ?>" required>
        <input type="number" name="credit_hours" placeholder="Credit Hours" value="<?= $edit_subject['credit_hours'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_subject ? 'update' : 'add' ?>">
            <?= $edit_subject ? 'Update Subject' : 'Add Subject' ?>
        </button>
    </form>

    <!-- Subjects List -->
    <h3>Subjects List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Teacher ID</th>
            <th>Credit Hours</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($subjects as $subject): ?>
            <tr>
                <td><?= $subject['subject_id'] ?></td>
                <td><?= $subject['subject_name'] ?></td>
                <td><?= $subject['teacher_id'] ?></td>
                <td><?= $subject['credit_hours'] ?></td>
                <td>
                    <a href="?edit=<?= $subject['subject_id'] ?>">Edit</a>
                    <a href="?delete=<?= $subject['subject_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>