"use strict";

const historySearch = document.getElementById("historySearch");
const historyRows = Array.from(document.querySelectorAll("#historyList article"));
const historyNoResults = document.getElementById("historyNoResults");

historySearch?.addEventListener("input", () => {
    const search = historySearch.value.trim().toLowerCase();
    let matches = 0;
    historyRows.forEach(row => {
        const visible = row.textContent.toLowerCase().includes(search);
        row.hidden = !visible;
        if (visible) matches += 1;
    });
    if (historyNoResults) historyNoResults.hidden = matches !== 0;
});
