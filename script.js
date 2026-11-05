document.addEventListener("DOMContentLoaded", function () {
  // 1. Confirm before deleting a task
  document.querySelectorAll(".delete-btn").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      if (!confirm("Delete this task?")) {
        e.preventDefault();
      }
    });
  });

  // 2. Live character counter on the description input
  var desc = document.getElementById("task_description");
  var counter = document.createElement("small");
  counter.textContent = desc.value.length + " / 255";
  desc.insertAdjacentElement("afterend", counter);

  desc.addEventListener("input", function () {
    counter.textContent = desc.value.length + " / 255";
  });

  // 3. Don't allow a name that's only spaces
  var form = document.querySelector("form");
  form.addEventListener("submit", function (e) {
    var name = document.getElementById("task_name").value.trim();
    if (name === "") {
      alert("Task name can't be empty.");
      e.preventDefault();
    }
  });
});