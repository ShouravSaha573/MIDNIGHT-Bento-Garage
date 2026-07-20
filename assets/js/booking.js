"use strict";

const menuToggle = document.getElementById("menuToggle");
const mainNav = document.getElementById("mainNav");
const dateInput = document.getElementById("appointmentDate");
const mechanicGrid = document.getElementById("mechanicGrid");
const mechanicInput = document.getElementById("mechanicId");
const bookingForm = document.getElementById("bookingForm");
const bookingButton = document.getElementById("bookingButton");

if (menuToggle && mainNav) {
    menuToggle.addEventListener("click", () => {
        const open = mainNav.classList.toggle("open");
        menuToggle.setAttribute("aria-expanded", String(open));
    });
}

function formatDate(dateValue) {
    const parts = dateValue.split("-");
    if (parts.length !== 3) return dateValue;
    const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    return new Intl.DateTimeFormat("en-GB", { day: "2-digit", month: "short", year: "numeric" }).format(date);
}

function initials(name) {
    return name.split(/\s+/).filter(Boolean).map(part => part[0]).slice(0, 2).join("").toUpperCase();
}

function setFieldError(name, message) {
    const field = bookingForm?.elements.namedItem(name);
    const error = document.querySelector(`[data-error-for="${name}"]`);
    if (field && "classList" in field) field.classList.toggle("is-invalid", Boolean(message));
    if (error) error.textContent = message;
}

function clearErrors() {
    document.querySelectorAll(".field-error").forEach(element => element.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(element => element.classList.remove("is-invalid"));
}

function validateForm() {
    clearErrors();
    let valid = true;
    const data = new FormData(bookingForm);
    const name = String(data.get("client_name") || "").trim();
    const address = String(data.get("address") || "").trim();
    const phone = String(data.get("phone") || "").replace(/\D+/g, "");
    const license = String(data.get("car_license") || "").trim();
    const engine = String(data.get("car_engine") || "").trim();
    const date = String(data.get("appointment_date") || "");
    const mechanic = String(data.get("mechanic_id") || "");

    if (name.length < 2) { setFieldError("client_name", "Please enter your full name."); valid = false; }
    if (address.length < 5) { setFieldError("address", "Please enter a complete address."); valid = false; }
    if (!/^\d{7,15}$/.test(phone)) { setFieldError("phone", "Phone number must contain 7 to 15 digits."); valid = false; }
    if (!/^[A-Za-z0-9 -]{2,50}$/.test(license)) { setFieldError("car_license", "Enter a valid car registration number."); valid = false; }
    if (!/^[A-Za-z0-9-]{2,50}$/.test(engine)) { setFieldError("car_engine", "Engine number may contain letters, numbers and hyphens."); valid = false; }
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const localTomorrow = new Date(tomorrow.getTime() - tomorrow.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
    if (!date || date < localTomorrow) { setFieldError("appointment_date", "Appointments must be requested at least one day in advance."); valid = false; }
    if (!mechanic) { setFieldError("mechanic_id", "Please select an available mechanic."); valid = false; }

    return valid;
}

function selectMechanic(card) {
    if (card.disabled) return;
    document.querySelectorAll(".mechanic-card").forEach(item => {
        item.classList.remove("selected");
        item.setAttribute("aria-pressed", "false");
    });
    card.classList.add("selected");
    card.setAttribute("aria-pressed", "true");
    mechanicInput.value = card.dataset.mechanicId || "";
    setFieldError("mechanic_id", "");
}

function attachMechanicEvents() {
    document.querySelectorAll(".mechanic-card").forEach(card => {
        card.addEventListener("click", () => selectMechanic(card));
    });
}

function createMechanicCard(mechanic) {
    const card = document.createElement("button");
    card.type = "button";
    card.className = `mechanic-card ${mechanic.status}`;
    card.dataset.mechanicId = mechanic.id;
    card.dataset.mechanicName = mechanic.name;
    card.disabled = mechanic.is_full;
    card.setAttribute("aria-pressed", "false");

    const dots = Array.from({ length: 4 }, (_, index) =>
        `<i class="${index < mechanic.booked ? "booked-dot" : "free-dot"}"></i>`
    ).join("");

    card.innerHTML = `
        <span class="selected-check" aria-hidden="true">✓</span>
        <span class="mechanic-avatar">${initials(mechanic.name)}</span>
        <strong>${mechanic.name}</strong>
        <small>${mechanic.role_title}</small>
        <span class="availability-text">${mechanic.free} of 4 places available</span>
        <span class="slot-dots" aria-hidden="true">${dots}</span>
        <span class="mechanic-status">${mechanic.is_full ? "Fully Booked" : (mechanic.free === 1 ? "Almost Full" : "Available")}</span>
    `;
    card.addEventListener("click", () => selectMechanic(card));
    return card;
}

function updateSummary(summary, date) {
    const freeMain = document.getElementById("capacityFreeMain");
    const free = document.getElementById("summaryFree");
    const booked = document.getElementById("summaryBooked");
    const full = document.getElementById("summaryFull");
    const donut = document.getElementById("capacityDonut");
    const capacityDate = document.getElementById("capacityDate");
    const mechanicDate = document.getElementById("mechanicDateLabel");

    if (freeMain) freeMain.textContent = summary.free;
    if (free) free.textContent = summary.free;
    if (booked) booked.textContent = summary.booked;
    if (full) full.textContent = summary.full;
    if (donut) donut.style.setProperty("--booked-percent", summary.booked_percent);
    if (capacityDate) capacityDate.textContent = formatDate(date);
    if (mechanicDate) mechanicDate.textContent = formatDate(date);
}

async function loadAvailability(date) {
    if (!date || !mechanicGrid) return;

    mechanicGrid.classList.add("loading");
    mechanicGrid.innerHTML = `<div class="loading-state"><span></span><p>Checking mechanic availability…</p></div>`;
    mechanicInput.value = "";

    try {
        const response = await fetch(`get_availability.php?date=${encodeURIComponent(date)}`, {
            headers: { "Accept": "application/json" }
        });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Availability could not be loaded.");

        mechanicGrid.innerHTML = "";
        result.mechanics.forEach(mechanic => mechanicGrid.appendChild(createMechanicCard(mechanic)));
        updateSummary(result.summary, date);
    } catch (error) {
        mechanicGrid.innerHTML = `<div class="empty-state error-state">${error.message}</div>`;
    } finally {
        mechanicGrid.classList.remove("loading");
    }
}

if (dateInput) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const localTomorrow = new Date(tomorrow.getTime() - tomorrow.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
    dateInput.min = localTomorrow;
    dateInput.addEventListener("change", () => loadAvailability(dateInput.value));
}

if (bookingForm) {
    bookingForm.addEventListener("submit", event => {
        if (!validateForm()) {
            event.preventDefault();
            document.querySelector(".is-invalid")?.focus();
            return;
        }

        if (bookingButton) {
            bookingButton.disabled = true;
            bookingButton.querySelector(".button-label")?.setAttribute("hidden", "");
            bookingButton.querySelector(".button-loading")?.removeAttribute("hidden");
        }
    });
}

attachMechanicEvents();
