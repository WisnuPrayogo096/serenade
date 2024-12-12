function toggleState(button) {
  const icon = button.querySelector("i");
  button.classList.toggle("active");

  if (button.classList.contains("active")) {
    icon.classList.remove("fa-toggle-on");
    icon.classList.add("fa-toggle-off");
  } else {
    icon.classList.remove("fa-toggle-off");
    icon.classList.add("fa-toggle-on");
  }
}

function togglePayment(button) {
  const icon = button.querySelector("i");
  const paymentId = button.getAttribute("data-id");
  const currentStatus = button.getAttribute("data-status");
  const newStatus = currentStatus === "1" ? "0" : "1";

  button.classList.toggle("active");
  if (newStatus === "1") {
    icon.classList.remove("fa-toggle-off");
    icon.classList.add("fa-toggle-on");
  } else {
    icon.classList.remove("fa-toggle-on");
    icon.classList.add("fa-toggle-off");
  }

  fetch("../admin/backend/update-status-payment.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${paymentId}&status=${newStatus}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        alert(data.message || "Failed to update status");
        button.classList.toggle("active");
        if (newStatus === "1") {
          icon.classList.remove("fa-toggle-on");
          icon.classList.add("fa-toggle-off");
        } else {
          icon.classList.remove("fa-toggle-off");
          icon.classList.add("fa-toggle-on");
        }
      } else {
        button.setAttribute("data-status", newStatus);
      }
    })
    .catch((error) => {
      alert("An error occurred. Please try again.");
      console.error(error);
    });
}

function toggleEvent(button) {
  const icon = button.querySelector("i");
  const eventId = button.getAttribute("data-id");
  const currentStatus = button.getAttribute("data-status");
  const newStatus = currentStatus === "1" ? "0" : "1";

  button.classList.toggle("active");
  if (newStatus === "1") {
    icon.classList.remove("fa-toggle-off");
    icon.classList.add("fa-toggle-on");
  } else {
    icon.classList.remove("fa-toggle-on");
    icon.classList.add("fa-toggle-off");
  }

  fetch("../admin/backend/update-status-event.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${eventId}&status=${newStatus}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        alert(data.message || "Failed to update status");
        button.classList.toggle("active");
        if (newStatus === "1") {
          icon.classList.remove("fa-toggle-on");
          icon.classList.add("fa-toggle-off");
        } else {
          icon.classList.remove("fa-toggle-off");
          icon.classList.add("fa-toggle-on");
        }
      } else {
        button.setAttribute("data-status", newStatus);
      }
    })
    .catch((error) => {
      alert("An error occurred. Please try again.");
      console.error(error);
    });
}

function openModal(modalId) {
  document.getElementById(modalId).classList.remove("hidden");
}

function closeModal(modalId) {
  document.getElementById(modalId).classList.add("hidden");
}

function openDetailModal(button) {
  const deskripsi = button.getAttribute("data-deskripsi");
  const modalContent = document.querySelector("#detailModal .space-y-4 p");
  modalContent.innerHTML = deskripsi;

  document.getElementById("detailModal").classList.remove("hidden");
}

function openQrcodeModal(button) {
  const qrcode = button.getAttribute("data-qrcode");
  const modalImage = document.querySelector("#qrcodeImage");
  modalImage.src = qrcode;

  document.getElementById("qrcodeModal").classList.remove("hidden");
}

function openDetailTiketModal(button) {
  try {
    const data = button.dataset;

    document.getElementById("namaEvent").innerText = data.namaEvent || "-";
    document.getElementById("noDay").innerText = data.noDay || "-";
    document.getElementById("noPresale").innerText = data.noPresale || "-";
    document.getElementById("kodeTicket").innerText = data.kodeTicket || "-";
    document.getElementById("jumlahTicket").innerText =
      data.jumlahTicket || "-";
    document.getElementById("total").innerText = data.total || "-";
    document.getElementById("kodeUnik").innerText = data.kodeUnik || "-";
    document.getElementById("totalAkhir").innerText = data.totalAkhir || "-";
    document.getElementById("refId").innerText = data.refId || "-";
    document.getElementById("namaPayment").innerText = data.namaPayment || "-";
    document.getElementById("statusBayar").innerText = data.statusBayar || "-";

    document.getElementById("detailTiketModal").classList.remove("hidden");
  } catch (error) {
    console.error("Error in openDetailTiketModal:", error);
  }
}

