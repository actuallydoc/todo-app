<?php
$file = "data.txt";

// Read tasks from the file
function readTasks() {

    global $file;
    if (file_exists($file)) {
        $tasks = file($file, FILE_IGNORE_NEW_LINES);
        //Če je datoteka prazna prikaži sporočilo
        if (empty($tasks)) {
            echo '<div class="alert alert-secondary" role="alert">';
            echo 'Ni opravil!';
            echo '</div>';
        }
        //Drugače prikaži opravila
        foreach ($tasks as $task) {
            $taskData = explode("|", $task);
            $taskId = $taskData[0];
            $taskText = $taskData[1];
            $completed = $taskData[2];

            $taskClass = ($completed == 1) ? "koncan" : "nekoncan";
          echo '<div id="task-container" class="input-group mb-3 ">';
          echo '<div id="task-container"  class="input-group-prepend">';
          echo '<div class="input-group-text">';
          echo '<div class="form-check ">';
          echo '<input id="task" for="checkbox-' . $taskId . '" type="checkbox" class="form-check-input" aria-label="Checkbox for following text input" data-id="' . $taskId . '" ' . ($completed == 1 ? 'checked' : '') . '>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '<label class="form-control custom-label no-outline ' . $taskClass . '" aria-label="Text input with checkbox">' . $taskText . '</label>';
          echo '<div class="input-group-append">';
          echo '<button data-id="'. $taskId .'" class="btn delete-task"  type="button"><i class="bi bi-x"></i></button>';
          echo '</div>';
          echo '</div>';
        }
    }
}
// Add a new task
// Add a new task
function addTask($task) {
  global $file;
  $taskId = time();
  $taskData = $taskId . "|" . $task . "|0" . PHP_EOL; // Add PHP_EOL to create a new line

  // Check if the file exists
  if (!file_exists($file)) {
    $fileHandle = fopen($file, 'w'); // Open the file in write mode if it doesn't exist
    fclose($fileHandle);
  }

  // Open the file in append mode and write the task
  $fileHandle = fopen($file, 'a');
  fwrite($fileHandle, $taskData);
  fclose($fileHandle);
}
// Delete a task
function deleteTask($id) {
    global $file;
    $tasks = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($tasks as $index => $task) {
        $taskData = explode("|", $task);
        $taskId = $taskData[0];
        if ($taskId == $id) {
            unset($tasks[$index]);
            break;
        }
    }
    file_put_contents($file, implode(PHP_EOL, $tasks));
}
function markTask($id, $uncompleted) {
  global $file;
  $tasks = file($file, FILE_IGNORE_NEW_LINES);
  foreach ($tasks as $index => $task) {
    $taskData = explode("|", $task);
    $taskId = $taskData[0];
    if ($taskId == $id) {
      $taskData[2] = $uncompleted; // Update completed state
      $tasks[$index] = implode("|", $taskData);
      break;
    }
  }
  file_put_contents($file, implode(PHP_EOL, $tasks));
}


if ($_SERVER["REQUEST_METHOD"] === "GET") {
    readTasks();
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["task"])) {
        addTask($_POST["task"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (isset($_DELETE["id"])) {
        deleteTask($_DELETE["id"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
  parse_str(file_get_contents("php://input"), $_PUT);
  if (isset($_PUT["id"]) && isset($_PUT["completed"])) {
    markTask($_PUT["id"], $_PUT["completed"]);
  }
}
?>
