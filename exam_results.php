<?php
include 'header.php';
include 'db.php';

// Fetch all exam results
$results = $pdo->query('SELECT * FROM exam_results')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO exam_results (exam_id, student_id, marks) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['exam_id'], $_POST['student_id'], $_POST['marks']]);
        header('Location: exam_results.php');
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare('UPDATE exam_results SET exam_id = ?, student_id = ?, marks = ? WHERE result_id = ?');
        $stmt->execute([$_POST['exam_id'], $_POST['student_id'], $_POST['marks'], $_POST['result_id']]);
        header('Location: exam_results.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM exam_results WHERE result_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: exam_results.php');
}

// Get exam result data for update
$edit_result = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM exam_results WHERE result_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exam Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Exam Results</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="result_id" value="<?= $edit_result['result_id'] ?? '' ?>">
        <input type="number" name="exam_id" placeholder="Exam ID" value="<?= $edit_result['exam_id'] ?? '' ?>" required>
        <input type="number" name="student_id" placeholder="Student ID" value="<?= $edit_result['student_id'] ?? '' ?>" required>
        <input type="number" name="marks" placeholder="Marks" value="<?= $edit_result['marks'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_result ? 'update' : 'add' ?>">
            <?= $edit_result ? 'Update Result' : 'Add Result' ?>
        </button>
    </form>

    <!-- Exam Results List -->
    <h3>Exam Results List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Exam ID</th>
            <th>Student ID</th>
            <th>Marks</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($results as $result): ?>
            <tr>
                <td><?= $result['result_id'] ?></td>
                <td><?= $result['exam_id'] ?></td>
                <td><?= $result['student_id'] ?></td>
                <td><?= $result['marks'] ?></td>
                <td>
                    <a href="?edit=<?= $result['result_id'] ?>">Edit</a>
                    <a href="?delete=<?= $result['result_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>