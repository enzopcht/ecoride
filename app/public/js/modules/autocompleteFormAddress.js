document.addEventListener('DOMContentLoaded', () => {
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

const validateButton = document.getElementById('validate_button')

const APIKEY = '5b3ce3597851110001cf6248addfcadb019b4478a7be4c7384531caf'

let departureCoords = null
let arrivalCoords = null
let calculatedArrival = null
arrivalTimeInput.disabled = true



const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

const appendAlert = (message, type) => {
    const wrapper = document.createElement('div')
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('')

    alertPlaceholder.append(wrapper)
}
checkValidity()
checkVehicle()
function checkValidity() {
    if (
        departureFullAddressInput.classList.contains('is-valid') &&
        arrivalFullAddressInput.classList.contains('is-valid') &&
        departureTimeInput.classList.contains('is-valid') &&
        arrivalTimeInput.classList.contains('is-valid') &&
        vehicleInput.classList.contains('is-valid') &&
        availableSeatsInput.classList.contains('is-valid') &&
        priceInput.classList.contains('is-valid')
    ) {
        validateButton.disabled = false
    } else {
        validateButton.disabled = true
    }
}
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
    arrivalTimeInput.disabled = false
    arrivalTimeInput.classList.add('is-valid')
    arrivalTimeInput.classList.remove('is-invalid')
}


function setupAddressAutocomplete(input, suggestionBox, cityInput, postcodeInput) {
    input.dataset.valid = "false"
    input.addEventListener('input', async (e) => {
        const query = e.target.value
        suggestionBox.innerHTML = ''
        input.dataset.valid = "false"
        input.classList.remove('is-valid')
        if (query.length > 3) {
            suggestionBox.innerHTML = '<li class="list-group-item disabled">Chargement...</li>'
            try {
                const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5&type=housenumber`)
                const data = await response.json()
                suggestionBox.innerHTML = ''

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
                                } else {
                                    arrivalCoords = coords
                                }
                            })
                        computeArrivalTime()
                        checkValidity()
                    })
                    suggestionBox.appendChild(li)
                })

            } catch (error) {
                console.error("Erreur API : ", error)
            }
        }
        arrivalTimeInput.value = ''
        arrivalTimeInput.classList.remove('is-valid')
        arrivalTimeInput.classList.remove('is-invalid')
        calculatedArrival = null

        if (departureFullAddressInput.dataset.valid === 'true' && arrivalFullAddressInput.dataset.valid === 'true') {
            computeArrivalTime()
            checkValidity()
        }
    })
}

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

function checkSeats() {
    if (!availableSeatsInput.value | availableSeatsInput.value < 1 | availableSeatsInput.value > 8) {
        availableSeatsInput.classList.add('is-invalid')
        availableSeatsInput.classList.remove('is-valid')
    } else {
        availableSeatsInput.classList.add('is-valid')
        availableSeatsInput.classList.remove('is-invalid')
    }
    checkValidity()
}

function checkPrice() {
    if (!priceInput.value | priceInput.value < 2) {
        priceInput.classList.add('is-invalid')
        priceInput.classList.remove('is-valid')
    } else if (priceInput.value == 2) {
        priceInput.classList.add('is-valid')
        priceInput.classList.remove('is-invalid')
        appendAlert('Actuellement votre trajet est au prix de 2 jetons, avec la commission vous ne recevrez rien. Ajustez le si nécessaire.', 'warning')
    } else {
        priceInput.classList.add('is-valid')
        priceInput.classList.remove('is-invalid')
    }
    checkValidity()
}

departureTimeInput.addEventListener('change', () => {
    const today = new Date()
    if (departureTimeInput.value) {
        const departure = new Date(departureTimeInput.value)
        if (departure > today) {
            departureTimeInput.classList.add('is-valid')
            departureTimeInput.classList.remove('is-invalid')
            computeArrivalTime()
        } else {
            departureTimeInput.classList.add('is-invalid')
            departureTimeInput.classList.remove('is-valid')
            departureTimeInput.value = ''
            arrivalTimeInput.value = ''
            arrivalTimeInput.classList.add('is-invalid')
            arrivalTimeInput.classList.remove('is-valid')
        }
    } else {
        departureTimeInput.classList.add('is-invalid')
        departureTimeInput.classList.remove('is-valid')
        arrivalTimeInput.value = ''
        arrivalTimeInput.classList.add('is-invalid')
        arrivalTimeInput.classList.remove('is-valid')
    }
    checkValidity()
})

arrivalTimeInput.addEventListener('change', () => {
    if (!calculatedArrival) {
        return
    }

    const customArrival = new Date(arrivalTimeInput.value)
    if (!departureFullAddressInput.classList.contains('is-valid') | !arrivalFullAddressInput.classList.contains('is-valid') | !departureTimeInput.classList.contains('is-valid')) {
        arrivalTimeInput.value = ''
        arrivalTimeInput.classList.add('is-invalid')
        arrivalTimeInput.classList.remove('is-valid')
    }
    if (customArrival < calculatedArrival) {
        arrivalTimeInput.value = calculatedArrival.toLocaleString('sv-SE').replace(' ', 'T').slice(0, 16)
        appendAlert('Votre date d\'arrivée ne peut être inférieure à celle calculée par EcoRide.', 'danger')
        arrivalTimeInput.classList.add('is-valid')
    } else {
        arrivalTimeInput.classList.add('is-valid')
        arrivalTimeInput.classList.remove('is-invalid')
    }
    checkValidity()
})

vehicleInput.addEventListener('change', checkVehicle)

availableSeatsInput.addEventListener('input', checkSeats)

priceInput.addEventListener('input', checkPrice)


setupAddressAutocomplete(departureFullAddressInput, departureSuggestions, departureCityInput, departurePostCodeInput)
setupAddressAutocomplete(arrivalFullAddressInput, arrivalSuggestions, arrivalCityInput, arrivalPostCodeInput)
})