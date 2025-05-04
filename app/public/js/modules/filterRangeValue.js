function updateRangeValue(rangeInputId, displayElementId, unit, order) {
    const rangeInput = document.getElementById(rangeInputId)
    const displayElement = document.getElementById(displayElementId)

    if (!rangeInput || !displayElement) {
        console.error(`Erreur: Impossible de trouver l'élément avec ID ${rangeInputId} ou ${displayElementId}`)
        return
    }

    function updateValue() {
        displayElement.innerHTML = rangeInput.value + ' ' + unit
    }

    if (order === 'max') {
        rangeInput.value = rangeInput.max
    } else if (order === 'min') {
        rangeInput.value = rangeInput.min
    }
    updateValue()
    rangeInput.addEventListener("input", updateValue)
}

updateRangeValue("max-price", "price-range-value", '<i class="bi bi-coin text-warning"></i>', 'max')
updateRangeValue("driver-min-rating", "driver-rating-range-value", '<i class="bi bi-star-fill text-warning"></i>', 'min')

function applyFilters() {
    const maxPrice = parseFloat(document.getElementById("max-price").value)
    const minRating = parseFloat(document.getElementById("driver-min-rating").value)
    const ecoOnly = document.getElementById("eco-trip-only").checked

    const items = document.querySelectorAll(".carpooling-item")

    items.forEach(item => {
        const price = parseFloat(item.dataset.price)
        const rating = parseFloat(item.dataset.rating)
        const eco = item.dataset.eco === '1'

        const visible =
            price <= maxPrice &&
            rating >= minRating &&
            (!ecoOnly || eco)

        item.style.display = visible ? 'block' : 'none'
    })

    // Vérifie s’il reste des trajets visibles après filtrage
    const visibleItems = Array.from(items).filter(item => item.style.display !== 'none')
    const noResultsMessage = document.getElementById('no-results-message')

    if (noResultsMessage) {
        if (visibleItems.length === 0) {
            noResultsMessage.classList.remove('d-none')
        } else {
            noResultsMessage.classList.add('d-none')
        }
    }
}

// Applique les filtres quand l’utilisateur change une valeur
["max-price", "driver-min-rating", "eco-trip-only"].forEach(id => {
    const el = document.getElementById(id)
    if (el) {
        el.addEventListener("input", applyFilters)
        el.addEventListener("change", applyFilters)
    }
})

function moveFilters() {
    const wrapper = document.getElementById("filters-form-wrapper")
    const sidebar = document.getElementById("filters-sidebar-container")
    const modal = document.getElementById("filters-modal-container")

    if (!wrapper || !sidebar || !modal) return

    if (window.innerWidth >= 992) {
        sidebar.innerHTML = ""
        sidebar.appendChild(wrapper)
        wrapper.classList.remove("d-none")
    } else {
        modal.innerHTML = ""
        modal.appendChild(wrapper)
        wrapper.classList.remove("d-none")
    }
}

window.addEventListener("resize", moveFilters)
window.addEventListener("DOMContentLoaded", moveFilters)