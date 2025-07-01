document.addEventListener('DOMContentLoaded', () => {
	// Select form with name containing 'vehicle' or exactly 'become_driver'
	const form =
		document.querySelector('form[name*="vehicle"]') ||
		document.querySelector('form[name="become_driver"]');
	if (!form) return;

	// Support nested names like vehicle[plate] or becomeDriverForm[vehicle][plate]
	const plate = form.querySelector('input[name$="[plate]"]');
	const errorPlate = document.getElementById('errorPlate');

	const firstRegistrationDate = form.querySelector('input[name$="[firstRegistrationDate]"]');
	const errorFirstRegistrationDate = document.getElementById('errorFirstRegistrationDate');

	const brandSelect = form.querySelector('select[name$="[brand]"]');
	const errorBrandSelect = document.getElementById('errorBrand');

	const modelSelect = form.querySelector('select[name$="[model]"]');
	const modelWrapper = document.getElementById('model_wrapper');

	const color = form.querySelector('select[name$="[color]"]');
	const errorColor = document.getElementById('errorColor');

	const validateButton = document.getElementById('validate_button');

	if (!modelWrapper.classList.contains('d-none')) {
		modelWrapper.classList.add('d-none')
	}

	function checkFormValidity() {
		if (
			plate.classList.contains('is-valid') &&
			firstRegistrationDate.classList.contains('is-valid') &&
			brandSelect.classList.contains('is-valid') &&
			modelSelect.classList.contains('is-valid') &&
			color.classList.contains('is-valid')
		) {
			validateButton.disabled = false
		} else {
			validateButton.disabled = true
		}
	}

	function checkPlate() {
		const regex = /^[A-HJ-NP-TV-Z]{2}[\s-]?[0-9]{3}[\s-]?[A-HJ-NP-TV-Z]{2}$/;
		if (plate.value) {
			if (regex.test(plate.value)) {
				plate.classList.remove('is-invalid')
				plate.classList.add('is-valid')
				if (errorPlate) {
					errorPlate.classList.add('d-none')
				}
			} else {
				plate.classList.remove('is-valid')
				plate.classList.add('is-invalid')
			}
	
			checkFormValidity()
		}
	}

	
	function checkFirstRegistrationDate() {
		if (!firstRegistrationDate.value) {
			firstRegistrationDate.classList.remove('is-valid')
		} else {
			let selectedDate = new Date(firstRegistrationDate.value).toISOString().split('T')[0];
			const today = new Date().toISOString().split('T')[0];
			if (selectedDate > today) {
				firstRegistrationDate.classList.remove('is-valid')
				firstRegistrationDate.classList.add('is-invalid')
			} else {
				firstRegistrationDate.classList.remove('is-invalid')
				firstRegistrationDate.classList.add('is-valid')
				if (errorFirstRegistrationDate) {
					errorFirstRegistrationDate.classList.add('d-none')
				}
			}
			checkFormValidity()
		}
	}
	
	function checkBrand() {
		const brandId = brandSelect.value
		if (brandSelect.value) {
			if (!brandId) {
				modelWrapper.classList.add('d-none')
				modelSelect.innerHTML = '<option value="">Sélectionnez un modèle</option>'
				brandSelect.classList.remove('is-valid')
				brandSelect.classList.add('is-invalid')
			} else {
				modelWrapper.classList.remove('d-none')
				modelSelect.innerHTML = '<option value="">Chargement...</option>'
				brandSelect.classList.remove('is-invalid')
				brandSelect.classList.add('is-valid')
				fetch(`/get-models/${brandId}`)
					.then(response => response.json())
					.then(models => {
						modelSelect.innerHTML = '<option value="">Sélectionnez un modèle</option>'
	
						models.forEach(model => {
							const option = document.createElement('option')
							option.value = model.id
							option.textContent = model.label
							modelSelect.appendChild(option)
						})
	
					})
					.catch(error => {
						modelSelect.innerHTML = '<option value="">Erreur de chargement</option>'
					})
			}
			checkFormValidity()
		}
	}

	function checkModel() {
		if (!modelSelect.value) {
			modelSelect.classList.remove('is-valid')
			modelSelect.classList.add('is-invalid')
		} else {
			modelSelect.classList.remove('is-invalid')
			modelSelect.classList.add('is-valid')
		}

		checkFormValidity()
	}

	function checkColor() {
			if (!color.value) {
				color.classList.remove('is-valid')
				color.classList.add('is-invalid')
			} else {
				color.classList.remove('is-invalid')
				color.classList.add('is-valid')
			}
	
			checkFormValidity()
	}

	if (errorPlate) {
		checkColor()
		checkBrand()
		checkFirstRegistrationDate()
	} 
	checkFormValidity()


	let previousPlateValue = ''
	plate.addEventListener('input', () => {
		let value = plate.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()

		if (value.length > previousPlateValue.replace(/[^a-zA-Z0-9]/g, '').length) {
			if (value.length >= 2) {
				value = value.slice(0, 2) + '-' + value.slice(2)
			}
			if (value.length >= 6) {
				value = value.slice(0, 6) + '-' + value.slice(6)
			}
			value = value.slice(0, 9)
		}

		plate.value = value
		previousPlateValue = value
	})

	plate.addEventListener('input', checkPlate)
	firstRegistrationDate.addEventListener('change', checkFirstRegistrationDate)
	brandSelect.addEventListener('change', checkBrand)
	modelSelect.addEventListener('change', checkModel)
	color.addEventListener('change', checkColor)
})