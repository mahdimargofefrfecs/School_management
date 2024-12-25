<?php
include 'header.php';
include 'db.php';

// Fetch all events
$events = $pdo->query('SELECT * FROM events')->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO events (event_name, event_date, description) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['event_name'], $_POST['event_date'], $_POST['description']]);
        header('Location: events.php');
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare('UPDATE events SET event_name = ?, event_date = ?, description = ? WHERE event_id = ?');
        $stmt->execute([$_POST['event_name'], $_POST['event_date'], $_POST['description'], $_POST['event_id']]);
        header('Location: events.php');
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: events.php');
}

// Get event data for update
$edit_event = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Manage Events</h2>
    
    <!-- Add / Update Form -->
    <form method="POST">
        <input type="hidden" name="event_id" value="<?= $edit_event['event_id'] ?? '' ?>">
        <input type="text" name="event_name" placeholder="Event Name" value="<?= $edit_event['event_name'] ?? '' ?>" required>
        <input type="date" name="event_date" value="<?= $edit_event['event_date'] ?? '' ?>" required>
        <textarea name="description" placeholder="Description" required><?= $edit_event['description'] ?? '' ?></textarea>
        <button type="submit" name="<?= $edit_event ? 'update' : 'add' ?>">
            <?= $edit_event ? 'Update Event' : 'Add Event' ?>
        </button>
    </form>

    <!-- Events List -->
    <h3>Events List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Event Name</th>
            <th>Event Date</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= $event['event_id'] ?></td>
                <td><?= $event['event_name'] ?></td>
                <td><?= $event['event_date'] ?></td>
                <td><?= $event['description'] ?></td>
                <td>
                    <a href="?edit=<?= $event['event_id'] ?>">Edit</a>
                    <a href="?delete=<?= $event['event_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>