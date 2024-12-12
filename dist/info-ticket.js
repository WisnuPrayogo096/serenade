document.addEventListener("DOMContentLoaded", () => {
  const lanjutkanButton = document.getElementById("lanjutkanButton");
  const confirmModal = document.getElementById("confirmModal");
  const closeModalButton = document.getElementById("closeModal");
  const editDataButton = document.getElementById("editDataButton");
  confirmModal.style.display = "none";

  lanjutkanButton.addEventListener("click", () => {
    confirmModal.style.display = "flex";
  });

  closeModalButton.addEventListener("click", () => {
    confirmModal.style.display = "none";
  });

  editDataButton.addEventListener("click", () => {
    confirmModal.style.display = "none";
  });

  confirmModal.addEventListener("click", (event) => {
    if (event.target === confirmModal) {
      confirmModal.style.display = "none";
    }
  });
});
