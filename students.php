<?php
include 'header.php';
include 'db.php';

// Fetch Students
$students = $pdo->query('SELECT * FROM students')->fetchAll(PDO::FETCH_ASSOC);

// Add Student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare('INSERT INTO students (first_name, last_name, date_of_birth, gender, grade) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['date_of_birth'], $_POST['gender'], $_POST['grade']]);
    header('Location: students.php');
}

// Delete Student
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: students.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Students</h2>
    <form method="POST">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="date" name="date_of_birth" required>
        <select name="gender">
            <option value="M">Male</option>
            <option value="F">Female</option>
        </select>
        <input type="number" name="grade" placeholder="Grade" required>
        <button type="submit" name="add">Add Student</button>
    </form>

    <h3>Student List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Grade</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= $student['student_id'] ?></td>
                <td><?= $student['first_name'] . ' ' . $student['last_name'] ?></td>
                <td><?= $student['date_of_birth'] ?></td>
                <td><?= $student['gender'] ?></td>
                <td><?= $student['grade'] ?></td>
                <td>
                    <a href="?delete=<?= $student['student_id'] ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>