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
            $taskClass = ($completed == 1) ? "1" : "0";

            //Poglej če je text prazen
            if (empty($taskText)) {
                return;
            }
            //Poglej če je text večji od 21 znakov in ga skrajšaj za 5 znakov in dodaj ... in pa tudi tool tip class
            if(strlen($taskText) > 20) {
                $skrajsanText = substr($taskText, 0, 20) . "...";
              echo '<div id="task-container" class="input-group mb-3">';
              echo '<div class="input-group-prepend">';
              echo '<div class="input-group-text">';
              echo '<div class="form-check align-items-center justify-content-center ">';
              echo '<input id="task" for="checkbox-' . $taskId . '" type="checkbox" class="custom-checkbox form-check-input align-middle" aria-label="Checkbox for following text input" data-id="' . $taskId . '" ' . ($completed == 1 ? 'checked' : '') . '>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
              echo '<label  class="form-control custom-label no-outline text-center ' . $taskClass . '" aria-label="Text input with checkbox">' . $skrajsanText . '</label>';
              echo '<div class="input-group-append">';
              echo '<button data-id="'. $taskId .'" class="btn delete-task" data-toggle="modal" data-target="#exampleModalCenter"  type="button"><i class="bi bi-x"></i></button>';
              echo '</div>';
              echo '</div>';
            }else {
              echo '<div id="task-container" class="input-group mb-3">';
              echo '<div class="input-group-prepend">';
              echo '<div class="input-group-text">';
              echo '<div class="form-check align-items-center justify-content-center ">';
              echo '<input id="task" for="checkbox-' . $taskId . '" type="checkbox" class="custom-checkbox form-check-input align-middle" aria-label="Checkbox for following text input" data-id="' . $taskId . '" ' . ($completed == 1 ? 'checked' : '') . '>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
              echo '<label class="form-control custom-label no-outline text-center ' . $taskClass . '" aria-label="Text input with checkbox">' . $taskText . '</label>';
              echo '<div class="input-group-append">';
              echo '<button data-id="'. $taskId .'" class="btn delete-task"   type="button"><i class="bi bi-x"></i></button>';
              echo '</div>';
              echo '</div>';
            }

        }
    }
}
// Add a new task
// Add a new task
function addTask($task) {
  global $file;
  $taskId = microtime(true);
  $taskData =  $taskId . "|" . $task . "|0";
  $taskData .= PHP_EOL;
  // Check if the file exists
  if (!file_exists($file)) {
    $fileHandle = fopen($file, 'w');
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
  $taskData = implode(PHP_EOL, $tasks);
  if (!empty($taskData)) {
    $taskData .= PHP_EOL;
  }

  file_put_contents($file, $taskData);
}
function markTask($id, $completed) {
  global $file;
  $tasks = file($file, FILE_IGNORE_NEW_LINES);
  $updatedTasks = [];
  foreach ($tasks as $task) {
    $taskData = explode("|", $task);
    $taskId = $taskData[0];
    if ($taskId == $id) {
      $taskData[2] = $completed;
    }
    $updatedTasks[] = implode("|", $taskData);
  }
  $taskData = implode(PHP_EOL, $updatedTasks);
  $taskData .= PHP_EOL;
  file_put_contents($file, $taskData);
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
