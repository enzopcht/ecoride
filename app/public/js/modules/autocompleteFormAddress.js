document.addEventListener('DOMContentLoaded', () => {
    function initFlatpickr() {
        const dateInput = document.getElementById('departure_date')
        const timeInput = document.getElementById('departure_time')

        if (!dateInput || !timeInput) return

        flatpickr(dateInput, {
            dateFormat: "d/m/Y",
            minDate: new Date(),
            locale: "fr",
            onChange: updateHiddenDepartureTime
        })

        flatpickr(timeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            locale: "fr",
            onChange: updateHiddenDepartureTime
        })
    }

    const departureFullAddressInput = document.getElementById('add_ride_departureAddress')
    const arrivalFullAddressInput = document.getElementById('add_ride_arrivalAddress')
    const departurePostCodeInput = document.getElementById('add_ride_departurePostCode')
    const arrivalPostCodeInput = document.getElementById('add_ride_arrivalPostCode')
    const departureCityInput = document.getElementById('add_ride_departureCity')
    const arrivalCityInput = document.getElementById('add_ride_arrivalCity')

    const departureTimeInput = document.getElementById('add_ride_departureTime')
    const arrivalTimeInput = document.getElementById('add_ride_arrivalTime')

    const vehicleInput = document.getElementById('add_ride_vehicle')
    const availableSeatsInput = document.getElementById('add_ride_seatsAvailable')
    const priceInput = document.getElementById('add_ride_price')

    const departureSuggestions = document.getElementById('departure_suggestions')
    const arrivalSuggestions = document.getElementById('arrival_suggestions')

    const arrivalTimeDiv = document.getElementById('arrivalTimeDiv')
    const validateButton = document.getElementById('validate_button')

    const APIKEY = document.getElementById('map-script').dataset.apiKey

    const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

    let departureCoords = null
    let arrivalCoords = null
    let calculatedArrival = null

    arrivalTimeInput.readOnly = true



    function appendAlert(message, type) {
        const wrapper = document.createElement('div')
        wrapper.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible" role="alert">`,
            `   <div>${message}</div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('')

        alertPlaceholder.append(wrapper)
    }


    function checkValidity() {
        if (
            departureFullAddressInput.classList.contains('is-valid') &&
            arrivalFullAddressInput.classList.contains('is-valid') &&
            document.getElementById('departure_date').classList.contains('is-valid') &&
            document.getElementById('departure_time').classList.contains('is-valid') &&
            vehicleInput.classList.contains('is-valid') &&
            availableSeatsInput.classList.contains('is-valid') &&
            priceInput.classList.contains('is-valid')
        ) {
            validateButton.disabled = false
        } else {
            validateButton.disabled = true
        }
    }
    checkValidity()


    async function computeArrivalTime() {
        if (!departureCoords || !arrivalCoords || !departureTimeInput.value) {
            arrivalTimeInput.value = ''
            return
        }
        const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car', {
            method: 'POST',
            headers: {
                'Authorization': APIKEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                coordinates: [departureCoords, arrivalCoords]
            })
        })
        const data = await response.json()
        const durationInSeconds = data.routes[0].summary.duration
        const departure = new Date(departureTimeInput.value)
        const arrival = new Date(departure.getTime() + durationInSeconds * 1000)
        calculatedArrival = arrival
        arrivalTimeInput.value = arrival.toLocaleString('sv-SE').replace(' ', 'T').slice(0, 16)
        arrivalTimeDiv.classList.remove('d-none')
    }

    function checkDepartureDate() {
        const date = new Date(departureTimeInput.value)
        const today = new Date()
        const dateInput = document.getElementById('departure_date')
        const timeInput = document.getElementById('departure_time')
        if (date < today) {
            dateInput.classList.remove('is-valid')
            timeInput.classList.add('is-invalid')
            timeInput.classList.remove('is-valid')
            arrivalTimeInput.value = ''
            arrivalTimeDiv.classList.add('d-none')
        } else {
            dateInput.classList.remove('is-invalid')
            dateInput.classList.add('is-valid')
            timeInput.classList.remove('is-invalid')
            timeInput.classList.add('is-valid')
            computeArrivalTime()
        }
    }

    function updateHiddenDepartureTime() {
        const dateInput = document.getElementById('departure_date')
        const timeInput = document.getElementById('departure_time')

        if (dateInput.value && timeInput.value) {
            const [day, month, year] = dateInput.value.split('/')
            const fullDateTime = `${year}-${month}-${day}T${timeInput.value}:00`
            departureTimeInput.value = fullDateTime
            checkDepartureDate()
        }
    }

    let debounceTimeout
    function setupAddressAutocomplete(input, suggestionBox, cityInput, postcodeInput) {
        input.dataset.valid = "false"
        input.addEventListener('input', async (e) => {
            const query = e.target.value
            suggestionBox.innerHTML = ''
            input.dataset.valid = "false"
            input.classList.remove('is-valid')
            input.classList.remove('is-invalid')

            clearTimeout(debounceTimeout)

            debounceTimeout = setTimeout(async () => {
                if (query.length > 3) {
                    try {
                        const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5&type=housenumber`)
                        suggestionBox.innerHTML = ''
                        const data = await response.json()

                        if (!data.features || !Array.isArray(data.features)) {
                            appendAlert('Une erreur est survenue lors de la récupération des adresses.', 'danger')
                            return
                        }

                        data.features.forEach(feature => {
                            const li = document.createElement('li')
                            li.textContent = feature.properties.label
                            li.classList.add('list-group-item')
                            li.addEventListener('click', () => {
                                input.value = feature.properties.label
                                input.dataset.valid = "true"
                                input.classList.remove('is-invalid')
                                input.classList.add('is-valid')
                                cityInput.value = feature.properties.city
                                postcodeInput.value = feature.properties.postcode
                                suggestionBox.innerHTML = ''

                                fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(feature.properties.label)}&limit=1`)
                                    .then(res => res.json())
                                    .then(data => {
                                        const coords = data.features[0].geometry.coordinates
                                        if (input.id.includes('departure')) {
                                            departureCoords = coords
                                            computeArrivalTime()
                                        } else {
                                            arrivalCoords = coords
                                            computeArrivalTime()
                                        }
                                    })

                                checkValidity()
                            })
                            suggestionBox.appendChild(li)
                        })
                    } catch (error) {
                    }
                }

                arrivalTimeInput.value = ''
                calculatedArrival = null

            }, 300)
        })
    }
    setupAddressAutocomplete(departureFullAddressInput, departureSuggestions, departureCityInput, departurePostCodeInput)
    setupAddressAutocomplete(arrivalFullAddressInput, arrivalSuggestions, arrivalCityInput, arrivalPostCodeInput)


    function checkVehicle() {
        if (!vehicleInput.value) {
            vehicleInput.classList.add('is-invalid')
            vehicleInput.classList.remove('is-valid')
        } else {
            vehicleInput.classList.add('is-valid')
            vehicleInput.classList.remove('is-invalid')
        }
        checkValidity()
    }
    checkVehicle()

    function checkSeats() {
        if (!availableSeatsInput.value || availableSeatsInput.value < 1 || availableSeatsInput.value > 8) {
            availableSeatsInput.classList.add('is-invalid')
            availableSeatsInput.classList.remove('is-valid')
        } else {
            availableSeatsInput.classList.add('is-valid')
            availableSeatsInput.classList.remove('is-invalid')
        }
        checkValidity()
    }


    function checkPrice() {
        if (!priceInput.value || priceInput.value < 2) {
            priceInput.classList.add('is-invalid')
            priceInput.classList.remove('is-valid')
        } else if (priceInput.value == 2) {
            priceInput.classList.add('is-valid')
            priceInput.classList.remove('is-invalid')

            document.querySelectorAll('.alert-warning').forEach(alert => {
                if (alert.textContent.includes("avec la commission")) {
                    alert.remove()
                }
            })

            appendAlert('Actuellement votre trajet est au prix de 2 jetons, avec la commission vous ne recevrez rien. Ajustez le si nécessaire.', 'warning')
        } else {
            priceInput.classList.add('is-valid')
            priceInput.classList.remove('is-invalid')
        }
        checkValidity()
    }

    vehicleInput.addEventListener('change', checkVehicle)
    availableSeatsInput.addEventListener('input', checkSeats)
    priceInput.addEventListener('input', checkPrice)

    initFlatpickr()

    const addDelayButton = document.getElementById('add_delay_button')
    if (addDelayButton) {
        addDelayButton.addEventListener('click', () => {
            adjustArrivalTime(15)
        })
    }
    const subDelayButton = document.getElementById('sub_delay_button')
    if (subDelayButton) {
        subDelayButton.addEventListener('click', () => {
            adjustArrivalTime(-15)
        })
    }

    function adjustArrivalTime(minutes) {
        if (!arrivalTimeInput.value) return

        const current = new Date(arrivalTimeInput.value)
        const newDate = new Date(current.getTime() + minutes * 60000)
        if (calculatedArrival && newDate < calculatedArrival) {
            arrivalTimeInput.value = calculatedArrival.toLocaleString('sv-SE').replace(' ', 'T').slice(0, 16)
            appendAlert('Vous ne pouvez pas fournir une heure inférieur à celle calculée par EcoRide', 'danger')
            return
        }

        arrivalTimeInput.value = newDate.toLocaleString('sv-SE').replace(' ', 'T').slice(0, 16)
    }
})
