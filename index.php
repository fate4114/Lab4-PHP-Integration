<?php
session_start();

$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // DELETE
    if (isset($_POST['delete_index'])) {
        $index = $_POST['delete_index'];

        if (ctype_digit($index) && isset($_SESSION['tasks'][(int)$index])) {
            unset($_SESSION['tasks'][(int)$index]);
            $_SESSION['tasks'] = array_values($_SESSION['tasks']); // re-number the list
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // CREATE
    $taskName = trim($_POST['task_name'] ?? '');
    $taskDescription = trim($_POST['task_description'] ?? '');

    if ($taskName === '') {
        $errors[] = "Task Name cannot be empty.";
    } elseif (strlen($taskName) < 3) {
        $errors[] = "Task Name must be at least 3 characters long.";
    }

    if ($taskDescription === '') {
        $errors[] = "Task Description is required.";
    } elseif (strlen($taskDescription) > 255) {
        $errors[] = "Task Description cannot exceed 255 characters.";
    }

    if (empty($errors)) {
        $_SESSION['tasks'][] = [$taskName, $taskDescription];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>To-Do List</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>My To-Do List</h1>
  </header>

  <main>
    <section>
      <?php foreach ($errors as $error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>

      <form method="POST">
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" required>

        <label for="task_description">Task Description:</label>
        <input type="text" id="task_description" name="task_description" required>

        <button type="submit" name="submit">Add Task</button>
      </form>
    </section>

    <section>
      <ul id="task-list">
        <?php foreach ($_SESSION['tasks'] as $i => $task): ?>
          <li class="task-item">
            <span>
              <strong><?= htmlspecialchars($task[0], ENT_QUOTES, 'UTF-8') ?></strong>:
              <?= htmlspecialchars($task[1], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <form method="POST" class="delete-form">
              <input type="hidden" name="delete_index" value="<?= $i ?>">
              <button type="submit" class="delete-btn">Delete</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>