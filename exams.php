<?php
include 'header.php';
include 'db.php';

// Fetch all exams
$exams = $pdo->query('SELECT * FROM exams')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO exams (exam_name, exam_date, class_id) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['exam_name'], $_POST['exam_date'], $_POST['class_id']]);
        header('Location: exams.php');
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare('UPDATE exams SET exam_name = ?, exam_date = ?, class_id = ? WHERE exam_id = ?');
        $stmt->execute([$_POST['exam_name'], $_POST['exam_date'], $_POST['class_id'], $_POST['exam_id']]);
        header('Location: exams.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM exams WHERE exam_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: exams.php');
}

// Get exam data for update
$edit_exam = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM exams WHERE exam_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_exam = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exams</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Exams</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="exam_id" value="<?= $edit_exam['exam_id'] ?? '' ?>">
        <input type="text" name="exam_name" placeholder="Exam Name" value="<?= $edit_exam['exam_name'] ?? '' ?>" required>
        <input type="date" name="exam_date" value="<?= $edit_exam['exam_date'] ?? '' ?>" required>
        <input type="number" name="class_id" placeholder="Class ID" value="<?= $edit_exam['class_id'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_exam ? 'update' : 'add' ?>">
            <?= $edit_exam ? 'Update Exam' : 'Add Exam' ?>
        </button>
    </form>

    <!-- Exams List -->
    <h3>Exams List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Exam Name</th>
            <th>Exam Date</th>
            <th>Class ID</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($exams as $exam): ?>
            <tr>
                <td><?= $exam['exam_id'] ?></td>
                <td><?= $exam['exam_name'] ?></td>
                <td><?= $exam['exam_date'] ?></td>
                <td><?= $exam['class_id'] ?></td>
                <td>
                    <a href="?edit=<?= $exam['exam_id'] ?>">Edit</a>
                    <a href="?delete=<?= $exam['exam_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>