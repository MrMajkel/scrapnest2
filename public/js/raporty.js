let selectedFormat = "";

function generujRaport(format) {
  selectedFormat = format;

  let wrapper = document.getElementById("modalWrapper");
  if (!wrapper) {
    wrapper = document.createElement("div");
    wrapper.id = "modalWrapper";
    wrapper.classList.add("modal");

    const modal = document.getElementById("dateModal");
    wrapper.appendChild(modal);
    document.body.appendChild(wrapper);

    wrapper.addEventListener("click", (e) => {
      if (e.target === wrapper) {
        closeModal();
      }
    });
  }

  const input = document.getElementById("dateInput");
  input.value = new Date().toISOString().split("T")[0];
  wrapper.style.display = "flex";
  document.getElementById("dateModal").style.display = "flex";
}

function submitDate() {
  const data = document.getElementById("dateInput").value;
  if (!data) return;

  const url = `backend/mod_raporty.php?format=${selectedFormat}&data=${data}`;
  window.open(url, '_blank');
  closeModal();
}

function closeModal() {
  const wrapper = document.getElementById("modalWrapper");
  if (wrapper) wrapper.style.display = "none";
  const modal = document.getElementById("dateModal");
  if (modal) modal.style.display = "none";
}
