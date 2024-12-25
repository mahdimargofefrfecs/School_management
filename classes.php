<?php
include 'header.php';
include 'db.php';

// Fetch all classes data
$classes = $pdo->query('SELECT * FROM classes')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding or updating a class
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = $_POST['class_name'] ?? '';
    $teacher_id = $_POST['teacher_id'] ?? null;  // Default to null if not set
    $room_number = $_POST['room_number'] ?? '';  // Default to empty string if not set

    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO classes (class_name, teacher_id, room_number) VALUES (?, ?, ?)');
        $stmt->execute([$class_name, $teacher_id, $room_number]);
        header('Location: classes.php');
    } elseif (isset($_POST['update'])) {
        $class_id = $_POST['class_id'];
        $stmt = $pdo->prepare('UPDATE classes SET class_name = ?, teacher_id = ?, room_number = ? WHERE class_id = ?');
        $stmt->execute([$class_name, $teacher_id, $room_number, $class_id]);
        header('Location: classes.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM classes WHERE class_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: classes.php');
}

// Get class data for update
$edit_class = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM classes WHERE class_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_class = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Classes</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="class_id" value="<?= $edit_class['class_id'] ?? '' ?>">
        <input type="text" name="class_name" placeholder="Class Name" value="<?= $edit_class['class_name'] ?? '' ?>" required>
        
        <label for="teacher_id">Assign Teacher:</label>
        <select name="teacher_id" required>
            <option value="">Select a teacher</option>
            <?php
            $teachers = $pdo->query('SELECT teacher_id, first_name, last_name FROM teachers')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($teachers as $teacher):
            ?>
                <option value="<?= $teacher['teacher_id'] ?>" <?= isset($edit_class) && $edit_class['teacher_id'] == $teacher['teacher_id'] ? 'selected' : '' ?>>
                    <?= $teacher['first_name'] ?> <?= $teacher['last_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="room_number" placeholder="Room Number" value="<?= isset($edit_class['room_number']) ? $edit_class['room_number'] : '' ?>" required>
        
        <button type="submit" name="<?= $edit_class ? 'update' : 'add' ?>">
            <?= $edit_class ? 'Update Class' : 'Add Class' ?>
        </button>
    </form>

    <!-- Classes List -->
    <h3>Classes List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Class Name</th>
            <th>Assigned Teacher</th>
            <th>Room Number</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($classes as $class): ?>
            <tr>
                <td><?= $class['class_id'] ?></td>
                <td><?= $class['class_name'] ?></td>
                <td>
                    <?php 
                    $teacher = $pdo->prepare('SELECT first_name, last_name FROM teachers WHERE teacher_id = ?');
                    $teacher->execute([$class['teacher_id']]);
                    $teacher_name = $teacher->fetch(PDO::FETCH_ASSOC);
                    echo $teacher_name['first_name'] . ' ' . $teacher_name['last_name'];
                    ?>
                </td>
                <td><?= isset($class['room_number']) ? $class['room_number'] : 'N/A' ?></td> <!-- Check room_number here -->
                <td>
                    <a href="?edit=<?= $class['class_id'] ?>">Edit</a>
                    <a href="?delete=<?= $class['class_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>