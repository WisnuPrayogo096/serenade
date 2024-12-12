function toggleSection(section) {
  const descriptionContent = document.getElementById("descriptionContent");
  const ticketContent = document.getElementById("ticketContent");
  const descriptionBtn = document.getElementById("descriptionBtn");
  const ticketBtn = document.getElementById("ticketBtn");
  const buyTicketBtn = document.getElementById("buyTicketBtn");

  if (section === "description") {
    descriptionContent.classList.remove("hidden");
    ticketContent.classList.add("hidden");
    descriptionBtn.classList.add("bg-[#000B58]", "text-white");
    descriptionBtn.classList.remove("bg-[#EBF1FF]", "text-[#6A7EFF]");
    ticketBtn.classList.remove("bg-[#000B58]", "text-white");
    ticketBtn.classList.add("bg-[#EBF1FF]", "text-[#6A7EFF]");
    buyTicketBtn.classList.remove("hidden");
  } else {
    descriptionContent.classList.add("hidden");
    ticketContent.classList.remove("hidden");
    descriptionBtn.classList.remove("bg-[#000B58]", "text-white");
    descriptionBtn.classList.add("bg-[#EBF1FF]", "text-[#6A7EFF]");
    ticketBtn.classList.remove("bg-[#EBF1FF]", "text-[#6A7EFF]");
    ticketBtn.classList.add("bg-[#000B58]", "text-white");
    buyTicketBtn.classList.add("hidden");
  }
}

function scrollToBuyTicket() {
  const ticketBtn = document.getElementById("ticketBtn");
  toggleSection("ticket");
  document
    .getElementById("buyTicketSection")
    .scrollIntoView({ behavior: "smooth" });
}

document.addEventListener("DOMContentLoaded", function () {
  const ticketContainers = document.querySelectorAll(".ticket-container");

  ticketContainers.forEach((container) => {
    const selectHandler = function () {
      const originalSection = container.querySelector(".original-section");
      const priceSection = container.querySelector(".price-section");

      originalSection.classList.add(
        "opacity-0",
        "transition-opacity",
        "duration-300"
      );
      setTimeout(() => {
        originalSection.style.display = "none";

        const quantitySection = document.createElement("div");
        let hargaTicket = container.getAttribute("data-harga-ticket");

        function formatRupiah(angka) {
          return Number(angka)
            .toLocaleString("id-ID", {
              style: "currency",
              currency: "IDR",
              minimumFractionDigits: 0,
            })
            .replace(/,00$/, "");
        }
        hargaTicket = formatRupiah(hargaTicket);

        quantitySection.className =
          "quantity-section opacity-0 transition-opacity duration-300";
        quantitySection.innerHTML = `
                    <div class="flex flex-row justify-between items-center">
                        <div>
                            <p class="text-gray-400">Harga</p>
                            <p class='text-orange-500 font-bold'>${hargaTicket}</p>
                        </div>
                        <div class="flex flex-row gap-5 items-center">
                            <button class="decrement-btn py-2 px-3 bg-gray-100 rounded-xl text-xs text-[#000B58] hover:bg-[#000B58] hover:text-white">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <p class="quantity">1</p>
                            <button class="increment-btn py-2 px-3 bg-gray-100 rounded-xl text-xs text-[#000B58] hover:bg-[#000B58] hover:text-white">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-row justify-end mt-5">
                        <div class="flex flex-row items-center gap-8">
                            <button class="cancel-btn text-sm text-[#000B58]">Batal</button>
                            <button class="buy-btn bg-[#000B58] px-5 py-2 rounded-lg text-white text-sm hover:opacity-90 hover:shadow-md">
                                Beli
                            </button>
                        </div>
                    </div>
                `;

        priceSection.innerHTML = "";
        priceSection.appendChild(quantitySection);

        requestAnimationFrame(() => {
          quantitySection.classList.remove("opacity-0");
        });

        const decrementBtn = quantitySection.querySelector(".decrement-btn");
        const incrementBtn = quantitySection.querySelector(".increment-btn");
        const quantityDisplay = quantitySection.querySelector(".quantity");

        incrementBtn.addEventListener("click", () => {
          let currentQuantity = parseInt(quantityDisplay.textContent);
          quantityDisplay.textContent = currentQuantity + 1;
        });

        decrementBtn.addEventListener("click", () => {
          let currentQuantity = parseInt(quantityDisplay.textContent);
          if (currentQuantity > 1) {
            quantityDisplay.textContent = currentQuantity - 1;
          }
        });

        const cancelBtn = quantitySection.querySelector(".cancel-btn");
        cancelBtn.addEventListener("click", () => {
          quantitySection.classList.add("opacity-0");

          setTimeout(() => {
            const newOriginalSection = document.createElement("div");
            let hargaTicket = container.getAttribute("data-harga-ticket");

            function formatRupiah(angka) {
              return Number(angka)
                .toLocaleString("id-ID", {
                  style: "currency",
                  currency: "IDR",
                  minimumFractionDigits: 0,
                })
                .replace(/,00$/, "");
            }
            hargaTicket = formatRupiah(hargaTicket);
            newOriginalSection.className =
              "original-section flex flex-row justify-between items-center opacity-0 transition-opacity duration-300";
            newOriginalSection.innerHTML = `
                            <div>
                                <p class="text-gray-400">Harga</p>
                                <p class='text-orange-500 font-bold'>${hargaTicket}</p>
                            </div>
                            <button class="select-ticket-btn bg-[#000B58] px-5 py-2 rounded-lg text-white text-sm hover:opacity-90 hover:shadow-md">
                                Pilih
                            </button>
                        `;

            priceSection.innerHTML = "";
            priceSection.appendChild(newOriginalSection);

            requestAnimationFrame(() => {
              newOriginalSection.classList.remove("opacity-0");
            });

            newOriginalSection
              .querySelector(".select-ticket-btn")
              .addEventListener("click", selectHandler);
          }, 300);
        });

        const buyBtn = quantitySection.querySelector(".buy-btn");
        buyBtn.addEventListener("click", () => {
          const selectedTicket = {
            event_id: container.getAttribute("data-event-id"),
            day_event_id: container.getAttribute("data-day-event-id"),
            presale_ticket_id: container.getAttribute("data-presale-ticket-id"),
            nama_event: container.getAttribute("data-nama-event"),
            no_day: container.getAttribute("data-no-day"),
            no_presale: container.getAttribute("data-no-presale"),
            jumlah_tiket: parseInt(quantityDisplay.textContent),
            harga_ticket: container.getAttribute("data-harga-ticket"),
          };

          const form = document.createElement("form");
          form.method = "POST";
          form.action = "information-ticket.php";

          for (const key in selectedTicket) {
            const hiddenField = document.createElement("input");
            hiddenField.type = "hidden";
            hiddenField.name = key;
            hiddenField.value = selectedTicket[key];
            form.appendChild(hiddenField);
          }

          document.body.appendChild(form);
          form.submit();
        });
      }, 300);
    };

    const selectButton = container.querySelector(".select-ticket-btn");
    selectButton.addEventListener("click", selectHandler);
  });
});
