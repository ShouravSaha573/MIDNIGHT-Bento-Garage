"use strict";

const adminMenuToggle = document.getElementById("adminMenuToggle");
const adminSidebar = document.getElementById("adminSidebar");
const editDate = document.getElementById("editDate");
const editMechanic = document.getElementById("editMechanic");
const editAppointmentId = document.getElementById("editAppointmentId");
const editCapacityMessage = document.getElementById("editCapacityMessage");
const adminEditForm = document.getElementById("adminEditForm");
const saveChangesButton = document.getElementById("saveChangesButton");

if (adminMenuToggle && adminSidebar) {
    const setMenuOpen = open => {
        adminSidebar.classList.toggle("open", open);
        document.body.classList.toggle("admin-menu-open", open);
        adminMenuToggle.setAttribute("aria-expanded", String(open));
        adminMenuToggle.setAttribute("aria-label", open ? "Close admin menu" : "Open admin menu");
    };
    adminMenuToggle.addEventListener("click", () => setMenuOpen(!adminSidebar.classList.contains("open")));
    document.addEventListener("keydown", event => {
        if (event.key === "Escape") setMenuOpen(false);
    });
    document.addEventListener("click", event => {
        if (!adminSidebar.classList.contains("open")) return;
        if (!adminSidebar.contains(event.target) && !adminMenuToggle.contains(event.target)) setMenuOpen(false);
    });
}

async function updateEditMechanicOptions() {
    if (!editDate || !editMechanic || !editAppointmentId) return;

    editCapacityMessage.textContent = "Checking availability…";
    const currentValue = editMechanic.value;

    try {
        const response = await fetch(
            `get_capacity.php?date=${encodeURIComponent(editDate.value)}&exclude_id=${encodeURIComponent(editAppointmentId.value)}`,
            { headers: { "Accept": "application/json" } }
        );
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Capacity could not be loaded.");

        editMechanic.innerHTML = "";
        result.mechanics.forEach(mechanic => {
            const option = document.createElement("option");
            option.value = mechanic.id;
            option.textContent = `${mechanic.name} — ${mechanic.free} free`;
            option.disabled = mechanic.is_full;
            if (String(mechanic.id) === String(currentValue) && !option.disabled) option.selected = true;
            editMechanic.appendChild(option);
        });
        editCapacityMessage.textContent = "The list shows free places after excluding the current appointment.";
    } catch (error) {
        editCapacityMessage.textContent = error.message;
    }
}

editDate?.addEventListener("change", updateEditMechanicOptions);

adminEditForm?.addEventListener("submit", () => {
    if (saveChangesButton) {
        saveChangesButton.disabled = true;
        saveChangesButton.textContent = "Saving…";
    }
});
