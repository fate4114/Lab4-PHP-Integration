<?php
session_start();

$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];
$errors = [];
$editIndex = null;
$formName = '';
$formDescription = '';

// Shared validation for create and update
function validateTask($name, $description) {
    $errors = [];

    if ($name === '') {
        $errors[] = "Task Name cannot be empty.";
    } elseif (strlen($name) < 3) {
        $errors[] = "Task Name must be at least 3 characters long.";
    }

    if ($description === '') {
        $errors[] = "Task Description is required.";
    } elseif (strlen($description) > 255) {
        $errors[] = "Task Description cannot exceed 255 characters.";
    }

    return $errors;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // DELETE
    if (isset($_POST['delete_index'])) {
        $index = $_POST['delete_index'];

        if (ctype_digit($index) && isset($_SESSION['tasks'][(int)$index])) {
            unset($_SESSION['tasks'][(int)$index]);
            $_SESSION['tasks'] = array_values($_SESSION['tasks']);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $taskName = trim($_POST['task_name'] ?? '');
    $taskDescription = trim($_POST['task_description'] ?? '');
    $errors = validateTask($taskName, $taskDescription);

    // UPDATE
    if (isset($_POST['update_index'])) {
        $index = $_POST['update_index'];

        if (!ctype_digit($index) || !isset($_SESSION['tasks'][(int)$index])) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        if (empty($errors)) {
            $_SESSION['tasks'][(int)$index] = [$taskName, $taskDescription];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $editIndex = (int)$index; // stay in edit mode and show errors
    }
    // CREATE
    else {
        if (empty($errors)) {
            $_SESSION['tasks'][] = [$taskName, $taskDescription];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Keep what the user typed if there were errors
    $formName = $taskName;
    $formDescription = $taskDescription;

} elseif (isset($_GET['edit'])) {
    // Open edit mode: load the task into the form
    $index = $_GET['edit'];

    if (ctype_digit($index) && isset($_SESSION['tasks'][(int)$index])) {
        $editIndex = (int)$index;
        $formName = $_SESSION['tasks'][$editIndex][0];
        $formDescription = $_SESSION['tasks'][$editIndex][1];
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
        <?php if ($editIndex !== null): ?>
          <input type="hidden" name="update_index" value="<?= $editIndex ?>">
        <?php endif; ?>

        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name"
               value="<?= htmlspecialchars($formName, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="task_description">Task Description:</label>
        <input type="text" id="task_description" name="task_description"
               value="<?= htmlspecialchars($formDescription, ENT_QUOTES, 'UTF-8') ?>" required>

        <button type="submit" name="submit">
          <?= $editIndex !== null ? 'Save Changes' : 'Add Task' ?>
        </button>

        <?php if ($editIndex !== null): ?>
          <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="cancel-link">Cancel</a>
        <?php endif; ?>
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
            <div class="actions">
              <a href="?edit=<?= $i ?>" class="edit-btn">Edit</a>
              <form method="POST" class="delete-form">
                <input type="hidden" name="delete_index" value="<?= $i ?>">
                <button type="submit" class="delete-btn">Delete</button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>