function openDetailEventModal(button) {
  try {
    const data = button.dataset;

    document.getElementById("namaEvent").innerText = data.namaEvent || "-";
    document.getElementById("lokasiEvent").innerText = data.lokasiEvent || "-";
    document.getElementById("jam").innerText = data.jamEvent || "-";
    document.getElementById("tanggal").innerText = data.tanggalEvent || "-";
    document.getElementById("kuota").innerText = data.kuotaEvent || "-";
    document.getElementById("status").innerText = data.statusEvent || "-";
    document.getElementById("admin").innerText = data.namaAdmin || "-";

    document.getElementById("detailEventModal").classList.remove("hidden");
  } catch (error) {
    console.error("Error in openDetailEventModal:", error);
  }
}

function openEditModal(id, namaEvent, lokasiEvent, imgBanner) {
  document.getElementById("editModal").classList.remove("hidden");
  document.getElementById("editId").value = id;
  document.getElementById("editNamaEvent").value = namaEvent;
  document.getElementById("editLokasiEvent").value = lokasiEvent;

  const currentImage = document.getElementById("currentImage");
  if (imgBanner && imgBanner !== "null" && imgBanner !== "") {
    currentImage.src = `../assets/img/slider/${imgBanner}`;
    currentImage.style.display = "block";
  } else {
    currentImage.style.display = "none";
  }
}

function openEditModalDay(
  id,
  idEvent,
  noDay,
  jamMulai,
  jamSelesai,
  tanggalPerform,
  deskripsi,
  imgDay
) {
  document.getElementById("editModal").classList.remove("hidden");
  document.getElementById("editId").value = id;
  const selectElement = document.getElementById("editNamaEvent");
  selectElement.value = idEvent;
  document.getElementById("editNoDay").value = noDay;
  document.getElementById("editTanggalDay").value = tanggalPerform;
  document.getElementById("editJamMulai").value = jamMulai.substring(0, 5);
  document.getElementById("editJamSelesai").value = jamSelesai.substring(0, 5);
  document.getElementById("deskripsiEventDayEdit").value = deskripsi;

  const currentImage = document.getElementById("currentImage");
  if (imgDay && imgDay !== "null" && imgDay !== "") {
    currentImage.src = "../assets/img/content/" + imgDay;
    currentImage.style.display = "block";
  } else {
    currentImage.style.display = "none";
  }
}

function openEditModalPresale(id, noPresale, hargaTicket, kuotaTicket) {
  document.getElementById("editModal").classList.remove("hidden");
  document.getElementById("presaleId").value = id;
  document.getElementById("editNoPresale").value = noPresale;
  document.getElementById("editHargaPresale").value = hargaTicket;
  document.getElementById("editKuotaPresale").value = kuotaTicket;
}

function openValidateModal(id, namaUser, kodeTicket) {
  document.getElementById("validateModal").classList.remove("hidden");
  document.getElementById("namaUser").textContent = namaUser;
  const confirmValidate = document.getElementById("confirmValidate");
  confirmValidate.onclick = function () {
    fetch("../admin/backend/validasi-ticket.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        id: id,
        namaUser: namaUser,
        kodeTicket: kodeTicket,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan.");
      });
  };
}

function openDeleteModal(id, namaEvent) {
  document.getElementById("hapusModal").classList.remove("hidden");
  document.getElementById("deleteEventName").textContent = namaEvent;
  const confirmDeleteButton = document.getElementById("confirmDeleteButton");
  confirmDeleteButton.onclick = function () {
    fetch("../admin/backend/delete-event.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menghapus event.");
      });
  };
}

function openDeleteModalDay(id, noDay) {
  document.getElementById("hapusModal").classList.remove("hidden");
  document.getElementById("deleteDay").textContent = noDay;
  const confirmDeleteDayButton = document.getElementById(
    "confirmDeleteDayButton"
  );
  confirmDeleteDayButton.onclick = function () {
    fetch("../admin/backend/delete-day.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menghapus event.");
      });
  };
}

function openDeleteModalPresale(id, noPresale) {
  document.getElementById("hapusModal").classList.remove("hidden");
  document.getElementById("deletePresale").textContent = noPresale;
  const confirmDeletePresaleButton = document.getElementById(
    "confirmDeletePresaleButton"
  );
  confirmDeletePresaleButton.onclick = function () {
    fetch("../admin/backend/delete-presale.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menghapus event.");
      });
  };
}

function openVerifUserModal(id, nameUser) {
  document.getElementById("confirmModal").classList.remove("hidden");
  document.getElementById("nameUser").textContent = nameUser;
  const confirmBtn = document.getElementById("confirmBtn");
  confirmBtn.onclick = function () {
    fetch("../admin/backend/confirm-user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan.");
      });
  };
}

function openBannedUserModal(id, nameUser) {
  document.getElementById("disabledModal").classList.remove("hidden");
  document.getElementById("name").textContent = nameUser;
  const bannedBtn = document.getElementById("bannedBtn");
  bannedBtn.onclick = function () {
    fetch("../admin/backend/disable-user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan.");
      });
  };
}
