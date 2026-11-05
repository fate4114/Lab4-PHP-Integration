<?php
session_start();

$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION['tasks'][] = [
        $_POST['task_name'],
        $_POST['task_description']
    ];
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
        <?php foreach ($_SESSION['tasks'] as $task): ?>
          <li><strong><?= $task[0] ?></strong>: <?= $task[1] ?></li>
        <?php endforeach; ?>
      </ul>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>