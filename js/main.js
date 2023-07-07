//21 je max char size za input field drugače se ne vidi vse. pokaži tooltip za več info



$(document).ready(function() {
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()

  })

  function refreshCount () {
    var count = $("#task-list #task").length;
    $("#task-count").text(count);
    var completed = $("#task-list #task:checked").length;
    $("#task-completed").text(completed);
    var not_completed = $("#task-list #task:not(:checked)").length;
    $("#task-not_completed").text(not_completed);
  }
  function updateTaskCompletion(taskId, completed) {
    $.ajax({
      url: 'tasks.php',
      type: 'PUT',
      data: { id: taskId, completed: completed },
      success: function (response) {
        fetchTasks();
        refreshCount();
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  }

  // Fetch tasks from the server
  function fetchTasks() {
    $.ajax({
      url: "tasks.php",
      method: "GET",
      success: function(data) {
        $("#task-list").html(data);
        refreshCount();
      }
    });
  }
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  $('#dobrodosli').toast('show')
  fetchTasks();
  // Dodaj opravilo
  $("#task-form").on("submit", function(event) {
    event.preventDefault();
    var taskInput = $("#task-input");
    $.ajax({
      url: "tasks.php",
      method: "POST",
      data: { task: taskInput.val() },
      success: function() {
        taskInput.val("");
        fetchTasks();
        $('#ustvari ').toast('show')
      }
    });
  });

  // Izbrisi opravilo
  $("#task-list").on("click", ".delete-task", function() {
    var taskId = $(this).data("id");
    $.ajax({
      url: "tasks.php",
      method: "DELETE",
      data: { id: taskId },
      success: function() {
        $('#izbrisi ').toast('show')
        fetchTasks();
        refreshCount();
      }
    });
  });
  $(document).ready(function() {
    // ...

    // Posodobi opravilo
    $("#task-list").on("change", "input[type='checkbox']", function () {
      var taskId = $(this).data('id');
      var completed = this.checked ? 1 : 0;
      updateTaskCompletion(taskId, completed);
      fetchTasks();
      refreshCount();

    });

  });

});


