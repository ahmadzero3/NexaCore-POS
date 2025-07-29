document.addEventListener("DOMContentLoaded", function () {
    const itemsGrid = document.getElementById("itemsGrid");
    const dropdownItems = document.querySelectorAll(
        ".dropdown-item[data-value]"
    );
    const sortingPreferenceInput =
        document.getElementById("sorting_preference");
    const manualOrderInput = document.getElementById("manual_order_input");
    let sortableInstance = null;

    // Dynamically get the manual order array from the hidden input
    function getManualOrder() {
        try {
            const input = document.getElementById("manual_order_input");
            const parsed = input ? JSON.parse(input.value) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            console.warn("Failed to parse manual order input:", e);
            return [];
        }
    }

    function applySort(type) {
        const items = Array.from(itemsGrid.children);
        items.sort((a, b) => {
            const aName = a
                .querySelector(".card-title")
                .textContent.trim()
                .toLowerCase();
            const bName = b
                .querySelector(".card-title")
                .textContent.trim()
                .toLowerCase();
            const aDate = new Date(a.dataset.date);
            const bDate = new Date(b.dataset.date);

            switch (type) {
                case "a_to_z":
                    return aName.localeCompare(bName);
                case "z_to_a":
                    return bName.localeCompare(aName);
                case "latest_product":
                    return bDate - aDate;
                case "oldest_product":
                    return aDate - bDate;
                default:
                    return 0;
            }
        });
        items.forEach((item) => itemsGrid.appendChild(item));
    }

    function enableManualSorting() {
        if (sortableInstance) return;
        sortableInstance = Sortable.create(itemsGrid, {
            animation: 150,
            onEnd: persistOrder,
        });
    }

    function disableManualSorting() {
        if (!sortableInstance) return;
        sortableInstance.destroy();
        sortableInstance = null;
    }

    function restoreManualOrder() {
        const manualOrder = getManualOrder();
        manualOrder.forEach((id) => {
            const el = itemsGrid.querySelector(`[data-id="${id}"]`);
            if (el) itemsGrid.appendChild(el);
        });
    }

    function persistOrder() {
        const newOrder = Array.from(itemsGrid.children).map((el) =>
            parseInt(el.dataset.id, 10)
        );
        fetch("/pos/save-sorting-preference", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({
                sorting_preference: "manual_sorting",
                manual_order: newOrder,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.preference === "manual_sorting" && data.manualOrder) {
                    document.getElementById("manual_order_input").value =
                        JSON.stringify(data.manualOrder);
                } else {
                    document.getElementById("manual_order_input").value = "[]";
                }
            })
            .catch(console.error);
    }

    const initialPref = sortingPreferenceInput.value;
    if (initialPref === "manual_sorting") {
        enableManualSorting();
        restoreManualOrder();
    } else {
        applySort(initialPref);
    }

    dropdownItems.forEach((item) => {
        if (item.dataset.value === initialPref) {
            item.classList.add("active");
        }
    });

    dropdownItems.forEach((item) => {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            const selectedOption = this.dataset.value;

            dropdownItems.forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
            sortingPreferenceInput.value = selectedOption;

            if (selectedOption === "manual_sorting") {
                enableManualSorting();
                restoreManualOrder();
            } else {
                disableManualSorting();
                applySort(selectedOption);

                // Reset grid + reload
                currentPage = 0;
                startFromFirst = 0;
                $("#itemsGrid").empty();
                loadMoreItems();
            }

            fetch("/pos/save-sorting-preference", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({ sorting_preference: selectedOption }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (
                        data.preference === "manual_sorting" &&
                        data.manualOrder
                    ) {
                        document.getElementById("manual_order_input").value =
                            JSON.stringify(data.manualOrder);
                    } else {
                        document.getElementById("manual_order_input").value =
                            "[]";
                    }
                })
                .catch(console.error);
        });
    });
});
