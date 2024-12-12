const userButton = document.getElementById("userButton");
const dropdownMenu = document.getElementById("dropdownMenu");

userButton.addEventListener("click", function (event) {
  dropdownMenu.style.display =
    dropdownMenu.style.display === "block" ? "none" : "block";
});

window.addEventListener("click", function (event) {
  if (
    !userButton.contains(event.target) &&
    !dropdownMenu.contains(event.target)
  ) {
    dropdownMenu.style.display = "none";
  }
});
