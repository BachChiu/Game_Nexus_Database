document.getElementById('registerForm').addEventListener('input', function () {
    validateForm();
});

function validateForm() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const submit = document.getElementById('submit');
    const errorElement = document.getElementById('passwordError');

    let isValid = true;

    if (!username || !password || !confirmPassword) {
        isValid = false;
    }

    if (password !== confirmPassword) {
        errorElement.textContent = 'Passwords do not match';
        errorElement.classList.remove('success');
        errorElement.classList.add('error');
        isValid = false;
    } else {
        errorElement.textContent = 'Passwords match';
        errorElement.classList.remove('error');
        errorElement.classList.add('success');
    }

    if (isValid) {
        submit.classList.add('enabled');
        submit.disabled = false;
    } else {
        submit.classList.remove('enabled');
        submit.disabled = true;
    }
}