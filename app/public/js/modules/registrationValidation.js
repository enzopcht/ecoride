document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('registration_plainPassword_first')
    const passwordConfirm = document.getElementById('registration_plainPassword_second')
    const email = document.getElementById('registration_email')
    const pseudo = document.getElementById('registration_pseudo')
    const validateButton = document.getElementById('validateButton')

    function testMail() {
        if (email.value.toLowerCase().match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        )) {
            email.classList.remove('is-invalid')
            email.classList.add('is-valid')
        } else {
            email.classList.remove('is-valid')
            email.classList.add('is-invalid')
        }
        checkFormValidity()
    }

    function testPseudo() {
        const errorPseudo = document.getElementById('errorPseudo')
        if (errorPseudo) {
            errorPseudo.classList.add('d-none')
        }
        if (pseudo.value.length < 3) {
            pseudo.classList.remove('is-valid')
            pseudo.classList.add('is-invalid')
        } else {
            pseudo.classList.remove('is-invalid')
            pseudo.classList.add('is-valid')
        }
        checkFormValidity()
    }

    if (email.value) {
        if (document.getElementById('errorMail')) {
            email.classList.remove('is-valid')
            email.classList.add('is-invalid')
            document.getElementById('mailFormat').classList.add('d-none')
        } else {
            testMail()
        }
    } 
    if (pseudo.value) {
        if (document.getElementById('errorPseudo')) {
            pseudo.classList.remove('is-valid')
            pseudo.classList.add('is-invalid')
            document.getElementById('pseudoFormat').classList.add('d-none')
        } else {
            testPseudo()
        }

    }

    function checkFormValidity() {
        if (
            email.classList.contains('is-valid') &&
            pseudo.classList.contains('is-valid') &&
            password.classList.contains('is-valid') &&
            passwordConfirm.classList.contains('is-valid')
        ) {
            validateButton.disabled = false
        } else {
            validateButton.disabled = true
        }
    }

    checkFormValidity()

    email.addEventListener('input', function () {
        const errorEmail = document.getElementById('errorMail')
        document.getElementById('mailFormat').classList.remove('d-none')
        if (errorEmail) {
            errorEmail.classList.add('d-none')
        }
        testMail()
    })

    pseudo.addEventListener('input', function () {
        document.getElementById('pseudoFormat').classList.remove('d-none')
        testPseudo()
    })

    password.addEventListener('input', function () {
        securePasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;"'<>,.?/~`\\|-]).{8,}$/
        if (securePasswordRegex.test(password.value)) {
            password.classList.remove('is-invalid')
            password.classList.add('is-valid')
        } else {
            password.classList.remove('is-valid')
            password.classList.add('is-invalid')
        }
        checkFormValidity()
        pswConfirmTest()
    })

    function pswConfirmTest() {
        if (passwordConfirm && password.value && passwordConfirm.value) {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.classList.remove('is-valid')
                passwordConfirm.classList.add('is-invalid')
            } else {
                passwordConfirm.classList.remove('is-invalid')
                passwordConfirm.classList.add('is-valid')
            }
        } else {
            passwordConfirm.classList.remove('is-valid')
            passwordConfirm.classList.add('is-invalid')
        }
        checkFormValidity()
    }

    passwordConfirm.addEventListener('input', pswConfirmTest)
})