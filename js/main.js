
$(document).ready(function() {

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
        console.log(xhr.responseText); // Log any errors
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
        console.log(data)
        refreshCount();
      }
    });
  }
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  $('#dobrodosli').toast('show')

  // Call fetchTasks on page load
  fetchTasks();
  // Add task
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

  // Delete task
  $("#task-list").on("click", ".delete-task", function() {
    var taskId = $(this).data("id");
    console.log(taskId)
    $.ajax({
      url: "tasks.php",
      method: "DELETE",
      data: { id: taskId },
      success: function() {
        $('#izbrisi ').toast('show')
        fetchTasks();
      }
    });
  });
  $(document).ready(function() {
    // ...

    // Toggle completed state
    $("#task-list").on("change", "input[type='checkbox']", function () {
      var taskId = $(this).data('id');
      var completed = this.checked ? 1 : 0;
      console.log(taskId)
      console.log(completed)
      console.log(this.checked)
      updateTaskCompletion(taskId, completed);
      fetchTasks();
      refreshCount();

    });

  });

});


