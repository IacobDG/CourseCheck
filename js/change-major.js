document.getElementById("btn-course-selection").addEventListener("click", function(event) {
    event.preventDefault();
    
    var processingMessage = document.createElement("div");
    processingMessage.classList.add("processing-message");
    processingMessage.innerHTML = "Processing...";
    document.body.appendChild(processingMessage);
    
    setTimeout(function() {
      processingMessage.remove();
      window.location.href = "course_selection.php";
    }, 500);
  });
  