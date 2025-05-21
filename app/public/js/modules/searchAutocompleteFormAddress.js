document.addEventListener('DOMContentLoaded', () => {
    const departureInput = document.getElementById('search_ride_departure')
    const arrivalInput = document.getElementById('search_ride_arrival')

    const departureTimeInput = document.getElementById('search_ride_date')

    const departureSuggestions = document.getElementById('departure_suggestions')
    const arrivalSuggestions = document.getElementById('arrival_suggestions')

    const validateButton = document.getElementById('search_ride_rechercher')

    departureInput.value = ""
    arrivalInput.value = ""
    departureTimeInput.value = ""


    function checkValidity() {
        if (
            departureInput.classList.contains('is-valid') &&
            arrivalInput.classList.contains('is-valid') &&
            departureTimeInput.classList.contains('is-valid')
        ) {
            validateButton.disabled = false
        } else {
            validateButton.disabled = true
        }
    }
    checkValidity()

    function setupAddressAutocomplete(input, suggestionBox) {
        input.dataset.valid = "false"
        input.addEventListener('input', async (e) => {
            const query = e.target.value
            suggestionBox.innerHTML = ''
            input.dataset.valid = "false"
            input.classList.remove('is-valid')
            if (query.length > 3) {
                try {
                    const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5&type=municipality`)
                    const data = await response.json()
                    suggestionBox.innerHTML = ''

                    data.features.forEach(feature => {
                        if (feature.properties.name.toLowerCase().includes('arrondissement')) { return }
                        const li = document.createElement('li')
                        const contextParts = feature.properties.context.split(',')
                        const department = contextParts[1]?.trim() || ''
                        li.textContent = `${feature.properties.label} (${department})`
                        li.classList.add('list-group-item')
                        li.addEventListener('click', () => {
                            input.value = `${feature.properties.label}`
                            input.dataset.valid = "true"
                            input.classList.remove('is-invalid')
                            input.classList.add('is-valid')
                            suggestionBox.innerHTML = ''
                        })
                        suggestionBox.appendChild(li)
                    })

                } catch (error) {
                }
            }
        })
        input.addEventListener('blur', () => {
            setTimeout(() => {
                const firstSuggestion = suggestionBox.querySelector('li')
                if (firstSuggestion) {
                    input.value = firstSuggestion.textContent.split('(')[0].trim()
                    input.dataset.valid = "true"
                    input.classList.remove('is-invalid')
                    input.classList.add('is-valid')
                }
                suggestionBox.innerHTML = ''
                checkValidity()
            }, 150)
        })
    }

    departureTimeInput.addEventListener('change', () => {
        if (departureTimeInput.value) {
            departureTimeInput.classList.add('is-valid')
            departureTimeInput.classList.remove('is-invalid')
        } else {
            departureTimeInput.classList.add('is-invalid')
            departureTimeInput.classList.remove('is-valid')
        }
        checkValidity()
    })


    setupAddressAutocomplete(departureInput, departureSuggestions)
    setupAddressAutocomplete(arrivalInput, arrivalSuggestions)
})