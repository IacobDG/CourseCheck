document.getElementById("btn-major-change").addEventListener("click", function(event) {
  event.preventDefault();
  
  var processingMessage = document.createElement("div");
  processingMessage.classList.add("processing-message");
  processingMessage.innerHTML = "Processing...";
  document.body.appendChild(processingMessage);
  
  setTimeout(function() {
    processingMessage.remove();
    window.location.href = "changeMajorForm.php";
  }, 500);
});