document.querySelectorAll('.star').forEach(star => {
    const ratingInput = document.getElementById('review_rating')
    const commentInput = document.getElementById('review_comment')
    const validateButton = document.getElementById('validateButton')

    function checkFormValidity() {
		if (
			ratingInput.classList.contains('is-valid') &&
			commentInput.classList.contains('is-valid') 
		) {
			validateButton.disabled = false
		} else {
			validateButton.disabled = true
		}
	}
    function checkRating() {
        const value = parseInt(ratingInput.value)
        if (value >= 1 && value <= 5) {
            ratingInput.classList.add('is-valid')
            ratingInput.classList.remove('is-invalid')
        } else {
            ratingInput.classList.remove('is-valid')
            ratingInput.classList.add('is-invalid')
        }
    }
    checkFormValidity()

    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-value')
        document.querySelector('#review_rating').value = rating
        document.querySelectorAll('.star').forEach(s => {
            s.classList.remove('bi-star-fill')
            s.classList.add('bi-star')
        })
        for (let i = 0; i < rating; i++) {
            document.querySelectorAll('.star')[i].classList.remove('bi-star')
            document.querySelectorAll('.star')[i].classList.add('bi-star-fill')
        }
        checkRating()
        checkFormValidity()
    })

    commentInput.addEventListener('input', () => {
        if (commentInput.value) {
            commentInput.classList.add('is-valid')
            commentInput.classList.remove('is-invalid')
        } else {
            commentInput.classList.remove('is-valid')
            commentInput.classList.add('is-invalid')
        }
        checkFormValidity()
    })

})