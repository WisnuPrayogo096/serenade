const userButton = document.getElementById("userButton");
const dropdownMenu = document.getElementById("dropdownMenu");
userButton.addEventListener("click", function () {
  dropdownMenu.classList.toggle("hidden");
});
window.addEventListener("click", function (event) {
  if (
    !userButton.contains(event.target) &&
    !dropdownMenu.contains(event.target)
  ) {
    dropdownMenu.classList.add("hidden");
  }
});
